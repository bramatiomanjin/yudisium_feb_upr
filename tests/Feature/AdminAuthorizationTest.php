<?php

namespace Tests\Feature;

use App\Models\Mahasiswa;
use App\Models\PengajuanYudisium;
use App\Models\RiwayatStatus;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
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
            $table->timestamps();
        });

        Schema::create('jenis_dokumen', function (Blueprint $table): void {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama_dokumen');
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
        Schema::dropIfExists('validasi_field');
        Schema::dropIfExists('jenis_dokumen');
        Schema::dropIfExists('pengajuan_yudisium');
        Schema::dropIfExists('mahasiswa');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_an_inactive_account_cannot_keep_using_an_existing_session(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        User::query()->whereKey($admin->id)->update(['status' => 'INACTIVE']);

        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_an_inactive_session_is_revoked_before_an_authenticated_public_data_bypass(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        User::query()->whereKey($admin->id)->update(['status' => 'INACTIVE']);

        $this->getJson('/submissions/999')
            ->assertForbidden()
            ->assertJsonPath('message', 'Akun tidak aktif.');

        $this->assertGuest();
    }

    public function test_a_regular_admin_is_forbidden_from_every_super_admin_endpoint(): void
    {
        $admin = $this->createUser('ADMIN');
        $target = $this->createUser('ADMIN', 'PENDING', 2);
        $this->actingAs($admin);

        $getEndpoints = [
            '/admin/pengaturan-dokumen',
            '/admin/backup-dokumen',
            '/superadmin/kelola-admin',
            '/superadmin/log-aktivitas',
        ];

        foreach ($getEndpoints as $endpoint) {
            $this->get($endpoint)->assertForbidden();
        }

        $postEndpoints = [
            '/admin/pengaturan-dokumen',
            '/superadmin/tambah-admin',
            "/superadmin/hapus-admin/{$target->id}",
            "/superadmin/approve-admin/{$target->id}",
            "/superadmin/reject-admin/{$target->id}",
            "/superadmin/activate-admin/{$target->id}",
        ];

        foreach ($postEndpoints as $endpoint) {
            $this->post($endpoint)->assertForbidden();
        }

        $this->assertSame('PENDING', $target->fresh()->status);
    }

    public function test_a_regular_admin_can_still_use_operational_routes(): void
    {
        $admin = $this->createUser('ADMIN');
        $this->actingAs($admin);

        $this->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.pengajuan'))->assertOk();
        $this->getJson('/submissions')->assertOk()->assertExactJson([]);
        $this->get('/admin/verifikasi')->assertOk();
        $this->get('/admin/review-revisi')->assertOk();
        $this->get('/admin/proses-sk')->assertOk();
    }

    public function test_only_a_super_admin_receives_global_activity_history(): void
    {
        $adminA = $this->createUser('ADMIN', 'ACTIVE', 1);
        $adminB = $this->createUser('ADMIN', 'ACTIVE', 2);
        $superAdmin = $this->createUser('SUPER_ADMIN', 'ACTIVE', 3);
        $pengajuan = $this->createSubmission();

        $this->createHistory($pengajuan, $adminA, 'TERVERIFIKASI');
        $this->createHistory($pengajuan, $adminB, 'PEMBUATAN_SK');

        $this->actingAs($adminA)
            ->getJson('/history')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.adminName', $adminA->name);

        $this->actingAs($superAdmin)
            ->getJson('/history')
            ->assertOk()
            ->assertJsonCount(2);
    }

    public function test_a_super_admin_can_use_privileged_management_routes(): void
    {
        $superAdmin = $this->createUser('SUPER_ADMIN');
        $pendingAdmin = $this->createUser('ADMIN', 'PENDING', 2);
        $this->actingAs($superAdmin);

        $this->get('/superadmin/kelola-admin')->assertOk();
        $this->get('/superadmin/log-aktivitas')->assertOk();
        $this->get(route('admin.pengaturan-dokumen'))->assertOk();

        $this->post("/superadmin/approve-admin/{$pendingAdmin->id}")
            ->assertRedirect('/superadmin/kelola-admin');

        $this->assertSame('ACTIVE', $pendingAdmin->fresh()->status);
    }

    private function createUser(
        string $role,
        string $status = 'ACTIVE',
        int $sequence = 1
    ): User {
        return User::query()->create([
            'name' => "User {$sequence}",
            'email' => "user{$sequence}@example.test",
            'password' => 'password',
            'role' => $role,
            'status' => $status,
        ]);
    }

    private function createSubmission(): PengajuanYudisium
    {
        Mahasiswa::query()->create([
            'nim' => '2301110001',
            'nama_lengkap' => 'Mahasiswa Test',
            'email' => 'mahasiswa@example.test',
            'no_whatsapp' => '081234567890',
            'tahun_angkatan' => 2023,
            'jalur_masuk' => 'REGULER',
            'jurusan' => 'MANAJEMEN',
        ]);

        return PengajuanYudisium::query()->create([
            'kode_pengajuan' => 'YDS-2026-0001',
            'nim' => '2301110001',
            'karya_tulis' => 'SKRIPSI',
            'judul_karya_tulis' => 'Judul Test',
            'tanggal_ujian' => '2026-09-01',
            'nilai_angka' => 85,
            'nilai_huruf' => 'A',
            'status' => 'TERVERIFIKASI',
        ]);
    }

    private function createHistory(
        PengajuanYudisium $pengajuan,
        User $admin,
        string $status
    ): void {
        RiwayatStatus::query()->create([
            'pengajuan_id' => $pengajuan->id,
            'status' => $status,
            'catatan' => "PROSES SK: TERVERIFIKASI → {$status}",
            'changed_by' => $admin->id,
        ]);
    }
}
