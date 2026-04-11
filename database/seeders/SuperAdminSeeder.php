<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\SuperAdmin;
use App\Models\Plan;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        if (!SuperAdmin::where('email', 'superadmin@crewrent.in')->exists()) {
            SuperAdmin::create([
                'name'     => 'Super Admin',
                'email'    => 'superadmin@crewrent.in',
                'password' => Hash::make('SuperAdmin@123'),
            ]);
        }

        $plans = [
            ['name' => 'Starter',      'price' => 999,  'max_orders' => 30,  'max_invoices' => 50,  'max_users' => 2],
            ['name' => 'Professional', 'price' => 2499, 'max_orders' => 100, 'max_invoices' => 200, 'max_users' => 5],
            ['name' => 'Enterprise',   'price' => 4999, 'max_orders' => 500, 'max_invoices' => 999, 'max_users' => 20],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(['name' => $plan['name']], $plan);
        }
    }
}
