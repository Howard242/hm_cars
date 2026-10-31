<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = config('hmcars.admin.name');
        $email = config('hmcars.admin.email');
        $password = config('hmcars.admin.password');

        if (! $email || ! $password) {
            throw new RuntimeException(
                'Set HM_CARS_ADMIN_EMAIL and HM_CARS_ADMIN_PASSWORD in .env before seeding.'
            );
        }

        $admin = User::firstOrNew(['email' => $email]);

        $admin->name = $name;
        $admin->password = $password;
        $admin->is_admin = true;
        $admin->is_staff = true;
        $admin->is_active = true;
        $admin->email_verified_at = now();

        $admin->save();
    }
}