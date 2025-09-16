<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
                // Master data seeders (run in order due to foreign key dependencies)
            PlantSeeder::class,
            AreaSeeder::class,
            PeriodCheckSeeder::class,
            LocationSeeder::class,
            UserSeeder::class,
            EquipmentTypeSeeder::class,
            ChecksheetTemplateSeeder::class,
            // EquipmentSeeder::class,
        ]);
    }
}
