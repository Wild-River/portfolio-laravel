<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class AdminUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_created_from_config_with_hashed_password(): void
    {
        config(['admin.name' => 'Rumi', 'admin.email' => 'admin@example.com', 'admin.password' => 'secret-password']);

        $this->seed(AdminUserSeeder::class);

        $admin = User::sole();
        $this->assertSame('Rumi', $admin->name);
        $this->assertSame('admin@example.com', $admin->email);
        $this->assertNotSame('secret-password', $admin->password);
        $this->assertTrue(Hash::check('secret-password', $admin->password));
    }

    public function test_seeding_twice_keeps_a_single_admin(): void
    {
        config(['admin.email' => 'admin@example.com', 'admin.password' => 'first-password']);
        $this->seed(AdminUserSeeder::class);

        config(['admin.password' => 'second-password']);
        $this->seed(AdminUserSeeder::class);

        $this->assertSame(1, User::count());
        $this->assertTrue(Hash::check('first-password', User::sole()->password));
    }

    public function test_seeder_fails_without_email(): void
    {
        config(['admin.email' => null, 'admin.password' => 'secret-password']);

        $this->expectException(RuntimeException::class);

        $this->seed(AdminUserSeeder::class);
    }

    public function test_seeder_fails_without_password(): void
    {
        config(['admin.email' => 'admin@example.com', 'admin.password' => '']);

        $this->expectException(RuntimeException::class);

        $this->seed(AdminUserSeeder::class);
    }

    public function test_admin_can_log_in_with_seeded_credentials(): void
    {
        config(['admin.email' => 'admin@example.com', 'admin.password' => 'secret-password']);
        $this->seed(AdminUserSeeder::class);

        $this->post('/login', ['email' => 'admin@example.com', 'password' => 'secret-password'])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs(User::sole());
    }
}
