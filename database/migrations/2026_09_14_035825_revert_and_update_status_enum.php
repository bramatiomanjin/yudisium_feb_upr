<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('pengajuan_yudisium')
            ->whereIn('status', ['DIAJUKAN', 'VERIFIKASI_ADMIN'])
            ->update(['status' => 'MENUNGGU_VERIFIKASI']);

        DB::table('pengajuan_yudisium')
            ->where('status', 'SK_TERBIT')
            ->update(['status' => 'SK_SIAP_DIAMBIL']);

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("
                ALTER TABLE pengajuan_yudisium
                ALTER COLUMN status TYPE VARCHAR(50)
            ");

            DB::statement("
                ALTER TABLE pengajuan_yudisium
                ALTER COLUMN status SET DEFAULT 'MENUNGGU_VERIFIKASI'
            ");
        } else {
            DB::statement("
                ALTER TABLE pengajuan_yudisium
                MODIFY status ENUM(
                    'MENUNGGU_VERIFIKASI',
                    'PERLU_REVISI',
                    'REVISI_DIKIRIM',
                    'TERVERIFIKASI',
                    'PEMBUATAN_SK',
                    'TTD_WAKIL_DEKAN',
                    'TTD_DEKAN',
                    'SK_SIAP_DIAMBIL'
                ) DEFAULT 'MENUNGGU_VERIFIKASI'
            ");
        }
    }

    public function down(): void
    {
        DB::table('pengajuan_yudisium')
            ->where('status', 'MENUNGGU_VERIFIKASI')
            ->update(['status' => 'DIAJUKAN']);

        DB::table('pengajuan_yudisium')
            ->where('status', 'SK_SIAP_DIAMBIL')
            ->update(['status' => 'SK_TERBIT']);

        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("
                ALTER TABLE pengajuan_yudisium
                ALTER COLUMN status TYPE VARCHAR(50)
            ");

            DB::statement("
                ALTER TABLE pengajuan_yudisium
                ALTER COLUMN status SET DEFAULT 'DIAJUKAN'
            ");
        } else {
            DB::statement("
                ALTER TABLE pengajuan_yudisium
                MODIFY status ENUM(
                    'DIAJUKAN',
                    'VERIFIKASI_ADMIN',
                    'PERLU_REVISI',
                    'REVISI_DIKIRIM',
                    'TERVERIFIKASI',
                    'PEMBUATAN_SK',
                    'TTD_WAKIL_DEKAN',
                    'TTD_DEKAN',
                    'SK_TERBIT'
                ) DEFAULT 'DIAJUKAN'
            ");
        }
    }
};