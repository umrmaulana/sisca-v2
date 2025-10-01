<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Company;
use App\Models\Area;
use App\Models\EquipmentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth()->user();
        $userRole = $user->role;

        // Filter defaults
        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));
        $selectedCompanyId = null;
        $selectedAreaId = $request->get('area_id');
        $selectedEquipmentTypeId = $request->get('equipment_type_id');

        // Get companies based on role
        $companies = collect();
        if (in_array($userRole, ['Admin', 'Management'])) {
            $companies = Company::where('is_active', true)->orderBy('company_name')->get();
            $selectedCompanyId = $request->get('company_id');
        } else {
            // For Supervisor, use their company_id
            $selectedCompanyId = $user->company_id;
            if ($selectedCompanyId) {
                $companies = Company::where('id', $selectedCompanyId)->where('is_active', true)->get();
            }
        }

        // Get equipment types based on role
        $equipmentTypes = collect();
        if (in_array($userRole, ['Admin', 'Management'])) {
            // Admin & Management can see all equipment types
            $equipmentTypes = EquipmentType::where('is_active', true)->get();
        } else {
            // PIC & Supervisor only see equipment types available in their company
            if ($user->company_id) {
                $equipmentTypes = EquipmentType::where('is_active', true)
                    ->whereHas('equipments.location.area', function ($q) use ($user) {
                        $q->where('company_id', $user->company_id);
                    })
                    ->get();
            }
        }

        // Get areas for selected company (with equipment type filtering)
        $areas = collect();
        if ($selectedCompanyId) {
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
        }

        // Get all equipment summary for chart calculations (without pagination)
        $equipmentSummaryAll = $this->getEquipmentSummary($selectedCompanyId, $selectedAreaId, $selectedEquipmentTypeId, $selectedMonth, $selectedYear);

        // Get monthly trends data
        $monthlyTrendsByEquipmentType = $this->getMonthlyTrendsByEquipmentType($selectedCompanyId, $selectedAreaId);

        // Get monthly status data by equipment type
        $monthlyStatusByEquipmentType = $this->getMonthlyStatusByEquipmentType($selectedCompanyId, $selectedAreaId, $selectedEquipmentTypeId);

        return view('dashboard', compact(
            'user',
            'userRole',
            'companies',
            'areas',
            'equipmentTypes',
            'selectedCompanyId',
            'selectedAreaId',
            'selectedEquipmentTypeId',
            'selectedMonth',
            'selectedYear',
            'equipmentSummaryAll',
            'monthlyTrendsByEquipmentType',
            'monthlyStatusByEquipmentType'
        ));
    }

    private function getEquipmentSummary($companyId, $areaId, $equipmentTypeId, $month, $year)
    {
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // Build equipment query
        $equipmentQuery = Equipment::with(['equipmentType', 'location.area.company']);

        // Apply filters
        if ($companyId) {
            $equipmentQuery->whereHas('location.area.company', function ($q) use ($companyId) {
                $q->where('id', $companyId);
            });
        }

        if ($areaId) {
            $equipmentQuery->whereHas('location', function ($q) use ($areaId) {
                $q->where('area_id', $areaId);
            });
        }

        if ($equipmentTypeId) {
            $equipmentQuery->where('equipment_type_id', $equipmentTypeId);
        }

        $equipmentQuery->where('is_active', true);

        $equipments = $equipmentQuery->get();

        $summary = [];

        foreach ($equipments as $equipment) {
            // Get latest inspection in the selected month
            $latestInspection = $equipment->inspections()
                ->whereBetween('inspection_date', [$startOfMonth, $endOfMonth])
                ->where('status', '!=', 'draft')
                ->orderBy('inspection_date', 'desc')
                ->first();

            $status = 'not_inspected';
            $inspectionDate = null;
            $ngCount = 0;

            if ($latestInspection) {
                $status = $latestInspection->status;
                $inspectionDate = $latestInspection->inspection_date;

                // Count NG items
                $ngCount = $latestInspection->details()
                    ->where('status', 'NG')
                    ->count();
            }

            $summary[] = [
                'equipment_code' => $equipment->equipment_code,
                'equipment_type' => $equipment->equipmentType->equipment_name ?? 'Unknown',
                'equipment_name' => $equipment->desc ?? '',
                'location' => $equipment->location->location_code ?? 'Unknown',
                'area' => $equipment->location->area->area_name ?? 'Unknown',
                'company' => $equipment->location->area->company->company_name ?? 'Unknown',
                'status' => $status,
                'inspection_date' => $inspectionDate,
                'ng_count' => $ngCount,
                'has_ng_items' => $ngCount > 0
            ];
        }

        return collect($summary);
    }

    private function getMonthlyTrendsByEquipmentType($companyId, $areaId)
    {
        $trends = [];
        $equipmentTypes = [];

        // Get equipment types first based on filters
        $equipmentTypeQuery = EquipmentType::where('is_active', true);

        if ($companyId || $areaId) {
            $equipmentTypeQuery->whereHas('equipments', function ($q) use ($companyId, $areaId) {
                if ($companyId) {
                    $q->whereHas('location.area.company', function ($sq) use ($companyId) {
                        $sq->where('id', $companyId);
                    });
                }
                if ($areaId) {
                    $q->whereHas('location', function ($sq) use ($areaId) {
                        $sq->where('area_id', $areaId);
                    });
                }
            });
        }

        $equipmentTypes = $equipmentTypeQuery->get();

        // Get last 6 months data (reduced from 12 for better visibility)
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $monthData = [
                'month' => $date->format('M y'),
                'year_month' => $date->format('Y-m'),
            ];

            foreach ($equipmentTypes as $equipmentType) {
                // Build inspections query for this month and equipment type
                $inspectionsQuery = \App\Models\Inspection::whereBetween('inspection_date', [$startOfMonth, $endOfMonth])
                    ->where('status', '!=', 'draft')
                    ->whereHas('equipment', function ($q) use ($equipmentType) {
                        $q->where('equipment_type_id', $equipmentType->id);
                    });

                // Apply company filter
                if ($companyId) {
                    $inspectionsQuery->whereHas('equipment.location.area.company', function ($q) use ($companyId) {
                        $q->where('id', $companyId);
                    });
                }

                // Apply area filter
                if ($areaId) {
                    $inspectionsQuery->whereHas('equipment.location', function ($q) use ($areaId) {
                        $q->where('area_id', $areaId);
                    });
                }

                $monthData[$equipmentType->equipment_name] = $inspectionsQuery->count();
            }

            $trends[] = $monthData;
        }

        return [
            'trends' => collect($trends),
            'equipmentTypes' => $equipmentTypes
        ];
    }

    private function getMonthlyStatusByEquipmentType($companyId, $areaId, $equipmentTypeId = null)
    {
        $equipmentTypeQuery = EquipmentType::where('is_active', true);

        // Apply filters
        if ($companyId || $areaId) {
            $equipmentTypeQuery->whereHas('equipments', function ($q) use ($companyId, $areaId) {
                if ($companyId) {
                    $q->whereHas('location.area.company', function ($sq) use ($companyId) {
                        $sq->where('id', $companyId);
                    });
                }
                if ($areaId) {
                    $q->whereHas('location', function ($sq) use ($areaId) {
                        $sq->where('area_id', $areaId);
                    });
                }
            });
        }

        // If specific equipment type is selected, filter to only that type
        if ($equipmentTypeId) {
            $equipmentTypeQuery->where('id', $equipmentTypeId);
        }

        $equipmentTypes = $equipmentTypeQuery->get();

        $statusData = [];

        foreach ($equipmentTypes as $equipmentType) {
            $monthlyData = [];

            // Get last 12 months data
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $startOfMonth = $date->copy()->startOfMonth();
                $endOfMonth = $date->copy()->endOfMonth();

                // Get all equipment of this type
                $equipmentQuery = Equipment::where('equipment_type_id', $equipmentType->id)
                    ->where('is_active', true);

                // Apply company/area filters
                if ($companyId) {
                    $equipmentQuery->whereHas('location.area.company', function ($q) use ($companyId) {
                        $q->where('id', $companyId);
                    });
                }

                if ($areaId) {
                    $equipmentQuery->whereHas('location', function ($q) use ($areaId) {
                        $q->where('area_id', $areaId);
                    });
                }

                $equipments = $equipmentQuery->get();

                $okCount = 0;
                $ngCount = 0;
                $totalEquipment = $equipments->count();

                foreach ($equipments as $equipment) {
                    // Get latest inspection in this month
                    $latestInspection = $equipment->inspections()
                        ->whereBetween('inspection_date', [$startOfMonth, $endOfMonth])
                        ->where('status', '!=', 'draft')
                        ->orderBy('inspection_date', 'desc')
                        ->first();

                    if ($latestInspection) {
                        // Check if inspection has any NG items
                        $hasNgItems = $latestInspection->details()
                            ->where('status', 'NG')
                            ->exists();

                        if ($hasNgItems) {
                            $ngCount++;
                        } else {
                            $okCount++;
                        }
                    }
                }

                $monthlyData[] = [
                    'month' => $date->format('M'),
                    'year_month' => $date->format('Y-m'),
                    'ok_count' => $okCount,
                    'ng_count' => $ngCount,
                    'total_equipment' => $totalEquipment,
                    'inspected_count' => $okCount + $ngCount
                ];
            }

            $statusData[] = [
                'equipment_type' => $equipmentType,
                'monthly_data' => $monthlyData,
                'total_equipment' => $equipments->count()
            ];
        }

        return collect($statusData);
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

        // Format response to match expected structure
        $formattedAreas = $areas->map(function ($area) {
            return [
                'id' => $area->id,
                'area_name' => $area->area_name
            ];
        });

        return response()->json($formattedAreas);
    }
}
