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


        DB::table('tm_locations_new')->insert([
            // Aisin Indonesia
            [
                'location_code' => 'AII-LOC-001',
                'plant_id' => $plants->first(),
                'area_id' => $areas->where('area_name', 'Area 1 AII')->first()->id,
                'pos' => 'K2',
                'coordinate_x' => 150,
                'coordinate_y' => 200,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
