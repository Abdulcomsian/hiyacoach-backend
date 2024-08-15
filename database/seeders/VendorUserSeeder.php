<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;
class VendorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coach =  User::create([
            'username' => 'coach',
            'name' => 'Coach',
            'email' => 'coach@coach.com',
            'password' => Hash::make('password'),
            'phone_no' => '0011223344',
            'dob' => '2024-08-12',
            'email_verified_at' => now(),
        ]);
        $coach->assignRole('coach');
    }
}
