<?php

namespace Tests\Feature;

use App\Enums\PengajuanStatus;
use App\Models\Mahasiswa;
use App\Models\PengajuanYudisium;
use App\Models\User;
use App\Models\ValidasiField;
use App\Support\PengajuanCodeGenerator;
use DomainException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Tests\TestCase;

class SubmissionBusinessLogicTest extends TestCase
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
            $table->string('status_validasi');
            $table->text('feedback')->nullable();
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();
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
        Schema::dropIfExists('pengajuan_dokumen');
        Schema::dropIfExists('validasi_field');
        Schema::dropIfExists('pengajuan_code_sequences');
        Schema::dropIfExists('pengajuan_yudisium');
        Schema::dropIfExists('mahasiswa');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_the_official_status_list_rejects_legacy_and_unknown_values(): void
    {
        $this->assertSame([
            'MENUNGGU_VERIFIKASI',
            'PERLU_REVISI',
            'REVISI_DIKIRIM',
            'TERVERIFIKASI',
            'PEMBUATAN_SK',
            'PARAF_PIMPINAN',
            'TTD_DEKAN',
            'SK_SIAP_DIAMBIL',
        ], PengajuanStatus::values());

        $pengajuan = $this->createSubmission('2301110001', 'YDS-2026-0001');

        $this->expectException(InvalidArgumentException::class);

        $pengajuan->update(['status' => 'VERIFIKASI_ADMIN']);
    }

    public function test_submission_codes_use_the_highest_existing_number_and_never_reuse_a_deleted_number(): void
    {
        $this->createSubmission('2301110001', 'YDS-2026-0001');
        $highest = $this->createSubmission('2301110002', 'YDS-2026-0003');

        $generator = app(PengajuanCodeGenerator::class);

        $this->assertSame('YDS-2026-0004', $generator->generate(2026));

        $highest->delete();

        $this->assertSame('YDS-2026-0005', $generator->generate(2026));
        $this->assertDatabaseHas('pengajuan_code_sequences', [
            'year' => 2026,
            'last_number' => 5,
        ]);
    }

    public function test_code_sequence_migration_is_safe_for_existing_submissions_and_can_be_rolled_back(): void
    {
        $this->createSubmission('2301110001', 'YDS-2026-0042');
        Schema::drop('pengajuan_code_sequences');

        $migration = require database_path(
            'migrations/2026_09_29_000000_create_pengajuan_code_sequences_table.php'
        );

        $migration->up();

        $this->assertTrue(Schema::hasTable('pengajuan_code_sequences'));
        $this->assertSame('YDS-2026-0043', app(PengajuanCodeGenerator::class)->generate(2026));
        $this->assertDatabaseHas('pengajuan_yudisium', [
            'kode_pengajuan' => 'YDS-2026-0042',
        ]);

        $migration->down();

        $this->assertFalse(Schema::hasTable('pengajuan_code_sequences'));
        $this->assertDatabaseHas('pengajuan_yudisium', [
            'kode_pengajuan' => 'YDS-2026-0042',
        ]);
    }

    public function test_legacy_paraf_status_is_migrated_without_changing_the_submission(): void
    {
        Schema::drop('pengajuan_yudisium');
        Schema::create('pengajuan_yudisium', function (Blueprint $table): void {
            $table->id();
            $table->string('kode_pengajuan')->unique();
            $table->enum('status', [
                'MENUNGGU_VERIFIKASI',
                'PERLU_REVISI',
                'REVISI_DIKIRIM',
                'TERVERIFIKASI',
                'PEMBUATAN_SK',
                'TTD_WAKIL_DEKAN',
                'TTD_DEKAN',
                'SK_SIAP_DIAMBIL',
            ]);
            $table->timestamps();
        });

        $pengajuanId = DB::table('pengajuan_yudisium')->insertGetId([
            'kode_pengajuan' => 'YDS-2026-0001',
            'status' => 'TTD_WAKIL_DEKAN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $migration = require database_path(
            'migrations/2026_09_29_010000_rename_ttd_wakil_dekan_status_to_paraf_pimpinan.php'
        );

        $migration->up();

        $this->assertDatabaseHas('pengajuan_yudisium', [
            'id' => $pengajuanId,
            'kode_pengajuan' => 'YDS-2026-0001',
            'status' => 'PARAF_PIMPINAN',
        ]);

        $migration->down();

        $this->assertDatabaseHas('pengajuan_yudisium', [
            'id' => $pengajuanId,
            'kode_pengajuan' => 'YDS-2026-0001',
            'status' => 'TTD_WAKIL_DEKAN',
        ]);
    }

    public function test_partial_canonical_verification_keeps_the_official_waiting_status(): void
    {
        $admin = $this->createAdmin();
        $pengajuan = $this->createSubmission('2301110001', 'YDS-2026-0001');
        ValidasiField::query()->create([
            'pengajuan_id' => $pengajuan->id,
            'field_key' => 'nama_lengkap',
            'status_validasi' => 'PENDING',
        ]);
        ValidasiField::query()->create([
            'pengajuan_id' => $pengajuan->id,
            'field_key' => 'email',
            'status_validasi' => 'PENDING',
        ]);

        $this->actingAs($admin)
            ->postJson("/submissions/{$pengajuan->id}/verify", [
                'items' => [[
                    'type' => 'field',
                    'key' => 'nama_lengkap',
                    'decision' => 'approved',
                ]],
            ])
            ->assertOk()
            ->assertJsonPath('status', 'menunggu_verifikasi');

        $this->assertSame(
            PengajuanStatus::MENUNGGU_VERIFIKASI->value,
            $pengajuan->fresh()->status
        );
        $this->assertDatabaseMissing('pengajuan_yudisium', [
            'status' => 'VERIFIKASI_ADMIN',
        ]);
    }

    public function test_canonical_verification_completes_or_requests_revision_consistently(): void
    {
        $admin = $this->createAdmin();
        $approved = $this->createSubmission('2301110001', 'YDS-2026-0001');
        $revision = $this->createSubmission('2301110002', 'YDS-2026-0002');

        foreach ([$approved, $revision] as $pengajuan) {
            ValidasiField::query()->create([
                'pengajuan_id' => $pengajuan->id,
                'field_key' => 'nama_lengkap',
                'status_validasi' => 'PENDING',
            ]);
        }

        $this->actingAs($admin)
            ->postJson("/submissions/{$approved->id}/verify", [
                'items' => [[
                    'type' => 'field',
                    'key' => 'nama_lengkap',
                    'decision' => 'approved',
                ]],
            ])->assertOk()->assertJsonPath('status', 'terverifikasi');

        $this->actingAs($admin)
            ->postJson("/submissions/{$revision->id}/verify", [
                'items' => [[
                    'type' => 'field',
                    'key' => 'nama_lengkap',
                    'decision' => 'revision',
                    'feedback' => 'Perbaiki nama.',
                ]],
            ])->assertOk()->assertJsonPath('status', 'perlu_revisi');

        $this->assertSame(PengajuanStatus::TERVERIFIKASI->value, $approved->fresh()->status);
        $this->assertNotNull($approved->fresh()->verified_at);
        $this->assertSame(PengajuanStatus::PERLU_REVISI->value, $revision->fresh()->status);
        $this->assertNull($revision->fresh()->verified_at);
    }

    public function test_legacy_verification_endpoint_is_not_available(): void
    {
        $admin = $this->createAdmin();
        $pengajuan = $this->createSubmission('2301110001', 'YDS-2026-0001');

        $this->actingAs($admin)
            ->post("/admin/pengajuan/{$pengajuan->id}/verifikasi")
            ->assertNotFound();
    }

    public function test_status_transitions_allow_the_workflow_but_reject_backward_moves(): void
    {
        $pengajuan = $this->createSubmission('2301110001', 'YDS-2026-0001');

        $pengajuan->transitionTo(PengajuanStatus::PERLU_REVISI);
        $pengajuan->transitionTo(PengajuanStatus::REVISI_DIKIRIM);
        $pengajuan->transitionTo(PengajuanStatus::TERVERIFIKASI);
        $pengajuan->transitionTo(PengajuanStatus::PEMBUATAN_SK);
        $pengajuan->transitionTo(PengajuanStatus::PARAF_PIMPINAN);
        $pengajuan->transitionTo(PengajuanStatus::TTD_DEKAN);

        $this->assertSame(PengajuanStatus::TTD_DEKAN->value, $pengajuan->status);

        $this->expectException(DomainException::class);

        $pengajuan->transitionTo(PengajuanStatus::PEMBUATAN_SK);
    }

    public function test_frontend_uses_the_canonical_paraf_pimpinan_status(): void
    {
        $frontendFiles = [
            public_path('js/yudisium_api.js'),
            public_path('js/admin_dashboard.js'),
            public_path('js/admin_pengajuan.js'),
            public_path('js/admin_detail.js'),
            public_path('js/student_tracking.js'),
            public_path('js/admin_proses_sk.js'),
            resource_path('views/admin/pengajuan.blade.php'),
            base_path('frontend/js/yudisium_api.js'),
            base_path('frontend/js/admin_dashboard.js'),
            base_path('frontend/js/admin_pengajuan.js'),
            base_path('frontend/js/student_tracking.js'),
            base_path('frontend/js/admin_proses_sk.js'),
            base_path('frontend/admin/pengajuan.html'),
        ];

        foreach ($frontendFiles as $file) {
            $contents = file_get_contents($file);

            $this->assertStringNotContainsString('TTD_WAKIL_DEKAN', $contents, $file);
            $this->assertStringNotContainsString('ttd_wakil_dekan', $contents, $file);
        }

        $this->assertStringContainsString(
            'PARAF_PIMPINAN',
            file_get_contents(public_path('js/yudisium_api.js'))
        );
        $this->assertStringContainsString(
            'value="paraf_pimpinan"',
            file_get_contents(resource_path('views/admin/pengajuan.blade.php'))
        );
    }

    public function test_sk_endpoint_accepts_paraf_pimpinan_and_rejects_the_legacy_name(): void
    {
        $admin = $this->createAdmin();
        $canonical = $this->createSubmission('2301110001', 'YDS-2026-0001');
        $legacy = $this->createSubmission('2301110002', 'YDS-2026-0002');
        $canonical->transitionTo(PengajuanStatus::TERVERIFIKASI);
        $legacy->transitionTo(PengajuanStatus::TERVERIFIKASI);

        $this->actingAs($admin)
            ->patchJson("/submissions/{$canonical->id}/sk-status", [
                'status' => 'paraf_pimpinan',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'paraf_pimpinan');

        $this->assertSame(
            PengajuanStatus::PARAF_PIMPINAN->value,
            $canonical->fresh()->status
        );

        $this->actingAs($admin)
            ->patchJson("/submissions/{$legacy->id}/sk-status", [
                'status' => 'ttd_wakil_dekan',
            ])
            ->assertUnprocessable();

        $this->assertSame(
            PengajuanStatus::TERVERIFIKASI->value,
            $legacy->fresh()->status
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

    private function createSubmission(string $nim, string $code): PengajuanYudisium
    {
        Mahasiswa::query()->create([
            'nim' => $nim,
            'nama_lengkap' => "Mahasiswa {$nim}",
            'email' => "{$nim}@example.test",
            'no_whatsapp' => '081234567890',
            'tahun_angkatan' => 2023,
            'jalur_masuk' => 'REGULER',
            'jurusan' => 'MANAJEMEN',
        ]);

        return PengajuanYudisium::query()->create([
            'kode_pengajuan' => $code,
            'nim' => $nim,
            'karya_tulis' => 'SKRIPSI',
            'judul_karya_tulis' => 'Judul Test',
            'tanggal_ujian' => '2026-09-01',
            'nilai_angka' => 85,
            'nilai_huruf' => 'A',
            'status' => PengajuanStatus::MENUNGGU_VERIFIKASI->value,
            'submitted_at' => now(),
        ]);
    }
}
