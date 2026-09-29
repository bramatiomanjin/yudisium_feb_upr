<?php

namespace Tests\Feature;

use App\Enums\PengajuanStatus;
use App\Models\JenisDokumen;
use App\Models\Mahasiswa;
use App\Models\PengajuanDokumen;
use App\Models\PengajuanYudisium;
use App\Models\RiwayatRevisi;
use App\Models\User;
use App\Models\ValidasiField;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Tests\TestCase;
use ZipArchive;

class ReleaseRegressionCoverageTest extends TestCase
{
    /** @var array<int, string> */
    private array $generatedDownloads = [];

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2026-09-29 10:00:00');
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
            $table->string('nama_file_asli');
            $table->string('nama_file_storage');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('ukuran_file')->nullable();
            $table->string('status_validasi')->default('PENDING');
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
        foreach ($this->generatedDownloads as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        Schema::dropIfExists('riwayat_status');
        Schema::dropIfExists('riwayat_revisi');
        Schema::dropIfExists('pengajuan_dokumen');
        Schema::dropIfExists('validasi_field');
        Schema::dropIfExists('jenis_dokumen');
        Schema::dropIfExists('pengajuan_yudisium');
        Schema::dropIfExists('mahasiswa');
        Schema::dropIfExists('users');
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_admin_can_review_a_valid_revision_and_complete_verification(): void
    {
        Notification::fake();
        $admin = $this->createUser('ADMIN');
        $submission = $this->createSubmission(PengajuanStatus::REVISI_DIKIRIM);
        $field = ValidasiField::query()->create([
            'pengajuan_id' => $submission->id,
            'field_key' => 'judul_karya_tulis',
            'status_validasi' => 'PENDING',
        ]);
        RiwayatRevisi::query()->create([
            'pengajuan_id' => $submission->id,
            'jenis_revisi' => 'FIELD',
            'field_key' => 'judul_karya_tulis',
            'nilai_lama' => 'Judul Lama',
            'nilai_baru' => 'Judul yang Sudah Diperbaiki',
            'revisi_ke' => 1,
        ]);

        $this->actingAs($admin)
            ->postJson("/submissions/{$submission->id}/revision/review", [
                'items' => [[
                    'type' => 'field',
                    'key' => 'judul_karya_tulis',
                    'decision' => 'approved',
                ]],
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', 'terverifikasi');

        $this->assertSame('DISETUJUI', $field->fresh()->status_validasi);
        $this->assertSame($admin->id, $field->fresh()->checked_by);
        $this->assertSame(PengajuanStatus::TERVERIFIKASI->value, $submission->fresh()->status);
        $this->assertNotNull($submission->fresh()->verified_at);
        $this->assertDatabaseHas('riwayat_status', [
            'pengajuan_id' => $submission->id,
            'status' => PengajuanStatus::TERVERIFIKASI->value,
            'changed_by' => $admin->id,
        ]);
    }

    public function test_invalid_revision_review_request_is_rejected_without_changing_state(): void
    {
        $admin = $this->createUser('ADMIN');
        $submission = $this->createSubmission(PengajuanStatus::REVISI_DIKIRIM);
        $field = ValidasiField::query()->create([
            'pengajuan_id' => $submission->id,
            'field_key' => 'judul_karya_tulis',
            'status_validasi' => 'PENDING',
        ]);

        $this->actingAs($admin)
            ->postJson("/submissions/{$submission->id}/revision/review", [
                'items' => [[
                    'type' => 'field',
                    'key' => 'judul_karya_tulis',
                    'decision' => 'invalid-decision',
                ]],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('items.0.decision');

        $this->assertSame('PENDING', $field->fresh()->status_validasi);
        $this->assertSame(PengajuanStatus::REVISI_DIKIRIM->value, $submission->fresh()->status);
        $this->assertDatabaseCount('riwayat_status', 0);
    }

    public function test_excel_export_contains_submission_data_and_uses_submitted_at_safely(): void
    {
        $admin = $this->createUser('ADMIN');
        $submission = $this->createSubmission(
            PengajuanStatus::TERVERIFIKASI,
            '=HYPERLINK("https://evil.example", "Mahasiswa")',
            '+CMD|\' /C calc\'!A0'
        );
        $submission->forceFill([
            'submitted_at' => '2026-09-15 08:30:00',
            'created_at' => '2026-09-01 07:00:00',
            'verified_at' => '2026-09-20 09:00:00',
        ])->saveQuietly();

        $response = $this->actingAs($admin)
            ->get(route('admin.export-yudisium'))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $path = $response->baseResponse->getFile()->getPathname();
        $this->generatedDownloads[] = $path;
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertSame($submission->kode_pengajuan, $sheet->getCell('B6')->getValue());
        $this->assertSame($submission->nim, $sheet->getCell('C6')->getValue());
        $this->assertSame($submission->mahasiswa->nama_lengkap, $sheet->getCell('D6')->getValue());
        $this->assertSame($submission->judul_karya_tulis, $sheet->getCell('H6')->getValue());
        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('D6')->getDataType());
        $this->assertSame(DataType::TYPE_STRING, $sheet->getCell('H6')->getDataType());
        $this->assertSame(
            '2026-09-15',
            Date::excelToDateTimeObject((float) $sheet->getCell('M6')->getValue())->format('Y-m-d')
        );
    }

    public function test_backup_zip_contains_the_expected_document_without_modifying_source(): void
    {
        Storage::fake('local');
        $superAdmin = $this->createUser('SUPER_ADMIN');
        $submission = $this->createSubmission(PengajuanStatus::TERVERIFIKASI, 'Mahasiswa Uji');
        $documentType = JenisDokumen::query()->create([
            'kode' => 'TRANSKRIP_NILAI',
            'nama_dokumen' => 'Transkrip Nilai',
            'wajib' => true,
            'max_size_mb' => 2,
            'allowed_extensions' => 'pdf',
            'is_active' => true,
        ]);
        $sourcePath = 'dokumen_yudisium/'.$submission->kode_pengajuan.'/transkrip.pdf';
        $sourceContents = '%PDF-1.4 regression backup contents';
        Storage::disk('local')->put($sourcePath, $sourceContents);

        PengajuanDokumen::query()->create([
            'pengajuan_id' => $submission->id,
            'jenis_dokumen_id' => $documentType->id,
            'nama_file_asli' => 'transkrip.pdf',
            'nama_file_storage' => 'transkrip.pdf',
            'file_path' => $sourcePath,
            'mime_type' => 'application/pdf',
            'ukuran_file' => strlen($sourceContents),
            'status_validasi' => 'DISETUJUI',
        ]);

        $response = $this->actingAs($superAdmin)
            ->get(route('admin.backup-dokumen'))
            ->assertOk()
            ->assertHeader('content-type', 'application/zip');

        $zipPath = $response->baseResponse->getFile()->getPathname();
        $this->generatedDownloads[] = $zipPath;
        $zip = new ZipArchive();
        $this->assertTrue($zip->open($zipPath) === true);

        $entry = 'Yudisium/Mahasiswa Uji - '.$submission->nim.'/Transkrip Nilai.pdf';
        $this->assertNotFalse($zip->locateName($entry));
        $this->assertSame($sourceContents, $zip->getFromName($entry));
        $zip->close();

        Storage::disk('local')->assertExists($sourcePath);
        $this->assertSame($sourceContents, Storage::disk('local')->get($sourcePath));
    }

    private function createUser(string $role): User
    {
        return User::query()->create([
            'name' => $role.' Test',
            'email' => strtolower($role).'@example.test',
            'password' => 'password',
            'role' => $role,
            'status' => 'ACTIVE',
        ]);
    }

    private function createSubmission(
        PengajuanStatus $status,
        string $studentName = 'Mahasiswa Test',
        string $title = 'Judul Test'
    ): PengajuanYudisium {
        $nim = '2301110001';

        Mahasiswa::query()->create([
            'nim' => $nim,
            'nama_lengkap' => $studentName,
            'email' => 'mahasiswa@example.test',
            'no_whatsapp' => '081234567890',
            'tahun_angkatan' => 2023,
            'jalur_masuk' => 'REGULER',
            'jurusan' => 'MANAJEMEN',
        ]);

        return PengajuanYudisium::query()->create([
            'kode_pengajuan' => 'YDS-2026-0001',
            'nim' => $nim,
            'karya_tulis' => 'SKRIPSI',
            'judul_karya_tulis' => $title,
            'tanggal_ujian' => '2026-09-10',
            'nilai_angka' => 85,
            'nilai_huruf' => 'A',
            'status' => $status->value,
            'submitted_at' => '2026-09-15 08:30:00',
        ]);
    }
}
