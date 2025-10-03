<?php

namespace Database\Seeders;

use App\Models\MasterAccident;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accidents = [
            ['name' => 'Terjatuh / Tergelincir / Tersandung'],
            ['name' => 'Tertimpa Benda Berat / Benda Jatuh'],
            ['name' => 'Terbentur'],
            ['name' => 'Tersayat / Tergores / Terkena Benda Tajam'],
            ['name' => 'Terjepit / Tergulung Mesin / Alat'],
            ['name' => 'Terkena Listrik'],
            ['name' => 'Terkena Bahan Kimia'],
            ['name' => 'Terbakar / Percikan Api'],
            ['name' => 'Keracunan'],
            ['name' => 'Paparan Panas'],
            ['name' => 'Paparan Debu'],
            ['name' => 'Paparan Gas / Uap'],
            ['name' => 'Tertabrak Alat Angkut'],
        ];

        foreach ($accidents as $accident) {
            MasterAccident::create([
                'name' => $accident['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
