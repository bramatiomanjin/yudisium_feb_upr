<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->changeStatusToString();

        DB::table('pengajuan_yudisium')
            ->where('status', 'TTD_WAKIL_DEKAN')
            ->update(['status' => 'PARAF_PIMPINAN']);

        $this->applyOfficialStatusSchema();
    }

    public function down(): void
    {
        $this->changeStatusToString();

        DB::table('pengajuan_yudisium')
            ->where('status', 'PARAF_PIMPINAN')
            ->update(['status' => 'TTD_WAKIL_DEKAN']);

        $this->applyLegacyStatusSchema();
    }

    private function changeStatusToString(): void
    {
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

            return;
        }

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("
                ALTER TABLE pengajuan_yudisium
                MODIFY status VARCHAR(50)
                DEFAULT 'MENUNGGU_VERIFIKASI'
            ");
        }
    }

    private function applyOfficialStatusSchema(): void
    {
        $statuses = [
            'MENUNGGU_VERIFIKASI',
            'PERLU_REVISI',
            'REVISI_DIKIRIM',
            'TERVERIFIKASI',
            'PEMBUATAN_SK',
            'PARAF_PIMPINAN',
            'TTD_DEKAN',
            'SK_SIAP_DIAMBIL',
        ];

        $this->changeStatusToEnum($statuses);
    }

    private function applyLegacyStatusSchema(): void
    {
        $statuses = [
            'MENUNGGU_VERIFIKASI',
            'PERLU_REVISI',
            'REVISI_DIKIRIM',
            'TERVERIFIKASI',
            'PEMBUATAN_SK',
            'TTD_WAKIL_DEKAN',
            'TTD_DEKAN',
            'SK_SIAP_DIAMBIL',
        ];

        $this->changeStatusToEnum($statuses);
    }

    private function changeStatusToEnum(array $statuses): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $values = implode("', '", $statuses);

            DB::statement(
                "ALTER TABLE pengajuan_yudisium
                 MODIFY status ENUM('{$values}')
                 DEFAULT 'MENUNGGU_VERIFIKASI'"
            );

            return;
        }

        if ($driver === 'pgsql') {
            // PostgreSQL production menggunakan VARCHAR.
            // Validasi status tetap ditangani oleh aplikasi.
            DB::statement("
                ALTER TABLE pengajuan_yudisium
                ALTER COLUMN status TYPE VARCHAR(50)
            ");

            DB::statement("
                ALTER TABLE pengajuan_yudisium
                ALTER COLUMN status SET DEFAULT 'MENUNGGU_VERIFIKASI'
            ");
        }
    }
};