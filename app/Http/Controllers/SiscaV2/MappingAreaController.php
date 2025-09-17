<?php

namespace App\Http\Controllers\SiscaV2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\SiscaV2\Plant;
use App\Models\SiscaV2\Area;
use App\Models\SiscaV2\Equipment;
use App\Models\SiscaV2\Inspection;
use Carbon\Carbon;

class MappingAreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sisca-v2');
    }

    public function index(Request $request)
    {
        $user = Auth::guard('sisca-v2')->user();
        $userRole = $user->role;

        // Initialize variables
        $plants = collect();
        $areas = collect();
        $equipments = Equipment::with(['equipmentType', 'location'])->where('id', 0)->paginate(15); // Empty paginated result
        $equipmentTypes = collect();
        $mappingImage = null;
        $selectedPlant = null;
        $selectedArea = null;
        $selectedEquipmentType = null;
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));
        $selectedStatus = $request->get('status', 'all');
        $searchEquipment = $request->get('search_equipment', '');
        $selectedEquipmentTypeId = $request->get('equipment_type_id');
        $viewMode = $request->get('view_mode', 'area'); // 'area' or 'plant'

        // Get equipment types based on role
        if (in_array($userRole, ['Admin', 'Management'])) {
            // Admin & Management can see all equipment types
            $equipmentTypes = \App\Models\SiscaV2\EquipmentType::where('is_active', true)
                ->orderBy('equipment_name')
                ->get();
        } else {
            // PIC & Supervisor only see equipment types available in their plant
            if ($user->plant_id) {
                $equipmentTypes = \App\Models\SiscaV2\EquipmentType::where('is_active', true)
                    ->whereHas('equipments', function ($q) use ($user) {
                        $q->whereHas('location', function ($loc) use ($user) {
                            $loc->where('plant_id', $user->plant_id);
                        });
                    })
                    ->orderBy('equipment_name')
                    ->get();
            }
        }

        // Set selected equipment type
        if ($selectedEquipmentTypeId) {
            $selectedEquipmentType = \App\Models\SiscaV2\EquipmentType::find($selectedEquipmentTypeId);
        }

        // Get plants based on role
        if (in_array($userRole, ['Admin', 'Management'])) {
            $plants = Plant::where('is_active', true)->orderBy('plant_name')->get();
            $selectedPlantId = $request->get('plant_id');
        } else {
            // For PIC and Supervisor, use their plant_id
            $selectedPlantId = $user->plant_id;
            if ($selectedPlantId) {
                $plants = Plant::where('id', $selectedPlantId)->where('is_active', true)->get();
            }
        }

        if ($selectedPlantId) {
            $selectedPlant = Plant::find($selectedPlantId);

            // Get areas for selected plant (always needed for dropdown)
            $areasQuery = Area::where('plant_id', $selectedPlantId)
                ->where('is_active', true);

            // If equipment type is selected, only show areas that have equipment of that type
            if ($selectedEquipmentTypeId) {
                $areasQuery->whereHas('locations.equipments', function ($q) use ($selectedEquipmentTypeId) {
                    $q->where('equipment_type_id', $selectedEquipmentTypeId)
                        ->where('is_active', true);
                });
            }

            $areas = $areasQuery->orderBy('area_name')->get();

            $selectedAreaId = $request->get('area_id');

            if ($selectedAreaId && $selectedAreaId !== 'all') {
                // Show specific area mapping
                $selectedArea = Area::find($selectedAreaId);
                $mappingImage = $this->getMappingImage($selectedArea);

                // Get equipments with area coordinates
                $equipmentsQuery = Equipment::with(['equipmentType', 'location'])
                    ->whereHas('location', function ($q) use ($selectedAreaId) {
                        $q->where('area_id', $selectedAreaId);
                    })
                    ->where('is_active', true);

                $this->applyEquipmentFilters($equipmentsQuery, $selectedEquipmentTypeId, $searchEquipment, $selectedStatus, $selectedMonth, $selectedYear);
                $equipments = $equipmentsQuery->paginate(15)->appends(request()->query());
                $this->addInspectionStatus($equipments, $selectedMonth, $selectedYear);

            } elseif ($selectedAreaId === 'all' || (!$selectedAreaId && $selectedPlant)) {
                // Show plant mapping (All Area)
                $mappingImage = $this->getPlantMappingImage($selectedPlant);

                // Get all equipments in this plant with plant-level coordinates
                $equipmentsQuery = Equipment::with(['equipmentType', 'location'])
                    ->whereHas('location', function ($q) use ($selectedPlantId) {
                        $q->where('plant_id', $selectedPlantId);
                    })
                    ->where('is_active', true);

                $this->applyEquipmentFilters($equipmentsQuery, $selectedEquipmentTypeId, $searchEquipment, $selectedStatus, $selectedMonth, $selectedYear);
                $equipments = $equipmentsQuery->paginate(15)->appends(request()->query());
                $this->addInspectionStatus($equipments, $selectedMonth, $selectedYear);
            }
        }

        return view('sisca-v2.mapping-area.index', compact(
            'plants',
            'areas',
            'equipments',
            'equipmentTypes',
            'mappingImage',
            'selectedPlant',
            'selectedArea',
            'selectedEquipmentType',
            'selectedMonth',
            'selectedYear',
            'selectedStatus',
            'searchEquipment',
            'viewMode',
            'userRole'
        ));
    }

    public function getAreasByPlant(Request $request)
    {
        $plantId = $request->get('plant_id');
        $equipmentTypeId = $request->get('equipment_type_id');

        $areasQuery = Area::where('plant_id', $plantId)
            ->where('is_active', true);

        // If equipment type is selected, only show areas that have equipment of that type
        if ($equipmentTypeId) {
            $areasQuery->whereHas('locations.equipments', function ($q) use ($equipmentTypeId) {
                $q->where('equipment_type_id', $equipmentTypeId)
                    ->where('is_active', true);
            });
        }

        $areas = $areasQuery->orderBy('area_name')->get();

        return response()->json($areas);
    }

    /**
     * Get mapping image URL from database
     */
    private function getMappingImage($area)
    {
        if (!$area) {
            return null;
        }

        // First, try to get image from database mapping_picture field
        if ($area->mapping_picture && Storage::disk('public')->exists($area->mapping_picture)) {
            return asset('storage/' . $area->mapping_picture);
        }

        // Fallback: try conventional naming (area_ID.jpg) for backward compatibility
        $fallbackImagePath = "sisca-v2/templates/mapping/area_{$area->id}.jpg";
        if (Storage::disk('public')->exists($fallbackImagePath)) {
            return asset('storage/' . $fallbackImagePath);
        }

        // Try other common extensions
        $extensions = ['png', 'jpeg', 'gif'];
        foreach ($extensions as $ext) {
            $fallbackPath = "sisca-v2/templates/mapping/area_{$area->id}.{$ext}";
            if (Storage::disk('public')->exists($fallbackPath)) {
                return asset('storage/' . $fallbackPath);
            }
        }

        return null;
    }

    /**
     * Apply equipment filters to query
     */
    private function applyEquipmentFilters($equipmentsQuery, $equipmentTypeId, $searchEquipment, $status, $month, $year)
    {
        // Apply equipment type filter
        if ($equipmentTypeId) {
            $equipmentsQuery->where('equipment_type_id', $equipmentTypeId);
        }

        // Apply equipment search filter
        if (!empty($searchEquipment)) {
            $equipmentsQuery->where(function ($q) use ($searchEquipment) {
                $q->where('equipment_code', 'like', "%{$searchEquipment}%")
                    ->orWhere('desc', 'like', "%{$searchEquipment}%");
            });
        }

        // Define date range for filtering
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Apply status filter before pagination
        if ($status === 'checked') {
            $equipmentsQuery->whereHas('inspections', function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('inspection_date', [$startOfMonth, $endOfMonth])
                    ->where('status', '!=', 'draft');
            });
        } elseif ($status === 'unchecked') {
            $equipmentsQuery->whereDoesntHave('inspections', function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('inspection_date', [$startOfMonth, $endOfMonth])
                    ->where('status', '!=', 'draft');
            });
        }
    }

    /**
     * Add inspection status to equipment collection
     */
    private function addInspectionStatus($equipments, $month, $year)
    {
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        foreach ($equipments as $equipment) {
            $hasInspection = Inspection::where('equipment_id', $equipment->id)
                ->whereBetween('inspection_date', [$startOfMonth, $endOfMonth])
                ->where('status', '!=', 'draft')
                ->exists();

            $equipment->is_checked = $hasInspection;

            // Get latest inspection for this equipment in the selected month
            $latestInspection = Inspection::where('equipment_id', $equipment->id)
                ->whereBetween('inspection_date', [$startOfMonth, $endOfMonth])
                ->where('status', '!=', 'draft')
                ->orderBy('inspection_date', 'desc')
                ->first();

            $equipment->latest_inspection = $latestInspection;
        }
    }

    /**
     * Get plant mapping image URL from database
     */
    private function getPlantMappingImage($plant)
    {
        if (!$plant) {
            return null;
        }

        // Get image from database plant_mapping_picture field
        if ($plant->plant_mapping_picture && Storage::disk('public')->exists($plant->plant_mapping_picture)) {
            return asset('storage/' . $plant->plant_mapping_picture);
        }

        // Fallback: try conventional naming (plant_ID.jpg) for backward compatibility
        $fallbackImagePath = "sisca-v2/templates/mapping/plant_{$plant->id}.jpg";
        if (Storage::disk('public')->exists($fallbackImagePath)) {
            return asset('storage/' . $fallbackImagePath);
        }

        // Try other common extensions
        $extensions = ['png', 'jpeg', 'gif'];
        foreach ($extensions as $ext) {
            $fallbackPath = "sisca-v2/templates/mapping/plant_{$plant->id}.{$ext}";
            if (Storage::disk('public')->exists($fallbackPath)) {
                return asset('storage/' . $fallbackPath);
            }
        }

        return null;
    }
}
