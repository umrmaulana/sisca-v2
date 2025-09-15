<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tm_plants')->insert([
            [
                'plant_name' => 'PT. AII',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plant_name' => 'PT. AIIA',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
