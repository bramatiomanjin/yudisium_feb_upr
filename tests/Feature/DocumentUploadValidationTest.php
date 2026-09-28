<?php

namespace Tests\Feature;

use App\Models\JenisDokumen;
use App\Models\Mahasiswa;
use App\Models\PengajuanDokumen;
use App\Models\PengajuanYudisium;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentUploadValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
        ]);

        Storage::fake('local');

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
            $table->string('jurusan')->nullable();
            $table->boolean('wajib')->default(true);
            $table->unsignedInteger('max_size_mb')->default(1);
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

    public function test_initial_submission_rejects_a_missing_required_file(): void
    {
        $this->createDocumentType();

        $this->postJson(route('pengajuan.store'), $this->initialPayload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('form_yudisium');

        $this->assertDatabaseCount('pengajuan_yudisium', 0);
    }

    public function test_initial_submission_rejects_a_wrong_extension(): void
    {
        $this->createDocumentType();

        $this->postJson(route('pengajuan.store'), $this->initialPayload([
            'form_yudisium' => $this->pdfFile('document.txt'),
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors('form_yudisium');
    }

    public function test_initial_submission_rejects_a_wrong_mime_type(): void
    {
        $this->createDocumentType();

        $this->postJson(route('pengajuan.store'), $this->initialPayload([
            'form_yudisium' => $this->pngFile('document.pdf'),
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors('form_yudisium');
    }

    public function test_initial_submission_rejects_an_oversize_file(): void
    {
        $this->createDocumentType();

        $this->postJson(route('pengajuan.store'), $this->initialPayload([
            'form_yudisium' => $this->pdfFile('document.pdf', 2049),
        ]))->assertUnprocessable()
            ->assertJsonValidationErrors('form_yudisium');
    }

    public function test_initial_submission_accepts_a_valid_file_using_the_database_size_limit(): void
    {
        $documentType = $this->createDocumentType();

        $this->postJson(route('pengajuan.store'), $this->initialPayload([
            'form_yudisium' => $this->pdfFile('document.pdf', 1536),
        ]))->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('pengajuan_dokumen', [
            'jenis_dokumen_id' => $documentType->id,
            'nama_file_asli' => 'document.pdf',
            'status_validasi' => 'PENDING',
        ]);
    }

    public function test_revision_submission_rejects_a_missing_required_file(): void
    {
        [$pengajuan, $document] = $this->createRevisionSubmission();

        $this->postRevision($pengajuan, $document)
            ->assertUnprocessable()
            ->assertJsonValidationErrors("revisi_dokumen.{$document->id}");
    }

    public function test_revision_submission_rejects_a_wrong_extension(): void
    {
        [$pengajuan, $document] = $this->createRevisionSubmission();

        $this->postRevision($pengajuan, $document, $this->pdfFile('document.txt'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors("revisi_dokumen.{$document->id}");
    }

    public function test_revision_submission_rejects_a_wrong_mime_type(): void
    {
        [$pengajuan, $document] = $this->createRevisionSubmission();

        $this->postRevision($pengajuan, $document, $this->pngFile('document.pdf'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors("revisi_dokumen.{$document->id}");
    }

    public function test_revision_submission_rejects_an_oversize_file(): void
    {
        [$pengajuan, $document] = $this->createRevisionSubmission();

        $this->postRevision($pengajuan, $document, $this->pdfFile('document.pdf', 2049))
            ->assertUnprocessable()
            ->assertJsonValidationErrors("revisi_dokumen.{$document->id}");
    }

    public function test_revision_submission_accepts_a_valid_file_using_the_database_size_limit(): void
    {
        [$pengajuan, $document] = $this->createRevisionSubmission();

        $this->postRevision($pengajuan, $document, $this->pdfFile('replacement.pdf', 1536))
            ->assertRedirect();

        $this->assertDatabaseHas('pengajuan_dokumen', [
            'id' => $document->id,
            'nama_file_asli' => 'replacement.pdf',
            'status_validasi' => 'PENDING',
        ]);
        $this->assertSame('REVISI_DIKIRIM', $pengajuan->fresh()->status);
    }

    private function createDocumentType(): JenisDokumen
    {
        return JenisDokumen::query()->create([
            'kode' => 'FORM_YUDISIUM',
            'nama_dokumen' => 'Form Yudisium',
            'jurusan' => null,
            'wajib' => true,
            'max_size_mb' => 2,
            'allowed_extensions' => 'pdf',
            'is_active' => true,
        ]);
    }

    private function createRevisionSubmission(): array
    {
        $documentType = $this->createDocumentType();

        Mahasiswa::query()->create([
            'nim' => '2301110001',
            'nama_lengkap' => 'Mahasiswa Test',
            'email' => 'mahasiswa@example.test',
            'no_whatsapp' => '081234567890',
            'tahun_angkatan' => 2023,
            'jalur_masuk' => 'REGULER',
            'jurusan' => 'MANAJEMEN',
        ]);

        $pengajuan = PengajuanYudisium::query()->create([
            'nim' => '2301110001',
            'kode_pengajuan' => 'YDS-2026-0001',
            'karya_tulis' => 'SKRIPSI',
            'judul_karya_tulis' => 'Judul Test',
            'tanggal_ujian' => '2026-09-01',
            'nilai_angka' => 85,
            'nilai_huruf' => 'A',
            'status' => 'PERLU_REVISI',
            'submitted_at' => now(),
        ]);

        $document = PengajuanDokumen::query()->create([
            'pengajuan_id' => $pengajuan->id,
            'jenis_dokumen_id' => $documentType->id,
            'nama_file_asli' => 'old.pdf',
            'nama_file_storage' => 'old.pdf',
            'file_path' => 'private/yudisium/2301110001/old.pdf',
            'mime_type' => 'application/pdf',
            'ukuran_file' => 1024,
            'status_validasi' => 'REVISI',
            'feedback' => 'Unggah ulang dokumen.',
        ]);

        return [$pengajuan, $document];
    }

    private function initialPayload(array $overrides = []): array
    {
        return array_merge([
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
        ], $overrides);
    }

    private function postRevision(
        PengajuanYudisium $pengajuan,
        PengajuanDokumen $document,
        ?UploadedFile $file = null
    ) {
        $payload = [];

        if ($file !== null) {
            $payload['revisi_dokumen'] = [$document->id => $file];
        }

        return $this->withHeader('Accept', 'application/json')->post(
            route('tracking.proses', [
                'kode_pengajuan' => $pengajuan->kode_pengajuan,
                'token' => $pengajuan->getOrCreateRevisionAccessToken(),
            ]),
            $payload
        );
    }

    private function pdfFile(string $name, int $kilobytes = 1): UploadedFile
    {
        $header = "%PDF-1.4\n";
        $trailer = "\n%%EOF";
        $contentLength = max(0, ($kilobytes * 1024) - strlen($header) - strlen($trailer));

        return UploadedFile::fake()->createWithContent(
            $name,
            $header.str_repeat('0', $contentLength).$trailer
        );
    }

    private function pngFile(string $name): UploadedFile
    {
        return UploadedFile::fake()->create($name, 1, 'image/png');
    }
}
