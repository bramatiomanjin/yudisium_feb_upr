<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

    private function usesMysqlEnum(): bool
    {
        return in_array(DB::getDriverName(), ['mysql', 'mariadb'], true);
    }

    private function changeStatusToString(): void
    {
        Schema::table('pengajuan_yudisium', function (Blueprint $table): void {
            $table->string('status', 50)
                ->default('MENUNGGU_VERIFIKASI')
                ->change();
        });
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
        if ($this->usesMysqlEnum()) {
            $values = implode("', '", $statuses);

            DB::statement(
                "ALTER TABLE pengajuan_yudisium MODIFY status ENUM('{$values}') DEFAULT 'MENUNGGU_VERIFIKASI'"
            );

            return;
        }

        Schema::table('pengajuan_yudisium', function (Blueprint $table) use ($statuses): void {
            $table->enum('status', $statuses)
                ->default('MENUNGGU_VERIFIKASI')
                ->change();
        });
    }
};
