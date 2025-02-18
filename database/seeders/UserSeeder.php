<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::create([
            "name" => "Super Admin",
            "email" => "super@example.com",
            "phone" => "0123456789",
            "address" => "Dhaka",
            "gender" => "male",
            "birth_date" => "2000-01-01",
            "email_verified_at" => now(),
            "password" => '12345678',

        ]);
        $superAdmin->assignRole('super admin');

        $admin = User::create([
            "name" => "Admin",
            "email" => "admin@example.com",
            "phone" => "0123456789",
            "address" => "Dhaka",
            "gender" => "male",
            "birth_date" => "2000-01-01",
            "email_verified_at" => now(),
            "password" => '12345678',

        ]);
        $admin->assignRole('admin');

        $user = User::create([
            "name" => "User",
            "email" => "user@example.com",
            "phone" => "0123456789",
            "address" => "Dhaka",
            "gender" => "male",
            "birth_date" => "2000-01-01",
            "email_verified_at" => now(),
            "password" => '12345678',

        ]);
        $user->assignRole('user');
    }
}
