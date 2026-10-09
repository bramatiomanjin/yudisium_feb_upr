<?php

namespace App\Http\Controllers;

use App\Enums\PengajuanStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Mahasiswa;
use App\Models\PengajuanYudisium;
use App\Models\JenisDokumen;
use App\Models\PengajuanDokumen;
use App\Models\ValidasiField;
use App\Support\DocumentUploadValidator;
use App\Support\PengajuanCodeGenerator;
use App\Support\StudentYudisiumNotifier;

class PengajuanController extends Controller
{
    private const DOCUMENT_CODE_ALIASES = [
    'BEBAS_PERPUS_UNIVERSITAS' => 'BEBAS_PERPUS_UNIV',
    'SURAT_TUGAS_PEMBIMBING' => 'SURAT_TUGAS_DOSBING',
    'JURNAL_MANAJEMEN' => 'JURNAL_JMSO',
    'JURNAL_EKONOMI' => 'JURNAL_EP',
];

private function canonicalDocumentCode(string $code): string
{
    $code = strtoupper(trim($code));

    return self::DOCUMENT_CODE_ALIASES[$code] ?? $code;
}
    // 1. Menampilkan halaman form
    public function create()
    {
        try {
            $setting = \App\Models\Setting::where('key', 'is_pengajuan_open')->first();
            $isPengajuanOpen = $setting ? $setting->value === '1' : true;
        } catch (\Exception $e) {
            $isPengajuanOpen = true;
        }

        if (!$isPengajuanOpen) {
            return view('mahasiswa.pengajuan_closed');
        }

        $documents = JenisDokumen::where('is_active', 1)->get();

        $activeDocs = $documents
            ->map(fn (JenisDokumen $document) => $this->canonicalDocumentCode($document->kode))
            ->values()
            ->all();

        $requiredDocs = $documents
            ->filter(fn (JenisDokumen $document): bool => (bool) $document->wajib)
            ->map(fn (JenisDokumen $document) => $this->canonicalDocumentCode($document->kode))
            ->values()
            ->all();

        try {
            $imgSetting = \App\Models\Setting::where('key', 'pengumuman_image_path')->first();
            $pengumumanImage = $imgSetting ? $imgSetting->value : '';
        } catch (\Exception $e) {
            $pengumumanImage = '';
        }

        return view('index', compact('activeDocs', 'requiredDocs', 'pengumumanImage'));
    }

    // 2. Memproses pengiriman form
    public function store(
        Request $request,
        DocumentUploadValidator $documentUploadValidator,
        PengajuanCodeGenerator $codeGenerator,
        StudentYudisiumNotifier $notifier
    )
    {
        try {
            $setting = \App\Models\Setting::where('key', 'is_pengajuan_open')->first();
            if ($setting && $setting->value === '0') {
                return back()->with('error', 'Pendaftaran yudisium saat ini sedang ditutup.');
            }
        } catch (\Exception $e) {
            // Ignore if settings table doesn't exist
        }

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

        $dokumenPersyaratan = JenisDokumen::where('is_active', 1)
            ->get()
            ->filter(function (JenisDokumen $doc) use ($request): bool {
                return $doc->jurusan === null || $doc->jurusan === $request->jurusan;
            });

        foreach ($dokumenPersyaratan as $doc) {
            $inputName = strtolower($this->canonicalDocumentCode($doc->kode));

            $documentUploadValidator->validate(
                $request,
                $inputName,
                $doc,
                (bool) $doc->wajib
            );
        }

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

            // 2. Buat kode pengajuan melalui counter tahunan yang dikunci transaksi.
            $kodePengajuan = $codeGenerator->generate();

            // 3. Simpan pengajuan
            $pengajuan = PengajuanYudisium::create([
                'nim' => $mahasiswa->nim,
                'kode_pengajuan' => $kodePengajuan,
                'karya_tulis' => $request->karya_tulis,
                'judul_karya_tulis' => $request->judul_karya_tulis,
                'tanggal_ujian' => $request->tanggal_ujian,
                'nilai_angka' => $request->nilai_angka,
                'nilai_huruf' => $request->nilai_huruf,
                'status' => PengajuanStatus::MENUNGGU_VERIFIKASI->value,
                'submitted_at' => now(),
            ]);

            \App\Models\RiwayatStatus::create([
                'pengajuan_id' => $pengajuan->id,
                'status' => PengajuanStatus::MENUNGGU_VERIFIKASI->value,
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
                $inputName = strtolower(
    $this->canonicalDocumentCode($doc->kode)
);

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
                        'yudisium/' .
                            $mahasiswa->nim,
                        $namaFileStorage,
                        'local'
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
            $notifier->submissionReceived($pengajuan);

            return response()->json([
                'success' => true,
                'code' => $kodePengajuan,
                'nim' => $mahasiswa->nim,
                'message' => 'Pengajuan berhasil dikirim.'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error('Gagal memproses pengajuan yudisium.', [
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Pengajuan belum dapat diproses. Silakan coba lagi.'
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
