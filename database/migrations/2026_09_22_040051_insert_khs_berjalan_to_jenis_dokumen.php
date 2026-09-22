<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('jenis_dokumen')->insert([
            'kode' => 'KHS_SEMESTER_BERJALAN',
            'nama_dokumen' => 'KHS Semester Berjalan Saat Mendaftar Yudisium',
            'wajib' => 1,
            'max_size_mb' => 1,
            'allowed_extensions' => 'pdf',
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('jenis_dokumen')->where('kode', 'KHS_SEMESTER_BERJALAN')->delete();
    }
};
