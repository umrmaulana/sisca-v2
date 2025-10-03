<?php

namespace Database\Seeders;

use App\Models\P3kLocation;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class P3kLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $loc = [
            $locations = [
            'MTE Mold Die Cast',
            'Hot Corner Die Cast',
            'Assy Unit',
            'Injeksi',
            'Prod Electric',
            ]
        ];

        foreach ($locations as $location) {
            P3kLocation::create([
                'location' => $location,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

    }
}
