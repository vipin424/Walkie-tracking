<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@crewrent.in'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('admin@123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Admin user ready: admin@crewrent.in / admin@123');
    }
}
