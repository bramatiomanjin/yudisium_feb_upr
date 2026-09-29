<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminWorkflowUxTest extends TestCase
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
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_super_admin_dashboard_shows_real_pending_accounts_and_backup_action(): void
    {
        $superAdmin = $this->createUser('Super Admin', 'super@example.test', 'SUPER_ADMIN');
        $this->createUser('Admin Pending Nyata', 'pending.nyata@example.test', 'ADMIN', 'PENDING');
        $this->createUser('Admin Aktif', 'active@example.test');

        $this->actingAs($superAdmin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Permintaan Akun Admin')
            ->assertSee('Admin Pending Nyata')
            ->assertSee('pending.nyata@example.test')
            ->assertDontSee('Admin Aktif')
            ->assertDontSee('Budi Santoso')
            ->assertDontSee('Rina Marlina')
            ->assertSee(route('admin.backup-dokumen'));
    }

    public function test_regular_admin_dashboard_hides_super_admin_requests_and_backup_action(): void
    {
        $admin = $this->createUser('Admin Biasa', 'admin@example.test');
        $this->createUser('Admin Pending', 'pending@example.test', 'ADMIN', 'PENDING');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Permintaan Akun Admin')
            ->assertDontSee('pending@example.test')
            ->assertDontSee(route('admin.backup-dokumen'));
    }

    public function test_revision_queues_and_admin_data_states_are_explicit(): void
    {
        $dashboardView = file_get_contents(resource_path('views/admin/dashboard.blade.php'));
        $submissionView = file_get_contents(resource_path('views/admin/pengajuan.blade.php'));
        $historyView = file_get_contents(resource_path('views/admin/history.blade.php'));
        $dashboardScript = file_get_contents(public_path('js/admin_dashboard.js'));
        $submissionScript = file_get_contents(public_path('js/admin_pengajuan.js'));
        $historyScript = file_get_contents(public_path('js/admin_history.js'));

        $this->assertStringContainsString('Menunggu Perbaikan Mahasiswa', $dashboardView);
        $this->assertStringContainsString('Revisi Siap Direview', $dashboardView);
        $this->assertStringContainsString('filter=menunggu-revisi', $dashboardView);
        $this->assertStringContainsString('filter=review-revisi', $dashboardView);
        $this->assertStringContainsString('"menunggu-revisi"', $submissionScript);
        $this->assertStringContainsString('"review-revisi"', $submissionScript);

        foreach ([$dashboardView, $submissionView, $historyView] as $view) {
            $this->assertStringContainsString('Coba Lagi', $view);
        }

        $this->assertStringContainsString('showDashboardState("loading")', $dashboardScript);
        $this->assertStringContainsString('showDashboardState("error")', $dashboardScript);
        $this->assertStringContainsString('showSubmissionState("loading")', $submissionScript);
        $this->assertStringContainsString('showSubmissionState("error")', $submissionScript);
        $this->assertStringContainsString('showHistoryState("loading")', $historyScript);
        $this->assertStringContainsString('showHistoryState("error")', $historyScript);
    }

    public function test_detail_actions_and_bulk_sk_confirmation_use_clear_legal_transitions(): void
    {
        $detailScript = file_get_contents(public_path('js/admin_detail.js'));
        $submissionScript = file_get_contents(public_path('js/admin_pengajuan.js'));
        $submissionView = file_get_contents(resource_path('views/admin/pengajuan.blade.php'));

        foreach ([
            'Verifikasi Pengajuan',
            'Menunggu Perbaikan Mahasiswa',
            'Review Revisi',
            'Proses SK',
        ] as $wording) {
            $this->assertStringContainsString($wording, $detailScript);
        }

        $this->assertStringContainsString('const SK_FLOW = [', $submissionScript);
        $this->assertStringContainsString('isLegalSkTransition', $submissionScript);
        $this->assertStringContainsString('targetIndex > currentIndex', $submissionScript);
        $this->assertStringNotContainsString('Loncat ke progres berikutnya', $submissionView);
        $this->assertStringContainsString('bulkConfirmationCount', $submissionView);
        $this->assertStringContainsString('bulkTransitionSummary', $submissionView);
        $this->assertStringContainsString('Mahasiswa akan menerima pembaruan status dan email', $submissionView);
    }

    private function createUser(
        string $name,
        string $email,
        string $role = 'ADMIN',
        string $status = 'ACTIVE'
    ): User {
        return User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
            'status' => $status,
        ]);
    }
}
