<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Area;
use App\Models\EquipmentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Get aggregated chart data without loading every equipment row into memory
        $dashboardChartData = $this->getDashboardChartData($selectedCompanyId, $selectedAreaId, $selectedEquipmentTypeId, $selectedMonth, $selectedYear);
        $monthlyTrendsByEquipmentType = ['trends' => collect(), 'equipmentTypes' => collect()];

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
            'dashboardChartData',
            'monthlyTrendsByEquipmentType',
            'monthlyStatusByEquipmentType'
        ));
    }

    private function getDashboardChartData($companyId, $areaId, $equipmentTypeId, $month, $year)
    {
        $equipmentTypeRows = DB::query()
            ->fromSub($this->equipmentStatusSummaryBaseQuery($companyId, $areaId, $equipmentTypeId, $month, $year), 'summary')
            ->selectRaw('equipment_type, COUNT(*) as total')
            ->groupBy('equipment_type')
            ->orderBy('equipment_type')
            ->get();

        $companyRows = DB::query()
            ->fromSub($this->equipmentStatusSummaryBaseQuery($companyId, $areaId, $equipmentTypeId, $month, $year), 'summary')
            ->selectRaw('company, status, COUNT(*) as total')
            ->groupBy('company', 'status')
            ->get()
            ->groupBy('company');

        $areaRows = DB::query()
            ->fromSub($this->equipmentStatusSummaryBaseQuery($companyId, $areaId, $equipmentTypeId, $month, $year), 'summary')
            ->selectRaw('area, status, COUNT(*) as total')
            ->groupBy('area', 'status')
            ->get()
            ->groupBy('area')
            ->take(10);

        $ngRows = DB::query()
            ->fromSub($this->equipmentStatusSummaryBaseQuery($companyId, $areaId, $equipmentTypeId, $month, $year), 'summary')
            ->selectRaw('SUM(CASE WHEN ng_count = 0 THEN 1 ELSE 0 END) as no_ng_count, SUM(CASE WHEN ng_count > 0 THEN 1 ELSE 0 END) as with_ng_count')
            ->first();

        return [
            'equipmentTypeData' => [
                'labels' => $equipmentTypeRows->pluck('equipment_type')->values(),
                'datasets' => [[
                    'label' => 'Equipment Count',
                    'data' => $equipmentTypeRows->pluck('total')->map(fn ($value) => (int) $value)->values(),
                    'backgroundColor' => [
                        '#007bff',
                        '#28a745',
                        '#ffc107',
                        '#dc3545',
                        '#17a2b8',
                        '#6f42c1',
                        '#e83e8c',
                        '#fd7e14',
                        '#20c997',
                        '#6c757d',
                    ],
                    'borderWidth' => 1,
                ]],
            ],
            'companyData' => $this->buildStatusChartData($companyRows),
            'areaData' => $this->buildStatusChartData($areaRows),
            'ngItemsData' => [
                'labels' => ['No NG Items', 'With NG Items'],
                'datasets' => [[
                    'data' => [
                        (int) ($ngRows->no_ng_count ?? 0),
                        (int) ($ngRows->with_ng_count ?? 0),
                    ],
                    'backgroundColor' => ['#28a745', '#dc3545'],
                    'borderWidth' => 2,
                    'borderColor' => '#fff',
                ]],
            ],
        ];
    }

    private function equipmentStatusSummaryBaseQuery($companyId, $areaId, $equipmentTypeId, $month, $year)
    {
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        return $this->filteredEquipmentBaseQuery($companyId, $areaId, $equipmentTypeId)
            ->leftJoin('tt_inspections as i', function ($join) use ($startOfMonth, $endOfMonth) {
                $join->whereRaw(
                    "i.id = (
                        SELECT ti.id
                        FROM tt_inspections ti
                        WHERE ti.equipment_id = e.id
                            AND ti.inspection_date BETWEEN ? AND ?
                            AND ti.status != ?
                        ORDER BY ti.inspection_date DESC, ti.id DESC
                        LIMIT 1
                    )",
                    [$startOfMonth->toDateString(), $endOfMonth->toDateString(), 'draft']
                );
            })
            ->leftJoin('tt_inspection_details as d', function ($join) {
                $join->on('d.inspection_id', '=', 'i.id')
                    ->where('d.status', '=', 'NG');
            })
            ->select([
                DB::raw('COALESCE(et.equipment_name, "Unknown") as equipment_type'),
                DB::raw('COALESCE(a.area_name, "Unknown") as area'),
                DB::raw('COALESCE(c.company_name, "Unknown") as company'),
                DB::raw('COALESCE(i.status, "not_inspected") as status'),
                DB::raw('COUNT(d.id) as ng_count'),
            ])
            ->groupBy(
                'e.id',
                'et.equipment_name',
                'a.area_name',
                'c.company_name',
                'i.status'
            );
    }

    private function buildStatusChartData($rowsByLabel)
    {
        $labels = $rowsByLabel->keys()->values();
        $statuses = [
            'approved' => '#28a745',
            'pending' => '#ffc107',
            'rejected' => '#dc3545',
            'not_inspected' => '#6c757d',
        ];

        return [
            'labels' => $labels,
            'datasets' => collect($statuses)->map(function ($color, $status) use ($rowsByLabel) {
                return [
                    'label' => $status === 'not_inspected' ? 'Not Inspected' : ucfirst($status),
                    'data' => $rowsByLabel->map(function ($rows) use ($status) {
                        return (int) optional($rows->firstWhere('status', $status))->total;
                    })->values(),
                    'backgroundColor' => $color,
                ];
            })->values(),
        ];
    }

    private function getMonthlyTrendsByEquipmentType($companyId, $areaId, $selectedYear = null)
    {
        $trends = [];

        $equipmentTypes = $this->filteredEquipmentTypesQuery($companyId, $areaId)->get();
        $baseYear = $selectedYear ? (int) $selectedYear : Carbon::now()->year;
        $startDate = Carbon::create($baseYear, 7, 1)->startOfMonth();
        $endDate = Carbon::create($baseYear, 12, 1)->endOfMonth();

        $inspectionCountsQuery = DB::table('tt_inspections')
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
        $baseYear = $selectedYear ? (int) $selectedYear : Carbon::now()->year;
        $startDate = Carbon::create($baseYear, 1, 1)->startOfYear();
        $endDate = Carbon::create($baseYear, 12, 1)->endOfYear();
        $equipmentTypes = $this->filteredEquipmentTypesQuery($companyId, $areaId, $equipmentTypeId)
            ->get()
            ->keyBy('id');

        $equipmentTotals = $this->filteredEquipmentBaseQuery($companyId, $areaId, $equipmentTypeId)
            ->selectRaw('e.equipment_type_id, COUNT(e.id) as total_equipment, MIN(pc.period_check) as period_check')
            ->groupBy('e.equipment_type_id')
            ->get()
            ->keyBy('equipment_type_id');

        $ngPerInspection = DB::table('tt_inspection_details')
            ->selectRaw('inspection_id, COUNT(*) as ng_count')
            ->where('status', 'NG')
            ->groupBy('inspection_id');

        $monthlyStatus = $this->filteredEquipmentBaseQuery($companyId, $areaId, $equipmentTypeId)
            ->join('tt_inspections as i', function ($join) use ($startDate, $endDate) {
                $join->on('i.equipment_id', '=', 'e.id')
                    ->whereBetween('i.inspection_date', [$startDate->toDateString(), $endDate->toDateString()])
                    ->where('i.status', '!=', 'draft');
            })
            ->leftJoinSub($ngPerInspection, 'ng', function ($join) {
                $join->on('ng.inspection_id', '=', 'i.id');
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tt_inspections as newer')
                    ->whereColumn('newer.equipment_id', 'i.equipment_id')
                    ->whereRaw('YEAR(newer.inspection_date) = YEAR(i.inspection_date)')
                    ->whereRaw('MONTH(newer.inspection_date) = MONTH(i.inspection_date)')
                    ->where('newer.status', '!=', 'draft')
                    ->where(function ($q) {
                        $q->whereColumn('newer.inspection_date', '>', 'i.inspection_date')
                            ->orWhere(function ($sq) {
                                $sq->whereColumn('newer.inspection_date', '=', 'i.inspection_date')
                                    ->whereColumn('newer.id', '>', 'i.id');
                            });
                    });
            })
            ->selectRaw('
                e.equipment_type_id,
                MONTH(i.inspection_date) as month_number,
                SUM(CASE WHEN COALESCE(ng.ng_count, 0) > 0 THEN 1 ELSE 0 END) as ng_count,
                SUM(CASE WHEN COALESCE(ng.ng_count, 0) = 0 THEN 1 ELSE 0 END) as ok_count
            ')
            ->groupByRaw('e.equipment_type_id, MONTH(i.inspection_date)')
            ->get()
            ->keyBy(function ($row) {
                return $row->equipment_type_id . '-' . $row->month_number;
            });

        $statusData = [];
        foreach ($equipmentTypes as $equipmentType) {
            $monthlyData = [];
            $totalData = $equipmentTotals->get($equipmentType->id);
            $periodCheck = $totalData->period_check ?? 'Not Set';
            $totalEquipment = (int) optional($totalData)->total_equipment;

            // Get 12 months data for the selected year (January to December)
            for ($month = 1; $month <= 12; $month++) {
                $date = Carbon::create($baseYear, $month, 1);
                $statusCount = $monthlyStatus->get($equipmentType->id . '-' . $month);
                $okCount = (int) optional($statusCount)->ok_count;
                $ngCount = (int) optional($statusCount)->ng_count;

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

    private function filteredEquipmentBaseQuery($companyId = null, $areaId = null, $equipmentTypeId = null)
    {
        $query = DB::table('tm_equipments as e')
            ->join('tm_equipment_types as et', 'e.equipment_type_id', '=', 'et.id')
            ->join('tm_locations_new as l', 'e.location_id', '=', 'l.id')
            ->leftJoin('tm_areas as a', 'l.area_id', '=', 'a.id')
            ->leftJoin('tm_companies as c', 'l.company_id', '=', 'c.id')
            ->leftJoin('tm_period_checks as pc', 'e.period_check_id', '=', 'pc.id')
            ->where('e.is_active', true);

        if ($companyId) {
            $query->where('l.company_id', $companyId);
        }

        if ($areaId) {
            $query->where('l.area_id', $areaId);
        }

        if ($equipmentTypeId) {
            $query->where('e.equipment_type_id', $equipmentTypeId);
        }

        return $query;
    }

    private function filteredEquipmentTypesQuery($companyId = null, $areaId = null, $equipmentTypeId = null)
    {
        $query = DB::table('tm_equipment_types as et')
            ->select('et.id', 'et.equipment_name', 'et.equipment_type')
            ->where('et.is_active', true);

        if ($companyId || $areaId) {
            $query->whereExists(function ($exists) use ($companyId, $areaId) {
                $exists->select(DB::raw(1))
                    ->from('tm_equipments as e')
                    ->join('tm_locations_new as l', 'e.location_id', '=', 'l.id')
                    ->whereColumn('e.equipment_type_id', 'et.id')
                    ->where('e.is_active', true);

                if ($companyId) {
                    $exists->where('l.company_id', $companyId);
                }

                if ($areaId) {
                    $exists->where('l.area_id', $areaId);
                }
            });
        }

        if ($equipmentTypeId) {
            $query->where('et.id', $equipmentTypeId);
        }

        return $query->orderBy('et.equipment_name')->orderBy('et.equipment_type');
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
