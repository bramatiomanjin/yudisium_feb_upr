<?php

namespace Tests\Feature;

use App\Enums\PengajuanStatus;
use App\Models\Mahasiswa;
use App\Models\PengajuanYudisium;
use App\Models\User;
use App\Models\ValidasiField;
use App\Notifications\YudisiumStatusNotification;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class YudisiumEmailNotificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-09-29 09:00:00');
        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
            'app.url' => 'https://yudisium-feb.example.test',
        ]);

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('ADMIN');
            $table->string('status')->default('ACTIVE');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('mahasiswa', function (Blueprint $table): void {
            $table->string('nim', 20)->primary();
            $table->string('nama_lengkap');
            $table->string('email');
            $table->string('no_whatsapp');
            $table->integer('tahun_angkatan');
            $table->string('jalur_masuk');
            $table->string('jurusan');
            $table->timestamps();
        });

        Schema::create('pengajuan_yudisium', function (Blueprint $table): void {
            $table->id();
            $table->string('kode_pengajuan')->unique();
            $table->string('revision_access_token_hash', 64)->nullable();
            $table->string('revision_access_token_nonce', 64)->nullable();
            $table->string('nim')->unique();
            $table->string('karya_tulis');
            $table->text('judul_karya_tulis');
            $table->date('tanggal_ujian');
            $table->decimal('nilai_angka', 5, 2);
            $table->string('nilai_huruf');
            $table->string('status');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('pengajuan_code_sequences', function (Blueprint $table): void {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
        });

        Schema::create('jenis_dokumen', function (Blueprint $table): void {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama_dokumen');
            $table->string('jurusan')->nullable();
            $table->boolean('wajib')->default(true);
            $table->unsignedInteger('max_size_mb')->default(2);
            $table->string('allowed_extensions')->default('pdf');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('validasi_field', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('pengajuan_id');
            $table->string('field_key');
            $table->string('status_validasi');
            $table->text('feedback')->nullable();
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('pengajuan_dokumen', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('pengajuan_id');
            $table->unsignedBigInteger('jenis_dokumen_id');
            $table->string('nama_file_asli')->nullable();
            $table->string('nama_file_storage')->nullable();
            $table->string('file_path')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('ukuran_file')->nullable();
            $table->string('status_validasi');
            $table->text('feedback')->nullable();
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('riwayat_revisi', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('pengajuan_id');
            $table->string('jenis_revisi');
            $table->string('field_key')->nullable();
            $table->unsignedBigInteger('jenis_dokumen_id')->nullable();
            $table->text('nilai_lama')->nullable();
            $table->text('nilai_baru')->nullable();
            $table->text('feedback_admin')->nullable();
            $table->unsignedInteger('revisi_ke')->default(1);
            $table->timestamps();
        });

        Schema::create('riwayat_status', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('pengajuan_id');
            $table->string('status');
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('riwayat_status');
        Schema::dropIfExists('riwayat_revisi');
        Schema::dropIfExists('pengajuan_dokumen');
        Schema::dropIfExists('validasi_field');
        Schema::dropIfExists('jenis_dokumen');
        Schema::dropIfExists('pengajuan_code_sequences');
        Schema::dropIfExists('pengajuan_yudisium');
        Schema::dropIfExists('mahasiswa');
        Schema::dropIfExists('users');
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_successful_submission_sends_one_registration_email(): void
    {
        Notification::fake();

        $response = $this->postJson(route('pengajuan.store'), $this->submissionPayload())
            ->assertOk()
            ->assertJsonPath('success', true);

        $code = $response->json('code');

        Notification::assertSentOnDemandTimes(YudisiumStatusNotification::class, 1);
        $this->assertEventSent(YudisiumStatusNotification::SUBMISSION_RECEIVED, function (
            YudisiumStatusNotification $notification
        ) use ($code): bool {
            return $notification->submissionCode === $code
                && $notification->statusLabel === 'Menunggu Verifikasi'
                && $notification->trackingUrl === route('tracking.index');
        });
    }

    public function test_admin_revision_and_verification_changes_send_the_expected_emails(): void
    {
        Notification::fake();
        $admin = $this->createAdmin();
        $revision = $this->createSubmission(1);
        $verified = $this->createSubmission(2);

        foreach ([$revision, $verified] as $submission) {
            ValidasiField::query()->create([
                'pengajuan_id' => $submission->id,
                'field_key' => 'judul_karya_tulis',
                'status_validasi' => 'PENDING',
            ]);
        }

        $this->actingAs($admin)
            ->postJson("/submissions/{$revision->id}/verify", [
                'items' => [[
                    'type' => 'field',
                    'key' => 'judul_karya_tulis',
                    'decision' => 'revision',
                    'feedback' => 'Silakan perbaiki judul.',
                ]],
            ])->assertOk();

        $this->actingAs($admin)
            ->postJson("/submissions/{$verified->id}/verify", [
                'items' => [[
                    'type' => 'field',
                    'key' => 'judul_karya_tulis',
                    'decision' => 'approved',
                ]],
            ])->assertOk();

        Notification::assertSentOnDemandTimes(YudisiumStatusNotification::class, 2);
        $this->assertEventSent(YudisiumStatusNotification::REVISION_REQUIRED);
        $this->assertEventSent(YudisiumStatusNotification::VERIFIED);
    }

    public function test_successful_student_revision_sends_confirmation_without_a_revision_token(): void
    {
        Notification::fake();
        $submission = $this->createSubmission(1, PengajuanStatus::PERLU_REVISI);
        ValidasiField::query()->create([
            'pengajuan_id' => $submission->id,
            'field_key' => 'judul_karya_tulis',
            'status_validasi' => 'REVISI',
            'feedback' => 'Silakan perbaiki judul.',
        ]);
        $token = $submission->getOrCreateRevisionAccessToken();

        $this->post(route('tracking.proses', [
            'kode_pengajuan' => $submission->kode_pengajuan,
            'token' => $token,
        ]), [
            'revisi_field' => [
                'judul_karya_tulis' => 'Judul yang Telah Diperbaiki',
            ],
        ])->assertRedirect();

        $this->assertSame(PengajuanStatus::REVISI_DIKIRIM->value, $submission->fresh()->status);
        Notification::assertSentOnDemandTimes(YudisiumStatusNotification::class, 1);
        $this->assertEventSent(YudisiumStatusNotification::REVISION_RECEIVED, function (
            YudisiumStatusNotification $notification
        ) use ($token): bool {
            return ! str_contains($notification->trackingUrl, $token)
                && ! str_contains($notification->trackingUrl, 'token=');
        });
    }

    public function test_every_relevant_sk_status_change_sends_an_email(): void
    {
        Notification::fake();
        $admin = $this->createAdmin();
        $submission = $this->createSubmission(1, PengajuanStatus::TERVERIFIKASI);

        $expectedEvents = [
            PengajuanStatus::PEMBUATAN_SK->value => YudisiumStatusNotification::SK_PROCESSING,
            PengajuanStatus::PARAF_PIMPINAN->value => YudisiumStatusNotification::LEADERSHIP_INITIAL,
            PengajuanStatus::TTD_DEKAN->value => YudisiumStatusNotification::DEAN_SIGNATURE,
            PengajuanStatus::SK_SIAP_DIAMBIL->value => YudisiumStatusNotification::READY_FOR_COLLECTION,
        ];

        foreach ($expectedEvents as $status => $event) {
            $this->actingAs($admin)
                ->patchJson("/submissions/{$submission->id}/sk-status", [
                    'status' => strtolower($status),
                ])->assertOk();

            $this->assertEventSent($event);
        }

        Notification::assertSentOnDemandTimes(YudisiumStatusNotification::class, 4);
    }

    public function test_repeating_the_same_status_does_not_send_an_email_twice(): void
    {
        Notification::fake();
        $admin = $this->createAdmin();
        $submission = $this->createSubmission(1, PengajuanStatus::TERVERIFIKASI);

        foreach ([1, 2] as $attempt) {
            $this->actingAs($admin)
                ->patchJson("/submissions/{$submission->id}/sk-status", [
                    'status' => 'pembuatan_sk',
                ])->assertOk();
        }

        Notification::assertSentOnDemandTimes(YudisiumStatusNotification::class, 1);
        $this->assertEventSent(YudisiumStatusNotification::SK_PROCESSING);
    }

    public function test_mail_failure_does_not_cancel_a_successful_submission(): void
    {
        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('send')
            ->once()
            ->andThrow(new RuntimeException('SMTP unavailable'));
        $this->app->instance(Dispatcher::class, $dispatcher);

        $this->postJson(route('pengajuan.store'), $this->submissionPayload())
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('pengajuan_yudisium', [
            'nim' => '2301110001',
            'status' => PengajuanStatus::MENUNGGU_VERIFIKASI->value,
        ]);
    }

    private function assertEventSent(string $event, ?callable $condition = null): void
    {
        Notification::assertSentOnDemand(
            YudisiumStatusNotification::class,
            function (YudisiumStatusNotification $notification) use ($event, $condition): bool {
                return $notification->event === $event
                    && ($condition === null || $condition($notification));
            }
        );
    }

    private function createAdmin(): User
    {
        return User::query()->create([
            'name' => 'Admin Test',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'ADMIN',
            'status' => 'ACTIVE',
        ]);
    }

    private function createSubmission(
        int $sequence,
        PengajuanStatus $status = PengajuanStatus::MENUNGGU_VERIFIKASI
    ): PengajuanYudisium {
        $nim = '230111'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

        Mahasiswa::query()->create([
            'nim' => $nim,
            'nama_lengkap' => 'Mahasiswa '.$sequence,
            'email' => "mahasiswa{$sequence}@example.test",
            'no_whatsapp' => '081234567890',
            'tahun_angkatan' => 2023,
            'jalur_masuk' => 'REGULER',
            'jurusan' => 'MANAJEMEN',
        ]);

        return PengajuanYudisium::query()->create([
            'kode_pengajuan' => 'YDS-2026-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT),
            'nim' => $nim,
            'karya_tulis' => 'SKRIPSI',
            'judul_karya_tulis' => 'Judul Test',
            'tanggal_ujian' => '2026-09-01',
            'nilai_angka' => 85,
            'nilai_huruf' => 'A',
            'status' => $status->value,
            'submitted_at' => now(),
        ]);
    }

    /**
     * @return array<string, string|int>
     */
    private function submissionPayload(): array
    {
        return [
            'nim' => '2301110001',
            'nama_lengkap' => 'Mahasiswa Test',
            'email' => 'mahasiswa@example.test',
            'no_whatsapp' => '081234567890',
            'tahun_angkatan' => 2023,
            'jalur_masuk' => 'REGULER',
            'jurusan' => 'MANAJEMEN',
            'karya_tulis' => 'SKRIPSI',
            'judul_karya_tulis' => 'Judul Test',
            'tanggal_ujian' => '2026-09-01',
            'nilai_angka' => '85,00',
            'nilai_huruf' => 'A',
        ];
    }
}
