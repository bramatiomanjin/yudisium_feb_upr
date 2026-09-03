<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\JenisDokumen;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Membuat Akun Super Admin Dummy
        User::create([
            'name' => 'Super Admin Akademik',
            'email' => 'superadmin@feb.upr.ac.id', // Ini email dummy untuk login nanti
            'password' => Hash::make('password123'), // Password dummy: password123
            'role' => 'SUPER_ADMIN',
        ]);

        // 2. Memasukkan Master Jenis Dokumen
        $dokumen = [
            ['kode' => 'FORM_YUDISIUM', 'nama_dokumen' => 'Formulir Pendaftaran Yudisium', 'jurusan' => null, 'wajib' => true, 'max_size_mb' => 1, 'allowed_extensions' => 'pdf'],
            ['kode' => 'FOTO_3X4', 'nama_dokumen' => 'Foto 3x4 Berwarna', 'jurusan' => null, 'wajib' => true, 'max_size_mb' => 1, 'allowed_extensions' => 'pdf'],
            ['kode' => 'IJAZAH_SLTA', 'nama_dokumen' => 'Ijazah SLTA', 'jurusan' => null, 'wajib' => true, 'max_size_mb' => 1, 'allowed_extensions' => 'pdf'],
            ['kode' => 'BERITA_ACARA_UJIAN', 'nama_dokumen' => 'Berita Acara Ujian Skripsi/Artikel yang sudah ditandatangani WD I dan dicap', 'jurusan' => null, 'wajib' => true, 'max_size_mb' => 1, 'allowed_extensions' => 'pdf'],
            ['kode' => 'REKAP_NILAI', 'nama_dokumen' => 'Rekapitulasi Nilai Ujian Skripsi/Artikel yang disahkan Wakil Dekan I', 'jurusan' => null, 'wajib' => true, 'max_size_mb' => 1, 'allowed_extensions' => 'pdf'],
            ['kode' => 'BLANKO_REVISI', 'nama_dokumen' => 'Blanko Revisi yang sudah ditandatangani dosen', 'jurusan' => null, 'wajib' => true, 'max_size_mb' => 1, 'allowed_extensions' => 'pdf'],
            ['kode' => 'TANDA_TERIMA', 'nama_dokumen' => 'Tanda Terima Skripsi/Artikel', 'jurusan' => null, 'wajib' => true, 'max_size_mb' => 10, 'allowed_extensions' => 'pdf,doc,docx'],
            ['kode' => 'SURAT_PERNYATAAN_IJAZAH', 'nama_dokumen' => 'Surat Pernyataan untuk Proses Penulisan Ijazah', 'jurusan' => null, 'wajib' => true, 'max_size_mb' => 1, 'allowed_extensions' => 'pdf'],
            ['kode' => 'BEBAS_PERPUS_UNIV', 'nama_dokumen' => 'Surat Bebas Pinjam Perpustakaan Universitas Asli', 'jurusan' => null, 'wajib' => true, 'max_size_mb' => 1, 'allowed_extensions' => 'pdf'],
            ['kode' => 'BEBAS_PERPUS_FAKULTAS', 'nama_dokumen' => 'Surat Bebas Pinjam Perpustakaan Fakultas Asli', 'jurusan' => null, 'wajib' => false, 'max_size_mb' => 1, 'allowed_extensions' => 'pdf'],
            ['kode' => 'KHS', 'nama_dokumen' => 'KHS Semester 1 s/d Terbaru yang sudah ditandatangani Kajur', 'jurusan' => null, 'wajib' => true, 'max_size_mb' => 1, 'allowed_extensions' => 'pdf'],
            ['kode' => 'TRANSKRIP', 'nama_dokumen' => 'Transkrip Nilai Ujian Skripsi yang sudah dicap Fakultas', 'jurusan' => null, 'wajib' => true, 'max_size_mb' => 1, 'allowed_extensions' => 'pdf'],
            ['kode' => 'SURAT_TUGAS_DOSBING', 'nama_dokumen' => 'Surat Tugas Dosen Pembimbing Skripsi (6 bulan terakhir)', 'jurusan' => null, 'wajib' => true, 'max_size_mb' => 10, 'allowed_extensions' => 'pdf'],
            ['kode' => 'BEBAS_TUNGGAKAN', 'nama_dokumen' => 'Surat Verifikasi Bebas Tunggakan dari Keuangan Rektorat', 'jurusan' => null, 'wajib' => true, 'max_size_mb' => 1, 'allowed_extensions' => 'pdf'],
            
            // Dokumen Khusus Jurusan
            ['kode' => 'JURNAL_JMSO', 'nama_dokumen' => 'Bukti Pengisian Jurnal Manajemen Sains dan Organisasi (JMSO)', 'jurusan' => 'MANAJEMEN', 'wajib' => true, 'max_size_mb' => 1, 'allowed_extensions' => 'pdf'],
            ['kode' => 'JURNAL_EP', 'nama_dokumen' => 'Bukti Pengisian Jurnal Jurusan Ekonomi Pembangunan', 'jurusan' => 'EKONOMI PEMBANGUNAN', 'wajib' => true, 'max_size_mb' => 1, 'allowed_extensions' => 'pdf'],
            ['kode' => 'JURNAL_AKUNTANSI', 'nama_dokumen' => 'Bukti Pengisian Jurnal Jurusan Akuntansi', 'jurusan' => 'AKUNTANSI', 'wajib' => true, 'max_size_mb' => 10, 'allowed_extensions' => 'pdf,doc,docx'],
        ];

        foreach ($dokumen as $doc) {
            JenisDokumen::create($doc);
        }
    }
}