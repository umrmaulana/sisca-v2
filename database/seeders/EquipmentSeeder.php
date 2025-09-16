<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    // public function run()
    // {
    //     // Get necessary data
    //     $equipmentTypes = DB::table('tm_equipment_types')->get();
    //     $locations = DB::table('tm_locations_new')->get();
    //     $periodChecks = DB::table('tm_period_checks')->get();

    //     $equipments = [];

    //     // Get existing equipment codes to avoid duplicates
    //     $existingCodes = DB::table('tm_equipments')->pluck('equipment_code')->toArray();

    //     // Generate equipments for each equipment type
    //     foreach ($equipmentTypes as $equipmentType) {
    //         // Determine how many equipments to create based on type
    //         $count = $this->getEquipmentCount($equipmentType->equipment_name);

    //         // Get existing counter for this specific equipment type ID
    //         $prefix = $this->getPrefix($equipmentType);
    //         $existingCountForType = collect($existingCodes)
    //             ->filter(function ($code) use ($prefix) {
    //                 return str_starts_with($code, $prefix . '-');
    //             })
    //             ->map(function ($code) use ($prefix) {
    //                 return (int) str_replace($prefix . '-', '', $code);
    //             })
    //             ->max() ?? 0;

    //         for ($i = 1; $i <= $count; $i++) {
    //             $counter = $existingCountForType + $i;
    //             $equipmentCode = $prefix . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);

    //             // Skip if already exists
    //             if (in_array($equipmentCode, $existingCodes)) {
    //                 // Increment counter and try again
    //                 $counter++;
    //                 $equipmentCode = $prefix . '-' . str_pad($counter, 3, '0', STR_PAD_LEFT);
    //             }

    //             $randomLocation = $locations->random();

    //             // Set period check based on equipment type
    //             $periodCheckId = $this->getPeriodCheckId($equipmentType->equipment_name);

    //             $equipments[] = [
    //                 'equipment_code' => $equipmentCode,
    //                 'equipment_type_id' => $equipmentType->id,
    //                 'location_id' => $randomLocation->id,
    //                 'qrcode' => 'sisca-v2/qrcode/QR_' . $equipmentCode . '_' . time() . $i . '.png',
    //                 'period_check_id' => $periodCheckId,
    //                 'is_active' => true,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ];
    //         }
    //     }

    //     if (!empty($equipments)) {
    //         DB::table('tm_equipments')->insert($equipments);
    //     }
    // }

    // /**
    //  * Get prefix for equipment type
    //  */
    // private function getPrefix($equipmentType)
    // {
    //     $basePrefix = '';
    //     switch ($equipmentType->equipment_name) {
    //         case 'Apar':
    //             $basePrefix = 'AP';
    //             break;
    //         case 'Hydrant':
    //             $basePrefix = $equipmentType->equipment_type === 'Box' ? 'HB' : 'HP';
    //             break;
    //         case 'Eye Wash':
    //             $basePrefix = 'EW';
    //             break;
    //         case 'Safety Belt':
    //             $basePrefix = 'SB';
    //             break;
    //         case 'Body Harness':
    //             $basePrefix = 'BH';
    //             break;
    //         case 'Chain Block':
    //             $basePrefix = 'CB';
    //             break;
    //         case 'Sling':
    //             $basePrefix = 'SL';
    //             break;
    //         case 'Tandu':
    //             $basePrefix = 'TD';
    //             break;
    //         case 'Tembin':
    //             $basePrefix = 'TB';
    //             break;
    //         case 'SCBA':
    //             $basePrefix = 'SC';
    //             break;
    //         case 'Lemary Emergency':
    //             $basePrefix = 'LE';
    //             break;
    //         case 'FACP':
    //             $basePrefix = 'FC';
    //             break;
    //         default:
    //             $basePrefix = 'EQ';
    //             break;
    //     }

    //     // Add type ID suffix for equipment types with same name
    //     if ($equipmentType->equipment_name === 'Apar' && $equipmentType->id > 1) {
    //         $basePrefix .= $equipmentType->id;
    //     }

    //     return $basePrefix;
    // }

    // /**
    //  * Determine how many equipments to create for each type
    //  */
    // private function getEquipmentCount($equipmentName)
    // {
    //     switch ($equipmentName) {
    //         case 'Sling':
    //             return 7; // Keep Sling at 7
    //         default:
    //             return 3; // All other equipment types get 3 units
    //     }
    // }

    // /**
    //  * Get period check ID based on equipment type
    //  */
    // private function getPeriodCheckId($equipmentName)
    // {
    //     switch ($equipmentName) {
    //         case 'Sling':
    //             return 1; // Daily period check (ID 1)
    //         default:
    //             return 3; // Monthly period check (ID 3)
    //     }
    // }
}
