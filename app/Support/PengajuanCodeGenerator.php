<?php

namespace App\Support;

use App\Models\PengajuanYudisium;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PengajuanCodeGenerator
{
    public function generate(?int $year = null): string
    {
        $year ??= (int) now()->format('Y');

        return DB::transaction(function () use ($year): string {
            DB::table('pengajuan_code_sequences')->insertOrIgnore([
                'year' => $year,
                'last_number' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $sequence = DB::table('pengajuan_code_sequences')
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if ($sequence === null) {
                throw new RuntimeException('Counter kode pengajuan tidak tersedia.');
            }

            $lastNumber = max(
                (int) $sequence->last_number,
                $this->existingMaximumForYear($year)
            );
            $nextNumber = $lastNumber + 1;

            if ($nextNumber > 9999) {
                throw new RuntimeException("Nomor pengajuan tahun {$year} telah mencapai batas.");
            }

            DB::table('pengajuan_code_sequences')
                ->where('year', $year)
                ->update([
                    'last_number' => $nextNumber,
                    'updated_at' => now(),
                ]);

            return sprintf('YDS-%d-%04d', $year, $nextNumber);
        });
    }

    private function existingMaximumForYear(int $year): int
    {
        $pattern = '/^YDS-'.preg_quote((string) $year, '/').'-(\d{4})$/';

        return PengajuanYudisium::query()
            ->where('kode_pengajuan', 'like', "YDS-{$year}-%")
            ->pluck('kode_pengajuan')
            ->reduce(function (int $maximum, string $code) use ($pattern): int {
                if (! preg_match($pattern, $code, $matches)) {
                    return $maximum;
                }

                return max($maximum, (int) $matches[1]);
            }, 0);
    }
}
