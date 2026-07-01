<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Company;
use App\Models\Area;
use App\Models\EquipmentType;
use App\Models\Inspection;
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
        $monthlyTrendsByEquipmentType = $this->getMonthlyTrendsByEquipmentType($selectedCompanyId, $selectedAreaId, $selectedYear);

        // Get monthly status data by equipment type
        $monthlyStatusByEquipmentType = $this->getMonthlyStatusByEquipmentType($selectedCompanyId, $selectedAreaId, $selectedEquipmentTypeId, $selectedYear);

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

        $equipments = $this->equipmentQuery($companyId, $areaId, $equipmentTypeId)
            ->with(['equipmentType', 'location.area.company', 'periodCheck'])
            ->get();

        $latestInspections = Inspection::whereIn('equipment_id', $equipments->pluck('id'))
            ->whereBetween('inspection_date', [$startOfMonth, $endOfMonth])
            ->where('status', '!=', 'draft')
            ->withCount([
                'details as ng_count' => function ($q) {
                    $q->where('status', 'NG');
                }
            ])
            ->orderByDesc('inspection_date')
            ->orderByDesc('id')
            ->get()
            ->unique('equipment_id')
            ->keyBy('equipment_id');

        $summary = [];

        foreach ($equipments as $equipment) {
            $latestInspection = $latestInspections->get($equipment->id);

            $status = 'not_inspected';
            $inspectionDate = null;
            $ngCount = 0;

            if ($latestInspection) {
                $status = $latestInspection->status;
                $inspectionDate = $latestInspection->inspection_date;
                $ngCount = (int) $latestInspection->ng_count;
            }

            $summary[] = [
                'equipment_code' => $equipment->equipment_code,
                'equipment_type' => $equipment->equipmentType->equipment_name ?? 'Unknown',
                'equipment_name' => $equipment->desc ?? '',
                'location' => $equipment->location->location_code ?? 'Unknown',
                'area' => $equipment->location->area->area_name ?? 'Unknown',
                'company' => $equipment->location->area->company->company_name ?? 'Unknown',
                'period_check' => $equipment->periodCheck->period_check ?? 'Not Set',
                'status' => $status,
                'inspection_date' => $inspectionDate,
                'ng_count' => $ngCount,
                'has_ng_items' => $ngCount > 0
            ];
        }

        return collect($summary);
    }

    private function getMonthlyTrendsByEquipmentType($companyId, $areaId, $selectedYear = null)
    {
        $trends = [];

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
        $baseYear = $selectedYear ? (int) $selectedYear : Carbon::now()->year;
        $startDate = Carbon::create($baseYear, 7, 1)->startOfMonth();
        $endDate = Carbon::create($baseYear, 12, 1)->endOfMonth();

        $inspectionCountsQuery = Inspection::query()
            ->selectRaw('MONTH(tt_inspections.inspection_date) as month_number, tm_equipments.equipment_type_id, COUNT(*) as total')
            ->join('tm_equipments', 'tt_inspections.equipment_id', '=', 'tm_equipments.id')
            ->join('tm_locations_new', 'tm_equipments.location_id', '=', 'tm_locations_new.id')
            ->whereBetween('tt_inspections.inspection_date', [$startDate, $endDate])
            ->where('tt_inspections.status', '!=', 'draft')
            ->where('tm_equipments.is_active', true);

        if ($companyId) {
            $inspectionCountsQuery->where('tm_locations_new.company_id', $companyId);
        }

        if ($areaId) {
            $inspectionCountsQuery->where('tm_locations_new.area_id', $areaId);
        }

        $inspectionCounts = $inspectionCountsQuery
            ->groupByRaw('MONTH(tt_inspections.inspection_date), tm_equipments.equipment_type_id')
            ->get()
            ->keyBy(function ($row) {
                return $row->month_number . '-' . $row->equipment_type_id;
            });

        // Get last 6 months data for the selected year (July to December)
        for ($month = 7; $month <= 12; $month++) {
            $date = Carbon::create($baseYear, $month, 1);

            $monthData = [
                'month' => $date->format('M y'),
                'year_month' => $date->format('Y-m'),
            ];

            foreach ($equipmentTypes as $equipmentType) {
                $key = $month . '-' . $equipmentType->id;
                $monthData[$equipmentType->equipment_name] = (int) optional($inspectionCounts->get($key))->total;
            }

            $trends[] = $monthData;
        }

        return [
            'trends' => collect($trends),
            'equipmentTypes' => $equipmentTypes
        ];
    }

    private function getMonthlyStatusByEquipmentType($companyId, $areaId, $equipmentTypeId = null, $selectedYear = null)
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

        $baseYear = $selectedYear ? (int) $selectedYear : Carbon::now()->year;
        $startDate = Carbon::create($baseYear, 1, 1)->startOfYear();
        $endDate = Carbon::create($baseYear, 12, 1)->endOfYear();
        $equipments = $this->equipmentQuery($companyId, $areaId)
            ->whereIn('equipment_type_id', $equipmentTypes->pluck('id'))
            ->with('periodCheck')
            ->get();
        $equipmentsByType = $equipments->groupBy('equipment_type_id');
        $equipmentIds = $equipments->pluck('id');

        $latestInspectionsByEquipmentMonth = Inspection::whereIn('equipment_id', $equipmentIds)
            ->whereBetween('inspection_date', [$startDate, $endDate])
            ->where('status', '!=', 'draft')
            ->withCount([
                'details as ng_count' => function ($q) {
                    $q->where('status', 'NG');
                }
            ])
            ->orderByDesc('inspection_date')
            ->orderByDesc('id')
            ->get()
            ->groupBy(function ($inspection) {
                return $inspection->equipment_id . '-' . $inspection->inspection_date->format('n');
            })
            ->map(function ($inspections) {
                return $inspections->first();
            });

        $statusData = [];
        foreach ($equipmentTypes as $equipmentType) {
            $monthlyData = [];
            $equipmentsForType = $equipmentsByType->get($equipmentType->id, collect());
            $periodCheck = optional(optional($equipmentsForType->first())->periodCheck)->period_check ?? 'Not Set';
            $totalEquipment = $equipmentsForType->count();

            // Get 12 months data for the selected year (January to December)
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::create($baseYear, $month, 1);

                $okCount = 0;
                $ngCount = 0;

                foreach ($equipmentsForType as $equipment) {
                    $latestInspection = $latestInspectionsByEquipmentMonth->get($equipment->id . '-' . $month);

                    if ($latestInspection) {
                        if ((int) $latestInspection->ng_count > 0) {
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
                'period_check' => $periodCheck,
                'total_equipment' => $totalEquipment
            ];
        }

        return collect($statusData);
    }

    private function equipmentQuery($companyId = null, $areaId = null, $equipmentTypeId = null)
    {
        $query = Equipment::where('is_active', true);

        if ($companyId) {
            $query->whereHas('location', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
        }

        if ($areaId) {
            $query->whereHas('location', function ($q) use ($areaId) {
                $q->where('area_id', $areaId);
            });
        }

        if ($equipmentTypeId) {
            $query->where('equipment_type_id', $equipmentTypeId);
        }

        return $query;
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
