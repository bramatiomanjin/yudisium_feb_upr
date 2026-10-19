<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserInitialTest extends TestCase
{
    public function test_super_admin_returns_sa_initial(): void
    {
        $superAdmin = new User([
            'name' => 'Super Admin FEB UPR',
            'role' => 'SUPER_ADMIN',
        ]);

        $this->assertSame('SA', $superAdmin->initial);
    }

    public function test_regular_admin_returns_first_letter_of_name(): void
    {
        $adminCiko = new User([
            'name' => 'Ciko Christian',
            'role' => 'ADMIN',
        ]);

        $adminBramatio = new User([
            'name' => 'Bramatio Manjin',
            'role' => 'ADMIN',
        ]);

        $this->assertSame('C', $adminCiko->initial);
        $this->assertSame('B', $adminBramatio->initial);
    }

    public function test_fallback_initial_when_name_is_empty(): void
    {
        $admin = new User([
            'name' => '',
            'role' => 'ADMIN',
        ]);

        $this->assertSame('A', $admin->initial);
    }
}
