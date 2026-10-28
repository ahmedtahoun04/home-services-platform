<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::firstOrNew(['email' => 'admin@homely.test']);
        $admin->forceFill([
            'name' => 'Admin',
            'password' => 'Admin12345',
            'role' => User::ROLE_ADMIN,
        ])->save();

        $customer = User::firstOrNew(['email' => 'customer@homely.test']);
        $customer->forceFill([
            'name' => 'Test Customer',
            'password' => 'Customer12345',
            'role' => User::ROLE_CUSTOMER,
        ])->save();

        $this->call(ServiceSeeder::class);
    }
}
