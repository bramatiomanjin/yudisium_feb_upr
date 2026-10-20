<?php

namespace Tests\Feature;

use App\Enums\PengajuanStatus;
use App\Models\Mahasiswa;
use App\Models\PengajuanDokumen;
use App\Models\PengajuanYudisium;
use App\Models\RiwayatStatus;
use App\Models\User;
use App\Models\ValidasiField;
use App\Support\StudentYudisiumNotifier;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class CancelVerificationTest extends TestCase
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

        Schema::create('validasi_field', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pengajuan_id');
            $table->string('field_key');
            $table->string('status_validasi')->default('PENDING');
            $table->text('feedback')->nullable();
            $table->foreignId('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('pengajuan_dokumen', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pengajuan_id');
            $table->string('jenis_dokumen');
            $table->string('nama_file');
            $table->string('path_file');
            $table->string('status_validasi')->default('PENDING');
            $table->text('feedback')->nullable();
            $table->foreignId('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
        });

        Schema::create('riwayat_status', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pengajuan_id');
            $table->string('status', 50);
            $table->text('catatan')->nullable();
            $table->foreignId('changed_by')->nullable();
            $table->timestamps();
        });
    }

    private function createSuperAdmin(): User
    {
        return User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@example.test',
            'password' => bcrypt('password'),
            'role' => 'SUPER_ADMIN',
            'status' => 'ACTIVE',
        ]);
    }

    private function createAdmin(): User
    {
        return User::create([
            'name' => 'Regular Admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('password'),
            'role' => 'ADMIN',
            'status' => 'ACTIVE',
        ]);
    }

    private function createSubmission(string $status = 'TERVERIFIKASI'): PengajuanYudisium
    {
        Mahasiswa::create([
            'nim' => '233020503001',
            'nama_lengkap' => 'Budi Santoso',
            'email' => 'budi@example.test',
            'no_whatsapp' => '081234567890',
            'tahun_angkatan' => 2020,
            'jalur_masuk' => 'SNMPTN',
            'jurusan' => 'Manajemen',
        ]);

        $submission = PengajuanYudisium::create([
            'kode_pengajuan' => 'YUD-2026-0001',
            'nim' => '233020503001',
            'karya_tulis' => 'SKRIPSI',
            'judul_karya_tulis' => 'Analisis Pengaruh Manajemen terhadap Kinerja Karyawan',
            'tanggal_ujian' => '2026-08-01',
            'nilai_angka' => 88.5,
            'nilai_huruf' => 'A',
            'status' => $status,
            'submitted_at' => now()->subDays(5),
            'verified_at' => $status === 'TERVERIFIKASI' ? now()->subDay() : null,
        ]);

        ValidasiField::create([
            'pengajuan_id' => $submission->id,
            'field_key' => 'judul_karya_tulis',
            'status_validasi' => 'DISETUJUI',
        ]);

        PengajuanDokumen::create([
            'pengajuan_id' => $submission->id,
            'jenis_dokumen' => 'ijazah_terakhir',
            'nama_file' => 'ijazah.pdf',
            'path_file' => 'uploads/ijazah.pdf',
            'status_validasi' => 'DISETUJUI',
        ]);

        return $submission;
    }

    public function test_super_admin_can_cancel_verification(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $submission = $this->createSubmission('TERVERIFIKASI');

        $notifierMock = Mockery::mock(StudentYudisiumNotifier::class);
        $notifierMock->shouldReceive('verificationCancelled')
            ->once()
            ->withArgs(function ($sub, $reason) use ($submission) {
                return $sub->id === $submission->id && $reason === 'Perlu pengecekan berkas kembali';
            });
        $this->app->instance(StudentYudisiumNotifier::class, $notifierMock);

        $response = $this->actingAs($superAdmin)
            ->postJson("/submissions/{$submission->id}/cancel-verification", [
                'reason' => 'Perlu pengecekan berkas kembali',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'status' => 'menunggu_verifikasi',
            ]);

        $submission->refresh();
        $this->assertSame(PengajuanStatus::MENUNGGU_VERIFIKASI->value, $submission->status);
        $this->assertNull($submission->verified_at);

        // Dokumen & validasi field tetap DISETUJUI
        $this->assertDatabaseHas('validasi_field', [
            'pengajuan_id' => $submission->id,
            'status_validasi' => 'DISETUJUI',
        ]);
        $this->assertDatabaseHas('pengajuan_dokumen', [
            'pengajuan_id' => $submission->id,
            'status_validasi' => 'DISETUJUI',
        ]);

        // Riwayat status tercatat
        $this->assertDatabaseHas('riwayat_status', [
            'pengajuan_id' => $submission->id,
            'status' => PengajuanStatus::MENUNGGU_VERIFIKASI->value,
            'changed_by' => $superAdmin->id,
        ]);

        $riwayat = RiwayatStatus::where('pengajuan_id', $submission->id)->latest('id')->first();
        $this->assertStringContainsString('PEMBATALAN VERIFIKASI', $riwayat->catatan);
        $this->assertStringContainsString('Perlu pengecekan berkas kembali', $riwayat->catatan);
    }

    public function test_regular_admin_cannot_cancel_verification(): void
    {
        $admin = $this->createAdmin();
        $submission = $this->createSubmission('TERVERIFIKASI');

        $response = $this->actingAs($admin)
            ->postJson("/submissions/{$submission->id}/cancel-verification", [
                'reason' => 'Perlu cek ulang',
            ]);

        $response->assertForbidden();

        $submission->refresh();
        $this->assertSame(PengajuanStatus::TERVERIFIKASI->value, $submission->status);
    }

    public function test_guest_cannot_cancel_verification(): void
    {
        $submission = $this->createSubmission('TERVERIFIKASI');

        $response = $this->postJson("/submissions/{$submission->id}/cancel-verification", [
            'reason' => 'Perlu cek ulang',
        ]);

        $response->assertUnauthorized();
    }

    public function test_cannot_cancel_verification_if_not_terverifikasi(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $submission = $this->createSubmission(PengajuanStatus::PEMBUATAN_SK->value);

        $response = $this->actingAs($superAdmin)
            ->postJson("/submissions/{$submission->id}/cancel-verification", [
                'reason' => 'Coba batalkan SK',
            ]);

        $response->assertStatus(409);

        $submission->refresh();
        $this->assertSame(PengajuanStatus::PEMBUATAN_SK->value, $submission->status);
    }

    public function test_cancellation_reason_is_required_and_validated(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $submission = $this->createSubmission('TERVERIFIKASI');

        // Kosong
        $response = $this->actingAs($superAdmin)
            ->postJson("/submissions/{$submission->id}/cancel-verification", [
                'reason' => '',
            ]);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['reason']);

        // Terlalu pendek (< 3 karakter)
        $responseShort = $this->actingAs($superAdmin)
            ->postJson("/submissions/{$submission->id}/cancel-verification", [
                'reason' => 'ab',
            ]);
        $responseShort->assertStatus(422)
            ->assertJsonValidationErrors(['reason']);
    }
}
