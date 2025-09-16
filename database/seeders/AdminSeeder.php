<?php

namespace Database\Seeders;

use App\Enums\Admin\AdminRoleEnum;
use App\Enums\Admin\AdminStatusEnum;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        Admin::query()->firstOrCreate(
            ['email' => 'admin@mamchef.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@mamchef.com',
                'password' => Hash::make('$$mamchef2025@@'),
                'status' => AdminStatusEnum::ACTIVE->value,
                'role' => AdminRoleEnum::SUPER_ADMIN->value
            ]
        );


        $this->command->info('Admin users created successfully!');
        $this->command->line('Default Admin: admin@mamchef.com / password123');
        $this->command->line('Support Admin: support@mamchef.com / support123');
    }
}
