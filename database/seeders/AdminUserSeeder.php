<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    /**
     * Create the admin user from config/admin.php.
     */
    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (blank($email) || blank($password)) {
            throw new RuntimeException('ADMIN_EMAIL and ADMIN_PASSWORD must be set in .env.');
        }

        User::firstOrCreate(
            ['email' => $email],
            [
                'name' => config('admin.name'),
                'password' => $password,
            ],
        );
    }
}
