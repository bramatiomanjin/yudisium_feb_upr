<?php

namespace Tests\Feature;

use App\Models\PengajuanDokumen;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Throwable;

class ErrorHandlingAndRateLimitingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
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
            $table->string('nim')->unique();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('jenis_dokumen', function (Blueprint $table): void {
            $table->id();
            $table->string('kode')->unique();
            $table->string('jurusan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pengajuan_dokumen', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('pengajuan_id');
            $table->unsignedBigInteger('jenis_dokumen_id');
            $table->string('nama_file_asli');
            $table->string('nama_file_storage');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('ukuran_file')->nullable();
            $table->string('status_validasi');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('pengajuan_dokumen');
        Schema::dropIfExists('jenis_dokumen');
        Schema::dropIfExists('pengajuan_yudisium');
        Schema::dropIfExists('mahasiswa');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    /** @param array<string, string> $payload */
    #[DataProvider('rateLimitedEndpoints')]
    public function test_sensitive_post_endpoints_enforce_their_primary_rate_limit(
        string $uri,
        array $payload,
        int $allowedAttempts,
        string $expectedMessage
    ): void {
        for ($attempt = 1; $attempt <= $allowedAttempts; $attempt++) {
            $response = $this->postJson($uri, $payload);

            $this->assertNotSame(429, $response->status());
        }

        $this->postJson($uri, $payload)
            ->assertTooManyRequests()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', $expectedMessage)
            ->assertHeader('Retry-After');
    }

    /**
     * @return array<string, array{string, array<string, string>, int, string}>
     */
    public static function rateLimitedEndpoints(): array
    {
        return [
            'login' => [
                '/admin/login',
                ['email' => 'limited@example.test', 'password' => 'wrong'],
                5,
                'Terlalu banyak percobaan login. Silakan coba lagi sebentar.',
            ],
            'admin registration' => [
                '/admin/register',
                [],
                20,
                'Terlalu banyak permintaan registrasi. Silakan coba lagi nanti.',
            ],
            'tracking' => [
                '/tracking',
                [],
                60,
                'Terlalu banyak permintaan tracking. Silakan coba lagi sebentar.',
            ],
            'initial submission' => [
                '/pengajuan',
                ['nim' => 'RATE-LIMIT-NIM'],
                10,
                'Terlalu banyak percobaan pengajuan. Silakan coba lagi nanti.',
            ],
            'revision access verification' => [
                '/tracking/revision-access',
                ['nim' => 'RATE-LIMIT-NIM', 'kode_pengajuan' => 'YDS-2026-9999'],
                10,
                'Terlalu banyak percobaan verifikasi revisi. Silakan coba lagi sebentar.',
            ],
            'revision submission' => [
                '/revisi/YDS-2026-9999',
                [],
                10,
                'Terlalu banyak percobaan pengiriman revisi. Silakan coba lagi nanti.',
            ],
        ];
    }

    public function test_submission_failure_is_logged_without_exposing_database_details(): void
    {
        Log::spy();

        $response = $this->postJson('/pengajuan', [
            'nim' => '2030101001',
            'nama_lengkap' => 'Mahasiswa Uji',
            'email' => 'mahasiswa@example.test',
            'no_whatsapp' => '081234567890',
            'tahun_angkatan' => 2022,
            'jalur_masuk' => 'REGULER',
            'jurusan' => 'MANAJEMEN',
            'karya_tulis' => 'SKRIPSI',
            'judul_karya_tulis' => 'Judul Uji',
            'tanggal_ujian' => '2026-09-29',
            'nilai_angka' => 85,
            'nilai_huruf' => 'A',
        ]);

        $response
            ->assertInternalServerError()
            ->assertExactJson([
                'success' => false,
                'message' => 'Pengajuan belum dapat diproses. Silakan coba lagi.',
            ])
            ->assertDontSee('SQLSTATE')
            ->assertDontSee('pengajuan_code_sequences')
            ->assertDontSee(base_path());

        Log::shouldHaveReceived('error')
            ->once()
            ->withArgs(fn (string $message, array $context): bool => $message === 'Gagal memproses pengajuan yudisium.'
                && ($context['exception'] ?? null) instanceof Throwable
            );
    }

    public function test_missing_admin_document_does_not_expose_its_storage_path(): void
    {
        Storage::fake('local');
        Log::spy();

        $admin = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'ADMIN',
            'status' => 'ACTIVE',
        ]);

        $document = PengajuanDokumen::query()->create([
            'pengajuan_id' => 1,
            'jenis_dokumen_id' => 1,
            'nama_file_asli' => 'dokumen.pdf',
            'nama_file_storage' => 'internal-name.pdf',
            'file_path' => 'private/yudisium/secret/internal-name.pdf',
            'mime_type' => 'application/pdf',
            'ukuran_file' => 100,
            'status_validasi' => 'PENDING',
        ]);

        $this->actingAs($admin)
            ->get("/admin/file/{$document->id}")
            ->assertNotFound()
            ->assertDontSee($document->file_path);

        Log::shouldHaveReceived('warning')
            ->once()
            ->withArgs(fn (string $message, array $context): bool => $message === 'Dokumen pengajuan tidak ditemukan di storage.'
                && ($context['file_path'] ?? null) === $document->file_path
            );
    }
}
