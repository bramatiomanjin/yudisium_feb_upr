<?php

namespace App\Support;

use App\Enums\PengajuanStatus;
use App\Models\PengajuanYudisium;
use App\Notifications\YudisiumStatusNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

            $notification = new YudisiumStatusNotification(
                event: $event,
                studentName: $name ?: 'Mahasiswa',
                submissionCode: (string) $submission->kode_pengajuan,
                statusLabel: $status->label(),
                trackingUrl: route('tracking.index'),
            );

            $content = $notification->content();

            $apiKey = (string) config('services.brevo.key');
            $senderEmail = (string) config('services.brevo.sender_email');
            $senderName = (string) config('services.brevo.sender_name');

            if ($apiKey === '' || $senderEmail === '') {
                Log::warning('Notifikasi email yudisium tidak dikirim karena konfigurasi Brevo belum lengkap.', [
                    'pengajuan_id' => $submission->id,
                    'event' => $event,
                ]);

                return;
            }

            $studentName = $name ?: 'Mahasiswa';
            $submissionCode = (string) $submission->kode_pengajuan;
            $statusLabel = $status->label();
            $trackingUrl = route('tracking.index');

            $html = '
            <html>
            <body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.6;">
                <h2 style="color:#065f46;">Yudisium FEB UPR</h2>

                <p>Yth. '.e($studentName).',</p>

                <p>'.e($content['message']).'</p>

                <p>
                    <strong>Kode Pengajuan:</strong> '.e($submissionCode).'<br>
                    <strong>Status:</strong> '.e($statusLabel).'
                </p>

                <p>
                    <a href="'.e($trackingUrl).'"
                       style="display:inline-block;padding:12px 18px;background:#047857;color:#ffffff;text-decoration:none;border-radius:6px;">
                        Lacak Pengajuan
                    </a>
                </p>

                <p>Silakan gunakan halaman tracking untuk melihat informasi terbaru mengenai pengajuan Anda.</p>

                <p>
                    Fakultas Ekonomi dan Bisnis<br>
                    Universitas Palangka Raya
                </p>
            </body>
            </html>';

            $response = Http::withHeaders([
                'api-key' => $apiKey,
                'accept' => 'application/json',
            ])
                ->asJson()
                ->timeout(10)
                ->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => [
                        'name' => $senderName,
                        'email' => $senderEmail,
                    ],
                    'to' => [
                        [
                            'email' => $email,
                            'name' => $studentName,
                        ],
                    ],
                    'subject' => $content['subject'],
                    'htmlContent' => $html,
                ]);

            $response->throw();

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