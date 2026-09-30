<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $documentNames = [
        // Nama lama yang sudah ada pada database existing.
        'Bukti Pengisian Jurnal Manajemen Sains dan Organisasi',

        // Nama baru pada seeder.
        'Bukti Pengisian Jurnal Manajemen Sains dan Organisasi (JMSO)',

        'Bukti Pengisian Jurnal Jurusan Ekonomi Pembangunan',
        'Bukti Pengisian Jurnal Jurusan Akuntansi',
    ];

    public function up(): void
    {
        DB::table('jenis_dokumen')
            ->whereIn('nama_dokumen', $this->documentNames)
            ->update([
                'wajib' => false,
            ]);
    }

    public function down(): void
    {
        DB::table('jenis_dokumen')
            ->whereIn('nama_dokumen', $this->documentNames)
            ->update([
                'wajib' => true,
            ]);
    }
};
