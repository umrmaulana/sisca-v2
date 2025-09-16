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
            // Plant 1 areas
            [
                'area_name' => 'Area 1 AII',
                'plant_id' => $plants->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/area01.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Area 2 AII',
                'plant_id' => $plants->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/area02.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Area 3 AII',
                'plant_id' => $plants->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/area03.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Area 4 AII',
                'plant_id' => $plants->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/area04.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Area 5 AII',
                'plant_id' => $plants->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/area05.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Office & Kantin AII',
                'plant_id' => $plants->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/area06.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Area 7 AII',
                'plant_id' => $plants->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/area07.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Area 8 AII',
                'plant_id' => $plants->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/area08.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Area 9 AII',
                'plant_id' => $plants->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/area09.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Area 10 AII',
                'plant_id' => $plants->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/area10.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Area 11 AII',
                'plant_id' => $plants->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/area11.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Area 12 AII',
                'plant_id' => $plants->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/area12.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Area 13 AII',
                'plant_id' => $plants->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/area13.png',
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
