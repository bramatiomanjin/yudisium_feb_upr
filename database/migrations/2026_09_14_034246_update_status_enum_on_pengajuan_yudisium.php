<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
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
                MODIFY status VARCHAR(50) DEFAULT 'DIAJUKAN'
            ");
        }
    }

    public function down(): void
    {
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