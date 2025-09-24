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
        // Get company IDs
        $companies = DB::table('tm_companies')->pluck('id', 'company_name');

        DB::table('tm_areas')->insert([
            // Companny  1 Aii
            [
                'area_name' => 'Office AII',
                'company_id' => $companies->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/Office_aii.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Body AII',
                'company_id' => $companies->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/Body_aii.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Unit AII',
                'company_id' => $companies->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/Unit_aii.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Eq Dev AII',
                'company_id' => $companies->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/Eq_dev_aii.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'PPIC AII',
                'company_id' => $companies->first(),
                'mapping_picture' => 'sisca-v2/templates/mapping/PPIC_aii.png',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Company 2 areas (if exists)
            [
                'area_name' => 'Office AIIA',
                'company_id' => $companies->count() > 1 ? $companies->skip(1)->first() : $companies->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Body AIIA',
                'company_id' => $companies->count() > 1 ? $companies->skip(1)->first() : $companies->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Locker & Pose AIIA',
                'company_id' => $companies->count() > 1 ? $companies->skip(1)->first() : $companies->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Kantin AIIA',
                'company_id' => $companies->count() > 1 ? $companies->skip(1)->first() : $companies->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Masjid AIIA',
                'company_id' => $companies->count() > 1 ? $companies->skip(1)->first() : $companies->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Main Station AIIA',
                'company_id' => $companies->count() > 1 ? $companies->skip(1)->first() : $companies->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Pump AIIA',
                'company_id' => $companies->count() > 1 ? $companies->skip(1)->first() : $companies->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Storage Chemical AIIA',
                'company_id' => $companies->count() > 1 ? $companies->skip(1)->first() : $companies->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'Unit AIIA',
                'company_id' => $companies->count() > 1 ? $companies->skip(1)->first() : $companies->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'area_name' => 'WWT AIIA',
                'company_id' => $companies->count() > 1 ? $companies->skip(1)->first() : $companies->first(),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
