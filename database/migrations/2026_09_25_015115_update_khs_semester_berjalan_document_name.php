<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('jenis_dokumen')
            ->whereRaw('UPPER(kode) = ?', ['KHS_SEMESTER_BERJALAN'])
            ->update([
                'nama_dokumen' => 'KHS terbaru yang ada nilai skripsi dan tanda tangan ketua jurusan',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('jenis_dokumen')
            ->whereRaw('UPPER(kode) = ?', ['KHS_SEMESTER_BERJALAN'])
            ->update([
                'nama_dokumen' => 'KHS Semester Berjalan Saat Mendaftar Yudisium',
                'updated_at' => now(),
            ]);
    }
};