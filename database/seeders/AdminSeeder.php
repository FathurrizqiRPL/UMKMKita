<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $isProduction = app()->environment('production');

        $name = $isProduction
            ? env('HOSTING_ADMIN_NAME', 'Admin Hosting')
            : env('LOCAL_ADMIN_NAME', 'Admin Localhost');

        $email = $isProduction
            ? env('HOSTING_ADMIN_EMAIL')
            : env('LOCAL_ADMIN_EMAIL', 'admin@localhost.test');

        $password = $isProduction
            ? env('HOSTING_ADMIN_PASSWORD')
            : env('LOCAL_ADMIN_PASSWORD', 'Admin12345');

        if (!$email || !$password) return;

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'has_password' => true,
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
