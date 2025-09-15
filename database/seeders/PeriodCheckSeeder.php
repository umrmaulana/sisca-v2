<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeriodCheckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tm_period_checks')->insert([
            [
                'period_check' => 'Daily',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'period_check' => 'Weekly',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'period_check' => 'Monthly',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'period_check' => 'Quarterly',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'period_check' => 'Annual',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
