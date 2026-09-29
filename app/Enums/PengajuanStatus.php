<?php

namespace App\Enums;

enum PengajuanStatus: string
{
    case MENUNGGU_VERIFIKASI = 'MENUNGGU_VERIFIKASI';
    case PERLU_REVISI = 'PERLU_REVISI';
    case REVISI_DIKIRIM = 'REVISI_DIKIRIM';
    case TERVERIFIKASI = 'TERVERIFIKASI';
    case PEMBUATAN_SK = 'PEMBUATAN_SK';
    case PARAF_PIMPINAN = 'PARAF_PIMPINAN';
    case TTD_DEKAN = 'TTD_DEKAN';
    case SK_SIAP_DIAMBIL = 'SK_SIAP_DIAMBIL';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function revisionAccessValues(): array
    {
        return [
            self::PERLU_REVISI->value,
            self::REVISI_DIKIRIM->value,
        ];
    }

    public static function skFlow(): array
    {
        return [
            self::TERVERIFIKASI,
            self::PEMBUATAN_SK,
            self::PARAF_PIMPINAN,
            self::TTD_DEKAN,
            self::SK_SIAP_DIAMBIL,
        ];
    }

    public static function skTargetValues(): array
    {
        return array_map(
            static fn (self $status): string => $status->value,
            array_slice(self::skFlow(), 1)
        );
    }

    public function label(): string
    {
        return match ($this) {
            self::MENUNGGU_VERIFIKASI => 'Menunggu Verifikasi',
            self::PERLU_REVISI => 'Perlu Revisi',
            self::REVISI_DIKIRIM => 'Revisi Dikirim',
            self::TERVERIFIKASI => 'Terverifikasi',
            self::PEMBUATAN_SK => 'Pembuatan SK',
            self::PARAF_PIMPINAN => 'Paraf Pimpinan',
            self::TTD_DEKAN => 'TTD Dekan',
            self::SK_SIAP_DIAMBIL => 'SK Selesai',
        };
    }

    public function textColor(): string
    {
        return match ($this) {
            self::MENUNGGU_VERIFIKASI => 'FF475569',
            self::PERLU_REVISI => 'FFB91C1C',
            self::REVISI_DIKIRIM => 'FFC2410C',
            self::TERVERIFIKASI => 'FF1D4ED8',
            self::PEMBUATAN_SK,
            self::PARAF_PIMPINAN,
            self::TTD_DEKAN => 'FFB45309',
            self::SK_SIAP_DIAMBIL => 'FF15803D',
        };
    }

    public static function resolveVerificationResult(
        bool $hasRevision,
        bool $hasPending,
        self $pendingStatus = self::MENUNGGU_VERIFIKASI
    ): self {
        if ($hasRevision) {
            return self::PERLU_REVISI;
        }

        if ($hasPending) {
            return $pendingStatus;
        }

        return self::TERVERIFIKASI;
    }

    public function canTransitionTo(self $next): bool
    {
        if ($this === $next) {
            return true;
        }

        return match ($this) {
            self::MENUNGGU_VERIFIKASI => in_array($next, [
                self::PERLU_REVISI,
                self::TERVERIFIKASI,
            ], true),
            self::PERLU_REVISI => $next === self::REVISI_DIKIRIM,
            self::REVISI_DIKIRIM => in_array($next, [
                self::PERLU_REVISI,
                self::TERVERIFIKASI,
            ], true),
            self::TERVERIFIKASI,
            self::PEMBUATAN_SK,
            self::PARAF_PIMPINAN,
            self::TTD_DEKAN => $this->canAdvanceInSkFlowTo($next),
            self::SK_SIAP_DIAMBIL => false,
        };
    }

    private function canAdvanceInSkFlowTo(self $next): bool
    {
        $flow = self::skFlow();
        $currentIndex = array_search($this, $flow, true);
        $nextIndex = array_search($next, $flow, true);

        return $currentIndex !== false &&
            $nextIndex !== false &&
            $nextIndex > $currentIndex;
    }
}
