<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\PengajuanYudisium;
use App\Models\ValidasiField;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RevisionAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
        ]);

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
            $table->string('kode_pengajuan', 30)->unique();
            $table->string('revision_access_token_hash', 64)->nullable();
            $table->string('revision_access_token_nonce', 64)->nullable();
            $table->string('nim', 20)->unique();
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

        Schema::create('jenis_dokumen', function (Blueprint $table): void {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama_dokumen');
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
            $table->string('nama_file_asli');
            $table->string('nama_file_storage');
            $table->string('file_path');
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
        Schema::dropIfExists('pengajuan_yudisium');
        Schema::dropIfExists('mahasiswa');

        parent::tearDown();
    }

    public function test_tracking_with_nim_only_still_succeeds_without_issuing_a_token(): void
    {
        $pengajuan = $this->createRevisionSubmission();

        $response = $this->postJson(route('tracking.search'), ['nim' => $pengajuan->nim])
            ->assertOk()
            ->assertJsonPath('data.kode_pengajuan', $pengajuan->kode_pengajuan);

        $response->assertJsonMissingPath('data.revision_url');
        $response->assertJsonMissingPath('data.token');
        $this->assertNull($pengajuan->fresh()->revision_access_token_hash);
    }

    public function test_tracking_with_submission_code_only_still_succeeds_without_issuing_a_token(): void
    {
        $pengajuan = $this->createRevisionSubmission();

        $this->postJson(route('tracking.search'), [
            'kode_pengajuan' => $pengajuan->kode_pengajuan,
        ])->assertOk()
            ->assertJsonPath('data.nim', $pengajuan->nim);

        $this->assertNull($pengajuan->fresh()->revision_access_token_hash);
    }

    public function test_tracking_with_nim_and_code_succeeds_and_explicit_access_always_returns_the_same_token(): void
    {
        $pengajuan = $this->createRevisionSubmission();
        $credentials = [
            'nim' => $pengajuan->nim,
            'kode_pengajuan' => $pengajuan->kode_pengajuan,
        ];

        $this->postJson(route('tracking.search'), $credentials)
            ->assertOk()
            ->assertJsonPath('data.revision_access_required', true);

        $this->assertNull($pengajuan->fresh()->revision_access_token_hash);

        $firstUrl = $this->postJson(route('tracking.revision-access'), $credentials)
            ->assertOk()
            ->json('data.revision_url');
        $firstHash = $pengajuan->fresh()->revision_access_token_hash;

        $this->postJson(route('tracking.search'), $credentials)
            ->assertOk();
        $this->flushSession();

        $secondUrl = $this->postJson(route('tracking.revision-access'), $credentials)
            ->assertOk()
            ->json('data.revision_url');

        $this->assertSame($firstUrl, $secondUrl);
        $this->assertSame($firstHash, $pengajuan->fresh()->revision_access_token_hash);
        $this->assertStringNotContainsString($this->tokenFromUrl($firstUrl), $firstHash);
        $this->assertArrayNotHasKey('revision_access_token_hash', $pengajuan->fresh()->toArray());
        $this->assertArrayNotHasKey('revision_access_token_nonce', $pengajuan->fresh()->toArray());
    }

    public function test_revision_page_rejects_a_missing_token(): void
    {
        $pengajuan = $this->createRevisionSubmission();

        $this->get(route('tracking.revisi', $pengajuan->kode_pengajuan))
            ->assertForbidden();
    }

    public function test_revision_page_rejects_an_invalid_token(): void
    {
        $pengajuan = $this->createRevisionSubmission();
        $pengajuan->getOrCreateRevisionAccessToken();

        $this->get($this->revisionUrl($pengajuan, 'invalid-token'))
            ->assertForbidden();
    }

    public function test_token_for_another_submission_cannot_open_revision_page(): void
    {
        $pengajuanA = $this->createRevisionSubmission(1);
        $pengajuanB = $this->createRevisionSubmission(2);
        $tokenA = $pengajuanA->getOrCreateRevisionAccessToken();

        $this->get($this->revisionUrl($pengajuanB, $tokenA))
            ->assertForbidden();

        $this->post($this->revisionUrl($pengajuanB, $tokenA), [
            'revisi_field' => ['judul_karya_tulis' => 'Tidak Boleh Berubah'],
        ])->assertForbidden();

        $this->assertSame('Judul Lama', $pengajuanB->fresh()->judul_karya_tulis);
    }

    public function test_valid_token_opens_revision_page_and_revision_data_endpoints(): void
    {
        $pengajuan = $this->createRevisionSubmission();
        $token = $pengajuan->getOrCreateRevisionAccessToken();
        $tokenHash = $pengajuan->fresh()->revision_access_token_hash;

        $this->get($this->revisionUrl($pengajuan, $token))
            ->assertOk()
            ->assertViewIs('mahasiswa.revisi');
        $this->get($this->revisionUrl($pengajuan, $token))
            ->assertOk();

        $this->assertSame($tokenHash, $pengajuan->fresh()->revision_access_token_hash);

        $query = http_build_query([
            'kode_sk' => $pengajuan->kode_pengajuan,
            'token' => $token,
        ]);

        $this->get("/submissions/{$pengajuan->id}/verification?{$query}")
            ->assertOk();
        $this->get("/submissions/{$pengajuan->id}/documents?{$query}")
            ->assertOk();
        $this->get("/submissions/{$pengajuan->id}?{$query}")
            ->assertOk();

        $invalidQuery = http_build_query([
            'kode_sk' => $pengajuan->kode_pengajuan,
            'token' => 'invalid-token',
        ]);

        $this->get("/submissions/{$pengajuan->id}/verification?{$invalidQuery}")
            ->assertForbidden();
        $this->get("/submissions/{$pengajuan->id}/documents?{$invalidQuery}")
            ->assertForbidden();
        $this->get("/submissions/{$pengajuan->id}?{$invalidQuery}")
            ->assertForbidden();
    }

    public function test_token_only_changes_through_an_explicit_reset(): void
    {
        $pengajuan = $this->createRevisionSubmission();
        $originalToken = $pengajuan->getOrCreateRevisionAccessToken();

        $this->assertSame(
            $originalToken,
            $pengajuan->fresh()->getOrCreateRevisionAccessToken()
        );

        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('b', 32)),
        ]);

        $this->assertFalse(
            $pengajuan->fresh()->hasValidRevisionAccessToken($originalToken)
        );

        $replacementToken = $pengajuan->resetRevisionAccessToken();

        $this->assertNotSame($originalToken, $replacementToken);
        $this->get($this->revisionUrl($pengajuan, $originalToken))
            ->assertForbidden();
        $this->get($this->revisionUrl($pengajuan, $replacementToken))
            ->assertOk();
    }

    public function test_revision_post_rejects_missing_and_invalid_tokens(): void
    {
        $pengajuan = $this->createRevisionSubmission();
        $pengajuan->getOrCreateRevisionAccessToken();
        $payload = ['revisi_field' => ['judul_karya_tulis' => 'Judul Baru']];

        $this->post(route('tracking.proses', $pengajuan->kode_pengajuan), $payload)
            ->assertForbidden();
        $this->post($this->revisionUrl($pengajuan, 'invalid-token'), $payload)
            ->assertForbidden();

        $this->assertSame('Judul Lama', $pengajuan->fresh()->judul_karya_tulis);
    }

    public function test_valid_revision_post_succeeds_without_changing_submission_code(): void
    {
        $pengajuan = $this->createRevisionSubmission();
        $originalCode = $pengajuan->kode_pengajuan;
        $token = $pengajuan->getOrCreateRevisionAccessToken();

        $this->post($this->revisionUrl($pengajuan, $token), [
            'revisi_field' => ['judul_karya_tulis' => 'Judul Baru'],
        ])->assertRedirect($this->revisionUrl($pengajuan, $token));

        $pengajuan->refresh();

        $this->assertSame('Judul Baru', $pengajuan->judul_karya_tulis);
        $this->assertSame('REVISI_DIKIRIM', $pengajuan->status);
        $this->assertSame($originalCode, $pengajuan->kode_pengajuan);
        $this->assertMatchesRegularExpression('/^YDS-\d{4}-\d{4}$/', $pengajuan->kode_pengajuan);
        $this->assertDatabaseHas('riwayat_revisi', [
            'pengajuan_id' => $pengajuan->id,
            'field_key' => 'judul_karya_tulis',
            'nilai_baru' => 'Judul Baru',
        ]);
    }

    public function test_revision_token_migration_can_be_applied_and_rolled_back(): void
    {
        Schema::table('pengajuan_yudisium', function (Blueprint $table): void {
            $table->dropColumn([
                'revision_access_token_hash',
                'revision_access_token_nonce',
            ]);
        });

        $migration = require database_path(
            'migrations/2026_09_28_000000_add_revision_access_token_hash_to_pengajuan_yudisium_table.php'
        );

        $migration->up();

        $this->assertTrue(Schema::hasColumns('pengajuan_yudisium', [
            'revision_access_token_hash',
            'revision_access_token_nonce',
        ]));

        $migration->down();

        $this->assertFalse(Schema::hasColumn(
            'pengajuan_yudisium',
            'revision_access_token_hash'
        ));
        $this->assertFalse(Schema::hasColumn(
            'pengajuan_yudisium',
            'revision_access_token_nonce'
        ));
    }

    private function createRevisionSubmission(int $sequence = 1): PengajuanYudisium
    {
        $nim = '230111'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
        $code = 'YDS-2026-'.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

        Mahasiswa::query()->create([
            'nim' => $nim,
            'nama_lengkap' => "Mahasiswa {$sequence}",
            'email' => "mahasiswa{$sequence}@example.test",
            'no_whatsapp' => '081234567890',
            'tahun_angkatan' => 2023,
            'jalur_masuk' => 'REGULER',
            'jurusan' => 'MANAJEMEN',
        ]);

        $pengajuan = PengajuanYudisium::query()->create([
            'nim' => $nim,
            'kode_pengajuan' => $code,
            'karya_tulis' => 'SKRIPSI',
            'judul_karya_tulis' => 'Judul Lama',
            'tanggal_ujian' => '2026-09-01',
            'nilai_angka' => 85,
            'nilai_huruf' => 'A',
            'status' => 'PERLU_REVISI',
            'submitted_at' => now(),
        ]);

        ValidasiField::query()->create([
            'pengajuan_id' => $pengajuan->id,
            'field_key' => 'judul_karya_tulis',
            'status_validasi' => 'REVISI',
            'feedback' => 'Perbaiki judul.',
        ]);

        return $pengajuan;
    }

    private function revisionUrl(PengajuanYudisium $pengajuan, string $token): string
    {
        return route('tracking.revisi', [
            'kode_pengajuan' => $pengajuan->kode_pengajuan,
            'token' => $token,
        ], false);
    }

    private function tokenFromUrl(string $url): string
    {
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        return (string) ($query['token'] ?? '');
    }
}
