<?php

namespace App\Models;

use App\Enums\PengajuanStatus;
use DomainException;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Illuminate\Support\Str;

class PengajuanYudisium extends Model
{
    protected $table = 'pengajuan_yudisium';

    protected $guarded = ['id'];

    protected $hidden = [
        'revision_access_token_hash',
        'revision_access_token_nonce',
    ];

    protected $casts = [
        'tanggal_ujian' => 'date',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $pengajuan): void {
            $status = strtoupper(trim((string) $pengajuan->status));

            if (PengajuanStatus::tryFrom($status) === null) {
                throw new InvalidArgumentException("Status pengajuan tidak valid: {$status}");
            }

            $pengajuan->status = $status;
        });
    }

    public function transitionTo(
        PengajuanStatus $nextStatus,
        array $attributes = []
    ): void {
        $currentStatus = PengajuanStatus::tryFrom((string) $this->status);

        if ($currentStatus === null || ! $currentStatus->canTransitionTo($nextStatus)) {
            throw new DomainException(sprintf(
                'Transisi status %s ke %s tidak valid.',
                (string) $this->status,
                $nextStatus->value
            ));
        }

        $this->forceFill([
            ...$attributes,
            'status' => $nextStatus->value,
        ])->save();
    }

    public function getOrCreateRevisionAccessToken(): string
    {
        if (
            $this->revision_access_token_hash === null ||
            $this->revision_access_token_nonce === null
        ) {
            return $this->storeNewRevisionAccessToken();
        }

        $token = $this->deriveRevisionAccessToken();

        if (! $this->hasValidRevisionAccessToken($token)) {
            throw new \RuntimeException(
                'Token akses revisi tidak dapat dipulihkan. Lakukan reset token secara eksplisit.'
            );
        }

        return $token;
    }

    public function resetRevisionAccessToken(): string
    {
        return $this->storeNewRevisionAccessToken();
    }

    private function storeNewRevisionAccessToken(): string
    {
        $nonce = Str::random(64);
        $token = $this->deriveRevisionAccessToken($nonce);

        static::withoutTimestamps(function () use ($nonce, $token): void {
            $this->forceFill([
                'revision_access_token_hash' => hash('sha256', $token),
                'revision_access_token_nonce' => $nonce,
            ])->saveQuietly();
        });

        return $token;
    }

    private function deriveRevisionAccessToken(?string $nonce = null): string
    {
        $key = (string) config('app.key');

        if ($key === '') {
            throw new \RuntimeException('APP_KEY wajib tersedia untuk membuat token akses revisi.');
        }

        return hash_hmac(
            'sha256',
            implode('|', [
                'revision-access',
                $this->getKey(),
                $this->kode_pengajuan,
                $nonce ?? $this->revision_access_token_nonce,
            ]),
            $key
        );
    }

    public function hasValidRevisionAccessToken(mixed $token): bool
    {
        if (
            ! is_string($token) ||
            $token === '' ||
            $this->revision_access_token_hash === null ||
            $this->revision_access_token_nonce === null
        ) {
            return false;
        }

        return
            hash_equals($this->deriveRevisionAccessToken(), $token) &&
            hash_equals(
                $this->revision_access_token_hash,
                hash('sha256', $token)
            );
    }

    public function mahasiswa()
    {
        return $this->belongsTo(
            Mahasiswa::class,
            'nim',
            'nim'
        );
    }

    public function dokumen()
    {
        return $this->hasMany(
            PengajuanDokumen::class,
            'pengajuan_id'
        );
    }

    public function validasi()
    {
        return $this->hasMany(
            ValidasiField::class,
            'pengajuan_id'
        );
    }
}
