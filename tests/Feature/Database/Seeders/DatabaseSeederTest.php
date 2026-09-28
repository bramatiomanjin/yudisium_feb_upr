<?php

namespace Tests\Feature\Database\Seeders;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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

        Schema::create('jenis_dokumen', function (Blueprint $table): void {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama_dokumen');
            $table->string('jurusan')->nullable();
            $table->boolean('wajib')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('max_size_mb')->default(1);
            $table->string('allowed_extensions')->default('pdf');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('jenis_dokumen');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    public function test_creates_an_active_super_admin_that_can_log_in(): void
    {
        config(['auth.super_admin.password' => 'InitialSecurePassword!']);

        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()
            ->where('email', 'superadmin@feb.upr.ac.id')
            ->sole();

        $this->assertSame('Super Admin FEB UPR', $superAdmin->name);
        $this->assertSame('SUPER_ADMIN', $superAdmin->role);
        $this->assertSame('ACTIVE', $superAdmin->status);
        $this->assertTrue(Hash::check('InitialSecurePassword!', $superAdmin->password));
        $this->assertTrue(Auth::attempt([
            'email' => 'superadmin@feb.upr.ac.id',
            'password' => 'InitialSecurePassword!',
        ]));
    }

    public function test_requires_a_configured_password_when_creating_the_super_admin(): void
    {
        config(['auth.super_admin.password' => null]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'SUPER_ADMIN_PASSWORD wajib diatur saat membuat akun Super Admin.'
        );

        $this->seed(DatabaseSeeder::class);
    }

    public function test_running_the_seeder_again_does_not_reset_the_existing_password(): void
    {
        config(['auth.super_admin.password' => 'InitialSecurePassword!']);
        $this->seed(DatabaseSeeder::class);

        $originalPasswordHash = User::query()
            ->where('email', 'superadmin@feb.upr.ac.id')
            ->value('password');

        config(['auth.super_admin.password' => 'ReplacementPassword!']);
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()
            ->where('email', 'superadmin@feb.upr.ac.id')
            ->sole();

        $this->assertSame(1, User::query()
            ->where('email', 'superadmin@feb.upr.ac.id')
            ->count());
        $this->assertSame($originalPasswordHash, $superAdmin->password);
        $this->assertTrue(Hash::check('InitialSecurePassword!', $superAdmin->password));
        $this->assertFalse(Hash::check('ReplacementPassword!', $superAdmin->password));
        $this->assertSame('SUPER_ADMIN', $superAdmin->role);
    }

    public function test_existing_super_admin_remains_usable_without_a_configured_password(): void
    {
        config(['auth.super_admin.password' => 'InitialSecurePassword!']);
        $this->seed(DatabaseSeeder::class);

        $originalPasswordHash = User::query()
            ->where('email', 'superadmin@feb.upr.ac.id')
            ->value('password');

        config(['auth.super_admin.password' => null]);
        $this->seed(DatabaseSeeder::class);

        $superAdmin = User::query()
            ->where('email', 'superadmin@feb.upr.ac.id')
            ->sole();

        $this->assertSame(1, User::query()
            ->where('email', 'superadmin@feb.upr.ac.id')
            ->count());
        $this->assertSame($originalPasswordHash, $superAdmin->password);
        $this->assertTrue(Hash::check('InitialSecurePassword!', $superAdmin->password));
    }
}
