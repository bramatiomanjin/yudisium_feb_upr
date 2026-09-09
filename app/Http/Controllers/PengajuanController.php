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
        // --- A. VALIDASI INPUT AWAL ---
        if ($request->has('nilai_angka')) {
            $request->merge([
                'nilai_angka' => str_replace(',', '.', $request->nilai_angka)
            ]);
        }
        
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
            // (Validasi file akan kita tambahkan nanti menyesuaikan name dari frontend)
        ]);

        // --- B. CEK DUPLIKASI ---
        $cekPengajuan = PengajuanYudisium::where('nim', $request->nim)->first();
        if ($cekPengajuan) {
            return response('Error: NIM ini sudah memiliki pengajuan aktif!', 400);
        }

        // --- C. PROSES SIMPAN KE DATABASE (Menggunakan Transaction) ---
        try {
            DB::beginTransaction();

            // 1. Simpan atau Update Data Mahasiswa
            $mahasiswa = Mahasiswa::firstOrCreate(
                ['nim' => $request->nim],
                [
                    'nama_lengkap' => $request->nama_lengkap,
                    'email' => $request->email,
                    'no_whatsapp' => $request->no_whatsapp,
                    'tahun_angkatan' => $request->tahun_angkatan,
                    'jalur_masuk' => $request->jalur_masuk,
                    'jurusan' => $request->jurusan,
                ]
            );

            // 2. Buat Kode Pengajuan Unik (Contoh: YDS-2026-0001)
            $tahun = date('Y');
            $urutan = PengajuanYudisium::count() + 1;
            $kodePengajuan = 'YDS-' . $tahun . '-' . str_pad($urutan, 4, '0', STR_PAD_LEFT);

            // 3. Simpan Data Pengajuan Yudisium
            $pengajuan = PengajuanYudisium::create([
                'kode_pengajuan' => $kodePengajuan,
                'nim' => $mahasiswa->nim,
                'karya_tulis' => $request->karya_tulis,
                'judul_karya_tulis' => $request->judul_karya_tulis,
                'tanggal_ujian' => $request->tanggal_ujian,
                'nilai_angka' => $request->nilai_angka,
                'nilai_huruf' => $request->nilai_huruf,
                'status' => 'DIAJUKAN',
                'submitted_at' => now(),
            ]);

            // 4. Siapkan Data Validasi Field (Agar Admin bisa acc/revisi per field)
            $fields = [
                'nama_lengkap', 'email', 'no_whatsapp', 'tahun_angkatan', 
                'jalur_masuk', 'jurusan', 'karya_tulis', 'judul_karya_tulis', 
                'tanggal_ujian', 'nilai_angka', 'nilai_huruf'
            ];

            foreach ($fields as $field) {
                ValidasiField::create([
                    'pengajuan_id' => $pengajuan->id,
                    'field_key' => $field,
                    'status_validasi' => 'PENDING'
                ]);
            }

            // 5. PROSES UPLOAD DOKUMEN
            // Kita ambil daftar master dokumen dari database
            $dokumenPersyaratan = JenisDokumen::all();
            
            foreach ($dokumenPersyaratan as $doc) {
                // Nama input dari frontend kita sepakati formatnya: file_KODE_DOKUMEN
                $inputName = 'file_' . $doc->kode; 
                
                if ($request->hasFile($inputName)) {
                    $file = $request->file($inputName);
                    
                    // Kumpulkan informasi file
                    $namaFileAsli = $file->getClientOriginalName();
                    $ukuranFile = $file->getSize();
                    $mimeType = $file->getMimeType();
                    
                    // Generate nama file unik agar tidak tertimpa
                    $namaFileStorage = time() . '_' . $doc->kode . '.' . $file->extension();
                    
                    // Simpan file ke folder storage/app/private/yudisium/{NIM}/
                    // Ini folder aman yang tidak bisa diakses langsung lewat URL publik
                    $path = $file->storeAs('private/yudisium/' . $mahasiswa->nim, $namaFileStorage);
                    
                    // Catat riwayat file tersebut ke database pengajuan_dokumen
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
            }

            DB::commit(); // Simpan permanen ke database

            // Redirect ke halaman sukses
            return response()->json([
                'success' => true,
                'code' => $kodePengajuan,
                'nim' => $mahasiswa->nim,
                'message' => 'Pengajuan berhasil dikirim.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua simpanan jika terjadi error
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan Sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    // 3. Menampilkan halaman sukses
    public function success()
    {
        return "Pengajuan Berhasil! Kode Pengajuan Anda: " . session('kode');
    }
}