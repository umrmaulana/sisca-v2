<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tm_equipment_types')->insert([
            [
                'equipment_name' => 'Apar',
                'equipment_type' => 'Co2',
                'desc' => 'Alat Pemadam Api Ringan - CO2',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Apar',
                'equipment_type' => 'Powder',
                'desc' => 'Alat Pemadam Api Ringan - Powder',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Apar',
                'equipment_type' => 'AF11',
                'desc' => 'Alat Pemadam Api Ringan - Foam',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Hydrant',
                'equipment_type' => 'Box',
                'desc' => 'Alat Hydrant Pemadam Kebakaran - Box',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Hydrant',
                'equipment_type' => 'Pilar',
                'desc' => 'Alat Hydrant Pemadam Kebakaran - Pilar',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'SCBA',
                'equipment_type' => 'SCBA',
                'desc' => 'Alat Pemadam Api Ringan - SCBA',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Lemary Emergency',
                'equipment_type' => 'Lemary Emergency',
                'desc' => 'Alat Pemadam Api Ringan - Trolley',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Sling',
                'equipment_type' => 'Sling',
                'desc' => 'Alat Angkut Barang Berat',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Tandu',
                'equipment_type' => 'Tandu',
                'desc' => 'Alat Evakuasi Korban',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Eye Wash',
                'equipment_type' => 'Eye Wash',
                'desc' => 'Alat Cuci Mata Darurat',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Tembin',
                'equipment_type' => 'Tembin',
                'desc' => 'Alat Angkut Korban',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Chain Block',
                'equipment_type' => 'Chain Block',
                'desc' => 'Alat Angkat Berat',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Body Harness',
                'equipment_type' => 'Body Harness',
                'desc' => 'Alat Pengaman Kerja di Ketinggian',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'Safety Belt',
                'equipment_type' => 'Safety Belt',
                'desc' => 'Alat Pengaman Kerja di Ketinggian',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'equipment_name' => 'FACP',
                'equipment_type' => 'FACP',
                'desc' => 'Fire Alarm Control Panel',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
