<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email','admin@walkietrack.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@walkietrack.com',
                'password' => Hash::make('password123'),
            ]);
        }
    }
}
