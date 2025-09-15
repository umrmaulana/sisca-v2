<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChecksheetTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get equipment type IDs
        $equipmentTypes = DB::table('tm_equipment_types')->get();

        $templates = [];

        foreach ($equipmentTypes as $equipmentType) {
            $order = 1;

            switch ($equipmentType->equipment_name) {
                case 'Apar':
                    if ($equipmentType->equipment_type === 'Co2') {
                        $templates = array_merge($templates, [
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Pressure',
                                'standar_condition' => 'Tekanan Pressure Berada Di Warna Hijau',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Hose',
                                'standar_condition' => 'Tidak Gepeng / Kaku / Tersumbat / Retak',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Corong / Nozzle',
                                'standar_condition' => 'Tidak Gepeng / Kaku / Tersumbat / Retak',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Tabung',
                                'standar_condition' => 'Tidak Karat / Penyok',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Regulator',
                                'standar_condition' => 'Tidak Rusak',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Segel & Pin',
                                'standar_condition' => 'Tidak Rusak',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Berat Tabung',
                                'standar_condition' => 'Minimal 5 Kg',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                        ]);
                    } elseif ($equipmentType->equipment_type === 'Powder') {
                        $templates = array_merge($templates, [
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Pressure',
                                'standar_condition' => 'Tekanan Pressure Berada Di Warna Hijau',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Hose',
                                'standar_condition' => 'Tidak Gepeng / Kaku / Tersumbat / Retak',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Tabung',
                                'standar_condition' => 'Tidak Karat / Penyok',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Regulator',
                                'standar_condition' => 'Tidak Rusak',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Segel & Pin',
                                'standar_condition' => 'Tidak Rusak',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Powder',
                                'standar_condition' => 'Tidak Menggumpal / Basah',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                        ]);
                    } elseif ($equipmentType->equipment_type === 'AF11') {
                        $templates = array_merge($templates, [
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Pressure',
                                'standar_condition' => 'Tekanan Pressure Berada Di Warna Hijau',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Hose',
                                'standar_condition' => 'Tidak Gepeng / Kaku / Tersumbat / Retak',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Tabung',
                                'standar_condition' => 'Tidak Karat / Penyok',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Foam Level',
                                'standar_condition' => 'Level Foam Minimal 80%',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                        ]);
                    }
                    break;

                case 'Hydrant':
                    if ($equipmentType->equipment_type === 'Box') {
                        $templates = array_merge($templates, [
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Box',
                                'standar_condition' => 'Tidak Penyok / Rusak',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Selang',
                                'standar_condition' => 'Tidak Rusak / Bocor',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Nozzle',
                                'standar_condition' => 'Tidak Rusak / Tersumbat',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Valve',
                                'standar_condition' => 'Dapat Dibuka Tutup Dengan Lancar',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                        ]);
                    } elseif ($equipmentType->equipment_type === 'Pilar') {
                        $templates = array_merge($templates, [
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Pilar Body',
                                'standar_condition' => 'Tidak Penyok / Rusak / Karat',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Valve',
                                'standar_condition' => 'Dapat Dibuka Tutup Dengan Lancar',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'equipment_type_id' => $equipmentType->id,
                                'order_number' => $order++,
                                'item_name' => 'Cap',
                                'standar_condition' => 'Tidak Hilang / Rusak',
                                'standar_picture' => null,
                                'is_active' => true,
                                'created_by' => 1,
                                'updated_by' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                        ]);
                    }
                    break;

                case 'Eye Wash':
                    $templates = array_merge($templates, [
                        [
                            'equipment_type_id' => $equipmentType->id,
                            'order_number' => $order++,
                            'item_name' => 'Flow Test',
                            'standar_condition' => 'Air Mengalir Dengan Lancar',
                            'standar_picture' => null,
                            'is_active' => true,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'equipment_type_id' => $equipmentType->id,
                            'order_number' => $order++,
                            'item_name' => 'Valve',
                            'standar_condition' => 'Dapat Dioperasikan Dengan Mudah',
                            'standar_picture' => null,
                            'is_active' => true,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'equipment_type_id' => $equipmentType->id,
                            'order_number' => $order++,
                            'item_name' => 'Nozzle',
                            'standar_condition' => 'Tidak Tersumbat / Rusak',
                            'standar_picture' => null,
                            'is_active' => true,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ]);
                    break;

                case 'Safety Belt':
                    $templates = array_merge($templates, [
                        [
                            'equipment_type_id' => $equipmentType->id,
                            'order_number' => $order++,
                            'item_name' => 'Belt Webbing',
                            'standar_condition' => 'Tidak Sobek / Putus / Aus',
                            'standar_picture' => null,
                            'is_active' => true,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'equipment_type_id' => $equipmentType->id,
                            'order_number' => $order++,
                            'item_name' => 'Buckle',
                            'standar_condition' => 'Dapat Mengunci Dengan Baik',
                            'standar_picture' => null,
                            'is_active' => true,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'equipment_type_id' => $equipmentType->id,
                            'order_number' => $order++,
                            'item_name' => 'D-Ring',
                            'standar_condition' => 'Tidak Rusak / Retak',
                            'standar_picture' => null,
                            'is_active' => true,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ]);
                    break;

                case 'Chain Block':
                    $templates = array_merge($templates, [
                        [
                            'equipment_type_id' => $equipmentType->id,
                            'order_number' => $order++,
                            'item_name' => 'Hand Chain',
                            'standar_condition' => 'Tidak Putus / Karat',
                            'standar_picture' => null,
                            'is_active' => true,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'equipment_type_id' => $equipmentType->id,
                            'order_number' => $order++,
                            'item_name' => 'Load Chain',
                            'standar_condition' => 'Tidak Putus / Karat / Aus',
                            'standar_picture' => null,
                            'is_active' => true,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'equipment_type_id' => $equipmentType->id,
                            'order_number' => $order++,
                            'item_name' => 'Hook',
                            'standar_condition' => 'Tidak Retak / Rusak',
                            'standar_picture' => null,
                            'is_active' => true,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'equipment_type_id' => $equipmentType->id,
                            'order_number' => $order++,
                            'item_name' => 'Body',
                            'standar_condition' => 'Tidak Retak / Penyok',
                            'standar_picture' => null,
                            'is_active' => true,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ]);
                    break;

                default:
                    // Generic template for other equipment types
                    $templates = array_merge($templates, [
                        [
                            'equipment_type_id' => $equipmentType->id,
                            'order_number' => $order++,
                            'item_name' => 'Visual Check',
                            'standar_condition' => 'Kondisi Fisik Tidak Ada Yang Rusak',
                            'standar_picture' => null,
                            'is_active' => true,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                        [
                            'equipment_type_id' => $equipmentType->id,
                            'order_number' => $order++,
                            'item_name' => 'Function Check',
                            'standar_condition' => 'Berfungsi Dengan Baik',
                            'standar_picture' => null,
                            'is_active' => true,
                            'created_by' => 1,
                            'updated_by' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ],
                    ]);
                    break;
            }
        }

        DB::table('tm_checksheet_templates')->insert($templates);
    }
}
