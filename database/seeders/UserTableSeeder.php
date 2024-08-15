<?php
namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

       $user1 =  User::create([
            'username' => 'user',
            'name' => 'User',
            'email' => 'user@user.com',
            'password' => Hash::make('password'),
            'phone_no' => '0011223344',
            'email_verified_at' => now(),
        ]);
        $user1->assignRole('user');

        $user2 =  User::create([
            'username' => 'asim khan',
            'name' => 'Asim Khan',
            'email' => 'deeds2595@gmail.com',
            'password' => Hash::make('password'),
            'phone_no' => '0011223344',
            'dob' => '2024-08-12',
            'email_verified_at' => now(),
        ]);
        $user2->assignRole('user');

        $user3 =  User::create([
            'username' => 'asim khan',
            'name' => 'Asim Khan',
            'email' => 'webtimecreative@gmail.com',
            'password' => Hash::make('password'),
            'phone_no' => '0011223344',
            'dob' => '2024-08-12',
            'email_verified_at' => now(),
        ]);
        $user3->assignRole('user');

        $user4 =  User::create([
            'username' => 'asim khan',
            'name' => 'Asim Khan',
            'email' => 'basitawan.abdul@gmail.com',
            'password' => Hash::make('password'),
            'phone_no' => '0011223344',
            'dob' => '2024-08-12',
            'email_verified_at' => now(),
        ]);
        $user4->assignRole('user');

        $user5 =  User::create([
            'username' => 'admin',
            'name' => 'Admin',
            'email' => 'admin@menainsurance.com',
            'password' => Hash::make('password'),
            'phone_no' => '0011223344',
            'dob' => '2024-08-12',
            'email_verified_at' => now(),
        ]);
        $user5->assignRole('user');
    }
}
