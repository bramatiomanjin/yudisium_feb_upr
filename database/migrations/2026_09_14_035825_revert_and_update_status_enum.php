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
        // 1. Ubah "DIAJUKAN" -> "MENUNGGU_VERIFIKASI"
        //    "VERIFIKASI_ADMIN" -> "MENUNGGU_VERIFIKASI"
        DB::table('pengajuan_yudisium')
            ->whereIn('status', ['DIAJUKAN', 'VERIFIKASI_ADMIN'])
            ->update(['status' => 'MENUNGGU_VERIFIKASI']);

        // 2. Ubah SK_TERBIT -> SK_SIAP_DIAMBIL
        DB::table('pengajuan_yudisium')
            ->where('status', 'SK_TERBIT')
            ->update(['status' => 'SK_SIAP_DIAMBIL']);

        // 3. Ubah kolom menjadi ENUM dengan urutan baru
        DB::statement("ALTER TABLE pengajuan_yudisium MODIFY status ENUM('MENUNGGU_VERIFIKASI', 'PERLU_REVISI', 'REVISI_DIKIRIM', 'TERVERIFIKASI', 'PEMBUATAN_SK', 'TTD_WAKIL_DEKAN', 'TTD_DEKAN', 'SK_SIAP_DIAMBIL') DEFAULT 'MENUNGGU_VERIFIKASI'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert ke Enum yang lama
        DB::table('pengajuan_yudisium')
            ->where('status', 'MENUNGGU_VERIFIKASI')
            ->update(['status' => 'DIAJUKAN']);

        DB::table('pengajuan_yudisium')
            ->where('status', 'SK_SIAP_DIAMBIL')
            ->update(['status' => 'SK_TERBIT']);

        DB::statement("ALTER TABLE pengajuan_yudisium MODIFY status ENUM('DIAJUKAN', 'VERIFIKASI_ADMIN', 'PERLU_REVISI', 'REVISI_DIKIRIM', 'TERVERIFIKASI', 'PEMBUATAN_SK', 'TTD_WAKIL_DEKAN', 'TTD_DEKAN', 'SK_TERBIT') DEFAULT 'DIAJUKAN'");
    }
};
