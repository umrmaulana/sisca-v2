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
            CompanySeeder::class,
            UserSeeder::class,
            PeriodCheckSeeder::class,
            P3kLocationSeeder::class,
            P3kSeeder::class,
            AccidentSeeder::class,
            DeptSeeder::class,
            // AreaSeeder::class,
            // LocationSeeder::class,
            EquipmentTypeSeeder::class,
            ChecksheetTemplateSeeder::class,
            // EquipmentSeeder::class,

            // Item check seeders
            // ItemCheckHeadCraneSeeder::class,
        ]);
    }
}
