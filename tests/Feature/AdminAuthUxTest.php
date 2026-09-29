<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminAuthUxTest extends TestCase
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

    public function test_login_and_registration_consistently_use_email_instead_of_username(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Email')
            ->assertDontSee('Username')
            ->assertSee('Lupa password? Hubungi Super Admin atau Bagian IT.');

        $this->get('/admin/register')
            ->assertOk()
            ->assertSee('Email digunakan untuk login.')
            ->assertDontSee('Buat username');
    }

    public function test_login_validation_errors_are_clear_and_email_input_is_preserved(): void
    {
        $this->followingRedirects()
            ->from('/admin/login')
            ->post('/admin/login', [
                'email' => 'alamat-tidak-valid',
                'password' => '',
            ])
            ->assertOk()
            ->assertSee('Login belum berhasil.')
            ->assertSee('Masukkan alamat email yang valid.')
            ->assertSee('Password wajib diisi.')
            ->assertSee('value="alamat-tidak-valid"', false);
    }

    public function test_failed_and_inactive_login_messages_are_safe_and_actionable(): void
    {
        $this->from('/admin/login')
            ->post('/admin/login', [
                'email' => 'unknown@example.test',
                'password' => 'wrong-password',
            ])
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors([
                'email' => 'Email atau password tidak sesuai.',
            ])
            ->assertSessionHasInput('email', 'unknown@example.test');

        User::query()->create([
            'name' => 'Admin Pending',
            'email' => 'pending@example.test',
            'password' => 'password123',
            'role' => 'ADMIN',
            'status' => 'PENDING',
        ]);

        $this->from('/admin/login')
            ->post('/admin/login', [
                'email' => 'pending@example.test',
                'password' => 'password123',
            ])
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors([
                'email' => 'Akun sedang menunggu persetujuan Super Admin.',
            ]);

        User::query()->create([
            'name' => 'Admin Inactive',
            'email' => 'inactive@example.test',
            'password' => 'password123',
            'role' => 'ADMIN',
            'status' => 'INACTIVE',
        ]);

        $this->from('/admin/login')
            ->post('/admin/login', [
                'email' => 'inactive@example.test',
                'password' => 'password123',
            ])
            ->assertRedirect('/admin/login')
            ->assertSessionHasErrors([
                'email' => 'Akun tidak aktif. Silakan hubungi Super Admin atau Bagian IT.',
            ]);
    }

    public function test_registration_shows_field_errors_and_preserves_safe_input(): void
    {
        $this->followingRedirects()
            ->from('/admin/register')
            ->post('/admin/register', [
                'name' => '',
                'email' => 'alamat-tidak-valid',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertOk()
            ->assertSee('Registrasi belum berhasil.')
            ->assertSee('Nama lengkap wajib diisi.')
            ->assertSee('Masukkan alamat email yang valid.')
            ->assertSee('Password minimal 8 karakter.')
            ->assertSee('value="alamat-tidak-valid"', false);
    }

    public function test_successful_registration_displays_pending_approval_message_on_login(): void
    {
        $this->post('/admin/register', [
            'name' => 'Admin Baru',
            'email' => 'admin.baru@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/admin/login');

        $this->assertDatabaseHas('users', [
            'email' => 'admin.baru@example.test',
            'role' => 'ADMIN',
            'status' => 'PENDING',
        ]);

        $this->get('/admin/login')
            ->assertOk()
            ->assertSee('Akun berhasil dibuat dan sedang menunggu persetujuan Super Admin.');
    }

    public function test_web_login_rate_limit_returns_to_form_with_feedback_and_email(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.77']);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->followingRedirects()
                ->from('/admin/login')
                ->post('/admin/login', [
                    'email' => 'web-limited@example.test',
                    'password' => 'wrong-password',
                ])
                ->assertOk();
        }

        $this->followingRedirects()
            ->from('/admin/login')
            ->post('/admin/login', [
                'email' => 'web-limited@example.test',
                'password' => 'wrong-password',
            ])
            ->assertOk()
            ->assertSee('Terlalu banyak percobaan login. Silakan coba lagi sebentar.')
            ->assertSee('value="web-limited@example.test"', false);
    }
}
