<?php

namespace App\Support;

use App\Enums\PengajuanStatus;
use App\Models\PengajuanYudisium;
use App\Notifications\YudisiumStatusNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

class StudentYudisiumNotifier
{
    public function submissionReceived(PengajuanYudisium $submission): void
    {
        $this->send(
            $submission,
            YudisiumStatusNotification::SUBMISSION_RECEIVED,
            PengajuanStatus::MENUNGGU_VERIFIKASI
        );
    }

    public function statusChanged(
        PengajuanYudisium $submission,
        string $previousStatus,
        PengajuanStatus $newStatus
    ): void {
        if (strtoupper(trim($previousStatus)) === $newStatus->value) {
            return;
        }

        $event = match ($newStatus) {
            PengajuanStatus::PERLU_REVISI => YudisiumStatusNotification::REVISION_REQUIRED,
            PengajuanStatus::REVISI_DIKIRIM => YudisiumStatusNotification::REVISION_RECEIVED,
            PengajuanStatus::TERVERIFIKASI => YudisiumStatusNotification::VERIFIED,
            PengajuanStatus::PEMBUATAN_SK => YudisiumStatusNotification::SK_PROCESSING,
            PengajuanStatus::PARAF_PIMPINAN => YudisiumStatusNotification::LEADERSHIP_INITIAL,
            PengajuanStatus::TTD_DEKAN => YudisiumStatusNotification::DEAN_SIGNATURE,
            PengajuanStatus::SK_SIAP_DIAMBIL => YudisiumStatusNotification::READY_FOR_COLLECTION,
            default => null,
        };

        if ($event !== null) {
            $this->send($submission, $event, $newStatus);
        }
    }

    private function send(
        PengajuanYudisium $submission,
        string $event,
        PengajuanStatus $status
    ): void {
        try {
            $submission->loadMissing('mahasiswa');

            $email = trim((string) $submission->mahasiswa?->email);
            $name = trim((string) $submission->mahasiswa?->nama_lengkap);

            if ($email === '') {
                Log::warning('Notifikasi email yudisium tidak dikirim karena alamat email tidak tersedia.', [
                    'pengajuan_id' => $submission->id,
                    'event' => $event,
                    'status' => 'failed',
                ]);

                return;
            }

            Log::info('Notifikasi email yudisium menunggu pengiriman.', [
                'pengajuan_id' => $submission->id,
                'event' => $event,
                'status' => 'pending',
            ]);

            Notification::route('mail', [$email => $name ?: 'Mahasiswa'])
                ->notify(new YudisiumStatusNotification(
                    event: $event,
                    studentName: $name ?: 'Mahasiswa',
                    submissionCode: (string) $submission->kode_pengajuan,
                    statusLabel: $status->label(),
                    trackingUrl: route('tracking.index'),
                ));

            Log::info('Notifikasi email yudisium berhasil dikirim.', [
                'pengajuan_id' => $submission->id,
                'event' => $event,
                'status' => 'sent',
            ]);
        } catch (Throwable $exception) {
            Log::error('Notifikasi email yudisium gagal dikirim.', [
                'pengajuan_id' => $submission->id,
                'event' => $event,
                'status' => 'failed',
                'exception' => $exception,
            ]);
        }
    }
}
