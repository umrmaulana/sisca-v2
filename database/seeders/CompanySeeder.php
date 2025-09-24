<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tm_companies')->insert([
            [
                'company_name' => 'PT. AII',
                'company_mapping_picture' => 'sisca-v2/templates/mapping/sample-company-map.jpg', // Sample mapping picture
                'company_description' => 'Astra International Indonesia',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_name' => 'PT. AIIA',
                'company_description' => 'Astra International Insurance Agency',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
