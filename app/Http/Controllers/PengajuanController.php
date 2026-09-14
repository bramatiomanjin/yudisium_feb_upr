<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Mahasiswa;
use App\Models\PengajuanYudisium;
use App\Models\JenisDokumen;
use App\Models\PengajuanDokumen;
use App\Models\ValidasiField;

class PengajuanController extends Controller
{
    // 1. Menampilkan halaman form
    public function create()
    {
        return view('index');
    }

    // 2. Memproses pengiriman form
    public function store(Request $request)
    {
        // --- A. NORMALISASI NILAI ANGKA ---
        if ($request->has('nilai_angka')) {
            $request->merge([
                'nilai_angka' => str_replace(',', '.', $request->nilai_angka)
            ]);
        }

        // --- B. VALIDASI INPUT UTAMA ---
        $request->validate([
            'nim' => 'required|string|max:20',
            'nama_lengkap' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'no_whatsapp' => 'required|string|max:20',
            'tahun_angkatan' => 'required|numeric',
            'jalur_masuk' => 'required|in:RPL,REGULER',
            'jurusan' => 'required|in:EKONOMI PEMBANGUNAN,MANAJEMEN,AKUNTANSI',
            'karya_tulis' => 'required|in:SKRIPSI,ARTIKEL',
            'judul_karya_tulis' => 'required|string',
            'tanggal_ujian' => 'required|date',
            'nilai_angka' => 'required|numeric',
            'nilai_huruf' => 'required|in:A,A-,A/B,B+,B,B-',
        ]);

        // --- C. CEK DUPLIKASI NIM ---
        $cekPengajuan = PengajuanYudisium::where(
            'nim',
            $request->nim
        )->first();

        if ($cekPengajuan) {
            return response()->json([
                'success' => false,
                'message' => 'NIM ini sudah memiliki pengajuan aktif.'
            ], 400);
        }

        // --- D. PROSES SIMPAN ---
        try {
            DB::beginTransaction();

            // 1. Simpan data mahasiswa
            $mahasiswa = Mahasiswa::firstOrCreate(
                [
                    'nim' => $request->nim
                ],
                [
                    'nama_lengkap' => $request->nama_lengkap,
                    'email' => $request->email,
                    'no_whatsapp' => $request->no_whatsapp,
                    'tahun_angkatan' => $request->tahun_angkatan,
                    'jalur_masuk' => $request->jalur_masuk,
                    'jurusan' => $request->jurusan,
                ]
            );

            // 2. Buat kode pengajuan
            $tahun = date('Y');

            $urutan =
                PengajuanYudisium::count() + 1;

            $kodePengajuan =
                'YDS-' .
                $tahun .
                '-' .
                str_pad(
                    $urutan,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

            // 3. Simpan pengajuan
            $pengajuan = PengajuanYudisium::create([
                'nim' => $mahasiswa->nim,
                'kode_pengajuan' => $kodePengajuan,
                'karya_tulis' => $request->karya_tulis,
                'judul_karya_tulis' => $request->judul_karya_tulis,
                'tanggal_ujian' => $request->tanggal_ujian,
                'nilai_angka' => $request->nilai_angka,
                'nilai_huruf' => $request->nilai_huruf,
                'status' => 'MENUNGGU_VERIFIKASI',
                'submitted_at' => now(),
            ]);

            \App\Models\RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'status' => 'MENUNGGU_VERIFIKASI',
                'catatan' => 'Pendaftaran pengajuan baru',
                'changed_by' => null // Mahasiswa (no auth)
            ]);

            // 4. Buat data validasi field
            $fields = [
                'nama_lengkap',
                'email',
                'no_whatsapp',
                'tahun_angkatan',
                'jalur_masuk',
                'jurusan',
                'karya_tulis',
                'judul_karya_tulis',
                'tanggal_ujian',
                'nilai_angka',
                'nilai_huruf'
            ];

            foreach ($fields as $field) {
                ValidasiField::create([
                    'pengajuan_id' => $pengajuan->id,
                    'field_key' => $field,
                    'status_validasi' => 'PENDING'
                ]);
            }

            // 5. Ambil master dokumen
            $dokumenPersyaratan =
                JenisDokumen::all();

            // 6. Proses upload dokumen
            foreach ($dokumenPersyaratan as $doc) {

                /*
                 * PENTING:
                 * name input frontend SAMA dengan kode dokumen (huruf kecil).
                 *
                 * Contoh:
                 * form_yudisium
                 * foto_3x4
                 * ijazah_slta
                 *
                 * Jadi TIDAK memakai prefix "file_".
                 */
                $inputName = strtolower($doc->kode);

                /*
                 * Dokumen khusus jurusan hanya diproses
                 * apabila sesuai dengan jurusan mahasiswa.
                 */
                if (
                    $doc->jurusan !== null &&
                    $doc->jurusan !== $request->jurusan
                ) {
                    continue;
                }

                /*
                 * Kalau file tidak dikirim, lewati.
                 * Untuk saat ini kita mengikuti validasi frontend.
                 */
                if (!$request->hasFile($inputName)) {
                    continue;
                }

                $file =
                    $request->file($inputName);

                // Informasi file asli
                $namaFileAsli =
                    $file->getClientOriginalName();

                $ukuranFile =
                    $file->getSize();

                $mimeType =
                    $file->getMimeType();

                // Extension file
                $extension =
                    strtolower(
                        $file->getClientOriginalExtension()
                    );

                /*
                 * Generate nama unik.
                 * Tambahkan pengajuan ID agar lebih aman
                 * dari bentrok nama file.
                 */
                $namaFileStorage =
                    $pengajuan->id .
                    '_' .
                    time() .
                    '_' .
                    $doc->kode .
                    '.' .
                    $extension;

                /*
                 * Simpan ke:
                 * storage/app/private/yudisium/{NIM}/
                 */
                $path =
                    $file->storeAs(
                        'private/yudisium/' .
                            $mahasiswa->nim,
                        $namaFileStorage
                    );

                /*
                 * Simpan informasi dokumen ke database.
                 */
                PengajuanDokumen::create([
                    'pengajuan_id' => $pengajuan->id,
                    'jenis_dokumen_id' => $doc->id,
                    'nama_file_asli' => $namaFileAsli,
                    'nama_file_storage' => $namaFileStorage,
                    'file_path' => $path,
                    'mime_type' => $mimeType,
                    'ukuran_file' => $ukuranFile,
                    'status_validasi' => 'PENDING'
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'code' => $kodePengajuan,
                'nim' => $mahasiswa->nim,
                'message' => 'Pengajuan berhasil dikirim.'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' =>
                    'Terjadi Kesalahan Sistem: ' .
                    $e->getMessage()
            ], 500);
        }
    }

    // 3. Menampilkan halaman sukses
    public function success()
    {
        return
            "Pengajuan Berhasil! Kode Pengajuan Anda: " .
            session('kode');
    }
}