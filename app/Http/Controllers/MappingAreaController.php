<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Company;
use App\Models\Area;
use App\Models\Equipment;
use App\Models\Inspection;
use App\Models\InspectionDetail;
use App\Models\ChecksheetTemplate;
use App\Models\PeriodCheck;
use Carbon\Carbon;

class MappingAreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::guard()->user();
        $userRole = $user->role;

        // Initialize variables
        $companies = collect();
        $areas = collect();
        $equipments = Equipment::with(['equipmentType', 'location'])->where('id', 0)->paginate(15); // Empty paginated result
        $equipmentTypes = collect();
        $mappingImage = null;
        $selectedCompany = null;
        $selectedArea = null;
        $selectedEquipmentType = null;
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));
        $selectedStatus = $request->get('status', 'all');
        $searchEquipment = $request->get('search_equipment', '');
        $selectedEquipmentTypeId = $request->get('equipment_type_id');
        $viewMode = $request->get('view_mode', 'area'); // 'area' or 'company'

        // Get equipment types based on role
        if (in_array($userRole, ['Admin', 'Management'])) {
            // Admin & Management can see all equipment types
            $equipmentTypes = \App\Models\EquipmentType::where('is_active', true)
                ->orderBy('equipment_name')
                ->get();
        } else {
            // PIC & Supervisor only see equipment types available in their company
            if ($user->company_id) {
                $equipmentTypes = \App\Models\EquipmentType::where('is_active', true)
                    ->whereHas('equipments', function ($q) use ($user) {
                        $q->whereHas('location', function ($loc) use ($user) {
                            $loc->where('company_id', $user->company_id);
                        });
                    })
                    ->orderBy('equipment_name')
                    ->get();
            }
        }

        // Set selected equipment type
        if ($selectedEquipmentTypeId) {
            $selectedEquipmentType = \App\Models\EquipmentType::find($selectedEquipmentTypeId);
        }

        // Get companies based on role
        if (in_array($userRole, ['Admin', 'Management'])) {
            $companies = Company::where('is_active', true)->orderBy('company_name')->get();
            $selectedCompanyId = $request->get('company_id');
        } else {
            // For PIC and Supervisor, use their company_id
            $selectedCompanyId = $user->company_id;
            if ($selectedCompanyId) {
                $companies = Company::where('id', $selectedCompanyId)->where('is_active', true)->get();
            }
        }

        if ($selectedCompanyId) {
            $selectedCompany = Company::find($selectedCompanyId);

            // Get areas for selected company (always needed for dropdown)
            $areasQuery = Area::where('company_id', $selectedCompanyId)
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
                $equipmentsQuery = Equipment::with(['equipmentType', 'location', 'periodCheck'])
                    ->whereHas('location', function ($q) use ($selectedAreaId) {
                        $q->where('area_id', $selectedAreaId);
                    })
                    ->where('is_active', true);

                $this->applyEquipmentFilters($equipmentsQuery, $selectedEquipmentTypeId, $searchEquipment, $selectedStatus, $selectedMonth, $selectedYear);
                $equipments = $equipmentsQuery->paginate(15)->appends(request()->query());
                $this->addInspectionStatusWithCheckItems($equipments, $selectedMonth, $selectedYear);

            } elseif ($selectedAreaId === 'all' || (!$selectedAreaId && $selectedCompany)) {
                // Show company mapping (All Area)
                $mappingImage = $this->getCompanyMappingImage($selectedCompany);

                // Get all equipments in this company with company-level coordinates
                $equipmentsQuery = Equipment::with(['equipmentType', 'location', 'periodCheck'])
                    ->whereHas('location', function ($q) use ($selectedCompanyId) {
                        $q->where('company_id', $selectedCompanyId);
                    })
                    ->where('is_active', true);

                $this->applyEquipmentFilters($equipmentsQuery, $selectedEquipmentTypeId, $searchEquipment, $selectedStatus, $selectedMonth, $selectedYear);
                $equipments = $equipmentsQuery->paginate(15)->appends(request()->query());
                $this->addInspectionStatusWithCheckItems($equipments, $selectedMonth, $selectedYear);
            }
        }

        return view('mapping-area.index', compact(
            'companies',
            'areas',
            'equipments',
            'equipmentTypes',
            'mappingImage',
            'selectedCompany',
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

    public function getAreasByCompany(Request $request)
    {
        $companyId = $request->get('company_id');
        $equipmentTypeId = $request->get('equipment_type_id');

        $areasQuery = Area::where('company_id', $companyId)
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
        $fallbackImagePath = "templates/mapping/area_{$area->id}.jpg";
        if (Storage::disk('public')->exists($fallbackImagePath)) {
            return asset('storage/' . $fallbackImagePath);
        }

        // Try other common extensions
        $extensions = ['png', 'jpeg', 'gif'];
        foreach ($extensions as $ext) {
            $fallbackPath = "templates/mapping/area_{$area->id}.{$ext}";
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

        // Apply status filter before pagination - note: this is simplified for demo
        // In production, you might want to implement period-aware filtering here too
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
     * Add inspection status to equipment collection with check items details
     */
    private function addInspectionStatusWithCheckItems($equipments, $month, $year)
    {
        $today = Carbon::now();

        foreach ($equipments as $equipment) {
            // Get period-based date range for current inspection cycle
            $dateRange = $this->getInspectionDateRange($equipment, $today);

            // Get checksheet templates for this equipment type
            $checksheetTemplates = ChecksheetTemplate::where('equipment_type_id', $equipment->equipment_type_id)
                ->where('is_active', true)
                ->orderBy('order_number')
                ->get();

            $equipment->checksheet_templates = $checksheetTemplates;

            // Get latest inspection within the period
            $latestInspection = null;
            if ($dateRange) {
                $latestInspection = Inspection::where('equipment_id', $equipment->id)
                    ->whereBetween('inspection_date', [$dateRange['start'], $dateRange['end']])
                    ->where('status', '!=', 'draft')
                    ->with(['details.checksheetTemplate'])
                    ->orderBy('inspection_date', 'desc')
                    ->first();
            }

            $equipment->latest_inspection = $latestInspection;
            $equipment->is_checked = (bool) $latestInspection;

            // Create check items status array
            $checkItemsStatus = [];
            foreach ($checksheetTemplates as $template) {
                $status = 'unchecked'; // Default: not checked

                if ($latestInspection) {
                    $detail = $latestInspection->details->where('checksheet_id', $template->id)->first();
                    if ($detail) {
                        $status = strtolower($detail->status); // ok, ng, na
                    }
                }

                $checkItemsStatus[] = [
                    'template_id' => $template->id,
                    'item_name' => $template->item_name,
                    'status' => $status,
                    'order_number' => $template->order_number
                ];
            }

            $equipment->check_items_status = $checkItemsStatus;

            // Set overall status based on check items
            if (!$latestInspection) {
                $equipment->overall_status = 'unchecked';
            } else {
                $hasNg = collect($checkItemsStatus)->contains('status', 'ng');
                $equipment->overall_status = $hasNg ? 'ng' : 'ok';
            }
        }
    }

    /**
     * Get inspection date range based on equipment's period check
     */
    private function getInspectionDateRange($equipment, $currentDate)
    {
        if (!$equipment->periodCheck) {
            return null;
        }

        $period = $equipment->periodCheck->period_check;

        switch ($period) {
            case 'Daily':
                return [
                    'start' => $currentDate->copy()->startOfDay(),
                    'end' => $currentDate->copy()->endOfDay()
                ];

            case 'Weekly':
                return [
                    'start' => $currentDate->copy()->startOfWeek(),
                    'end' => $currentDate->copy()->endOfWeek()
                ];

            case 'Monthly':
                return [
                    'start' => $currentDate->copy()->startOfMonth(),
                    'end' => $currentDate->copy()->endOfMonth()
                ];

            case 'Quarterly':
                $quarter = ceil($currentDate->month / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $quarter * 3;
                return [
                    'start' => Carbon::create($currentDate->year, $startMonth, 1)->startOfMonth(),
                    'end' => Carbon::create($currentDate->year, $endMonth, 1)->endOfMonth()
                ];

            case 'Annual':
            case 'Yearly':
                return [
                    'start' => $currentDate->copy()->startOfYear(),
                    'end' => $currentDate->copy()->endOfYear()
                ];

            default:
                return null;
        }
    }

    /**
     * Add inspection status to equipment collection (legacy method for backward compatibility)
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
     * Get company mapping image URL from database
     */
    private function getCompanyMappingImage($company)
    {
        if (!$company) {
            return null;
        }

        // Get image from database company_mapping_picture field
        if ($company->company_mapping_picture && Storage::disk('public')->exists($company->company_mapping_picture)) {
            return asset('storage/' . $company->company_mapping_picture);
        }

        // Fallback: try conventional naming (company_ID.jpg) for backward compatibility
        $fallbackImagePath = "templates/mapping/company_{$company->id}.jpg";
        if (Storage::disk('public')->exists($fallbackImagePath)) {
            return asset('storage/' . $fallbackImagePath);
        }

        // Try other common extensions
        $extensions = ['png', 'jpeg', 'gif'];
        foreach ($extensions as $ext) {
            $fallbackPath = "templates/mapping/company_{$company->id}.{$ext}";
            if (Storage::disk('public')->exists($fallbackPath)) {
                return asset('storage/' . $fallbackPath);
            }
        }

        return null;
    }
}
