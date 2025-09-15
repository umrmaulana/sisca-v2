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
                'npk' => '00000',
                'role' => 'Admin',
                'plant_id' => null,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Loop3r123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PIC AII',
                'npk' => '12121',
                'role' => 'Pic',
                'plant_id' => 1,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Aisin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PIC AIIA',
                'npk' => '12122',
                'role' => 'Pic',
                'plant_id' => 2,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Aisin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Supervisor AII',
                'npk' => '11111',
                'role' => 'Supervisor',
                'plant_id' => 1,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Aisin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Supervisor AIIA',
                'npk' => '22222',
                'role' => 'Supervisor',
                'plant_id' => 2,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Aisin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Management',
                'npk' => '33333',
                'role' => 'Management',
                'plant_id' => null,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make('Aisin123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
