<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get plant IDs
        $plants = DB::table('tm_plants')->pluck('id', 'plant_name');

        DB::table('tm_areas')->insert([
            // Plant  1 Aii
            [
                'area_name' => 'Office AII',
                'plant_id' => $plants->first(),
                'mappping_picture' => 'sisca-v2/templates/mapping/Office_aii.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Body AII',
                'plant_id' => $plants->first(),
                'mappping_picture' => 'sisca-v2/templates/mapping/Body_aii.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Unit AII',
                'plant_id' => $plants->first(),
                'mappping_picture' => 'sisca-v2/templates/mapping/Unit_aii.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Eq Dev AII',
                'plant_id' => $plants->first(),
                'mappping_picture' => 'sisca-v2/templates/mapping/Eq_dev_aii.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'PPIC AII',
                'plant_id' => $plants->first(),
                'mappping_picture' => 'sisca-v2/templates/mapping/PPIC_aii.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Plant 2 areas (if exists)
            [
                'area_name' => 'Office AIIA',
                'plant_id' => $plants->count() > 1 ? $plants->skip(1)->first() : $plants->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Body AIIA',
                'plant_id' => $plants->count() > 1 ? $plants->skip(1)->first() : $plants->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Locker & Pose AIIA',
                'plant_id' => $plants->count() > 1 ? $plants->skip(1)->first() : $plants->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Kantin AIIA',
                'plant_id' => $plants->count() > 1 ? $plants->skip(1)->first() : $plants->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Masjid AIIA',
                'plant_id' => $plants->count() > 1 ? $plants->skip(1)->first() : $plants->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Main Station AIIA',
                'plant_id' => $plants->count() > 1 ? $plants->skip(1)->first() : $plants->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Pump AIIA',
                'plant_id' => $plants->count() > 1 ? $plants->skip(1)->first() : $plants->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Storage Chemical AIIA',
                'plant_id' => $plants->count() > 1 ? $plants->skip(1)->first() : $plants->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Unit AIIA',
                'plant_id' => $plants->count() > 1 ? $plants->skip(1)->first() : $plants->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'WWT AIIA',
                'plant_id' => $plants->count() > 1 ? $plants->skip(1)->first() : $plants->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
