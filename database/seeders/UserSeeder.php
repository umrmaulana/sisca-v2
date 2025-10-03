<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'npk' => '000000',
                'role' => 'Admin',
                'company_id' => null,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Loop3r123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PIC AII',
                'npk' => '012121',
                'role' => 'Pic',
                'company_id' => 1,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Aisin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PIC AIIA',
                'npk' => '012122',
                'role' => 'Pic',
                'company_id' => 2,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Aisin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PIC GA',
                'npk' => '012123',
                'role' => 'GA',
                'company_id' => 2,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Aisin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PIC P3K',
                'npk' => '012124',
                'role' => 'PIC P3K',
                'company_id' => 2,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Aisin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Supervisor AII',
                'npk' => '111111',
                'role' => 'Supervisor',
                'company_id' => 1,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Aisin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Supervisor AIIA',
                'npk' => '222222',
                'role' => 'Supervisor',
                'company_id' => 2,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Aisin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Management',
                'npk' => '333333',
                'role' => 'Management',
                'company_id' => null,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Aisin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
