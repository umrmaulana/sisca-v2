<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get plant and area IDs
        $plants = DB::table('tm_plants')->pluck('id', 'plant_name');
        $areas = DB::table('tm_areas')->get();

        $locations = [];
        $counter = 1;

        foreach ($areas as $area) {
            // Create 2-3 locations per area
            for ($i = 1; $i <= 10; $i++) {
                $locations[] = [
                    'location_code' => 'LOC-' . str_pad($counter, 3, '0', STR_PAD_LEFT),
                    'plant_id' => $area->plant_id,
                    'area_id' => $area->id,
                    'pos' => 'Position ' . $i,
                    'coordinate_x' => rand(100, 999) / 10,
                    'coordinate_y' => rand(100, 999) / 10,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $counter++;
            }
        }

        DB::table('tm_locations_new')->insert($locations);
    }
}
