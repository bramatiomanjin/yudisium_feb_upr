<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YudisiumStatusNotification extends Notification
{
    use Queueable;

    public const SUBMISSION_RECEIVED = 'submission_received';

    public const REVISION_REQUIRED = 'revision_required';

    public const REVISION_RECEIVED = 'revision_received';

    public const VERIFIED = 'verified';

    public const SK_PROCESSING = 'sk_processing';

    public const LEADERSHIP_INITIAL = 'leadership_initial';

    public const DEAN_SIGNATURE = 'dean_signature';

    public const READY_FOR_COLLECTION = 'ready_for_collection';

    public function __construct(
        public readonly string $event,
        public readonly string $studentName,
        public readonly string $submissionCode,
        public readonly string $statusLabel,
        public readonly string $trackingUrl,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $content = $this->content();

        return (new MailMessage)
            ->subject($content['subject'])
            ->greeting('Yth. '.$this->studentName.',')
            ->line($content['message'])
            ->line('Kode pengajuan: '.$this->submissionCode)
            ->line('Status: '.$this->statusLabel)
            ->action('Lacak Pengajuan', $this->trackingUrl)
            ->line('Silakan gunakan halaman tracking untuk melihat informasi terbaru mengenai pengajuan Anda.')
            ->salutation('Fakultas Ekonomi dan Bisnis Universitas Palangka Raya');
    }

    /**
     * @return array{subject: string, message: string}
     */
    private function content(): array
    {
        return match ($this->event) {
            self::SUBMISSION_RECEIVED => [
                'subject' => 'Pengajuan Yudisium Berhasil Diterima',
                'message' => 'Pengajuan Yudisium Anda telah berhasil diterima dan akan diperiksa oleh Admin Akademik.',
            ],
            self::REVISION_REQUIRED => [
                'subject' => 'Revisi Pengajuan Yudisium Diperlukan',
                'message' => 'Pengajuan Yudisium Anda memerlukan revisi. Silakan lihat catatan perbaikan melalui halaman tracking.',
            ],
            self::REVISION_RECEIVED => [
                'subject' => 'Revisi Pengajuan Yudisium Telah Diterima',
                'message' => 'Revisi Pengajuan Yudisium Anda telah diterima dan akan diperiksa kembali oleh Admin Akademik.',
            ],
            self::VERIFIED => [
                'subject' => 'Pengajuan Yudisium Telah Terverifikasi',
                'message' => 'Pengajuan Yudisium Anda telah selesai diverifikasi.',
            ],
            self::SK_PROCESSING => [
                'subject' => 'SK Yudisium Sedang Diproses',
                'message' => 'Pengajuan Anda telah memasuki tahap pembuatan SK Yudisium.',
            ],
            self::LEADERSHIP_INITIAL => [
                'subject' => 'SK Yudisium Menunggu Paraf Pimpinan',
                'message' => 'SK Yudisium Anda sedang dalam tahap paraf pimpinan.',
            ],
            self::DEAN_SIGNATURE => [
                'subject' => 'SK Yudisium Menunggu Tanda Tangan Dekan',
                'message' => 'SK Yudisium Anda sedang dalam tahap tanda tangan Dekan.',
            ],
            self::READY_FOR_COLLECTION => [
                'subject' => 'SK Yudisium Siap Diambil',
                'message' => 'SK Yudisium Anda telah selesai dan siap diambil sesuai ketentuan Fakultas.',
            ],
            default => [
                'subject' => 'Pembaruan Status Pengajuan Yudisium',
                'message' => 'Status Pengajuan Yudisium Anda telah diperbarui.',
            ],
        };
    }
}
