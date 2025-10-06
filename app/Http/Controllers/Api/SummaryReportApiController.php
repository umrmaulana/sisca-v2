<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\Equipment;
use App\Models\Company;
use App\Models\Area;
use App\Models\EquipmentType;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SummaryReportApiController extends Controller
{
    /**
     * Get summary report data for AII company only
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAiiSummaryReport(Request $request)
    {
        try {
            // Validasi parameter
            $year = $request->get('year', date('Y'));
            $areaId = $request->get('area_id');
            $equipmentTypeId = $request->get('equipment_type_id');
            $searchEquipment = $request->get('search_equipment', '');
            $inspectionResult = $request->get('inspection_result', 'all'); // all, ok, ng

            // Validasi year
            if (!is_numeric($year) || $year < 2020 || $year > date('Y')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid year parameter'
                ], 400);
            }

            // Get AII company ID (hardcoded for security) 
            // Note: Based on user's data sample, the company name is "PT. AII"
            $aiiCompany = Company::where(function ($query) {
                $query->where('company_name', '=', 'PT. AII')
                    ->orWhere('company_description', 'LIKE', '%PT. Aisin Indonesia');
            })
                ->where('is_active', true)
                ->first();


            if (!$aiiCompany) {
                return response()->json([
                    'success' => false,
                    'message' => 'AII company not found'
                ], 404);
            }

            $companyId = $aiiCompany->id;

            // Get equipment summary untuk AII
            $equipmentSummary = $this->getAiiEquipmentSummary(
                $companyId,
                $areaId,
                $equipmentTypeId,
                $year,
                $searchEquipment,
                $inspectionResult
            );

            // Get comprehensive statistics
            $statistics = $this->getAiiComprehensiveStats($companyId, $areaId, $equipmentTypeId, $year, $searchEquipment);

            // Get available areas untuk AII
            $availableAreas = Area::where('company_id', $companyId)
                ->where('is_active', true)
                ->select('id', 'area_name')
                ->orderBy('area_name')
                ->get();

            // Get available equipment types untuk AII
            $availableEquipmentTypes = EquipmentType::where('is_active', true)
                ->whereHas('equipments.location.area', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                })
                ->select('id', 'equipment_name', 'equipment_type')
                ->orderBy('equipment_name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'company' => [
                        'id' => $aiiCompany->id,
                        'name' => $aiiCompany->company_name
                    ],
                    'filters' => [
                        'year' => $year,
                        'area_id' => $areaId,
                        'equipment_type_id' => $equipmentTypeId,
                        'search_equipment' => $searchEquipment,
                        'inspection_result' => $inspectionResult
                    ],
                    'statistics' => $statistics,
                    'equipment_summary' => $equipmentSummary,
                    'available_filters' => [
                        'areas' => $availableAreas,
                        'equipment_types' => $availableEquipmentTypes
                    ]
                ],
                'generated_at' => now()->toISOString(),
                'total_records' => $equipmentSummary->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => config('app.debug') ? $e->getMessage() : 'Something went wrong'
            ], 500);
        }
    }

    /**
     * Get equipment summary for AII company
     */
    private function getAiiEquipmentSummary($companyId, $areaId, $equipmentTypeId, $year, $searchEquipment = '', $inspectionResult = 'all')
    {
        $startOfYear = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endOfYear = Carbon::createFromDate($year, 12, 31)->endOfYear();

        // Build equipment query untuk AII saja
        $equipmentQuery = Equipment::select([
            'id',
            'equipment_code',
            'equipment_type_id',
            'location_id'
        ])->with([
                    'equipmentType:id,equipment_name,equipment_type',
                    'location:id,location_code,area_id',
                    'location.area:id,area_name,company_id',
                    'location.area.company:id,company_name'
                ]);

        // Filter by AII company
        $equipmentQuery->whereHas('location.area', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });

        // Apply additional filters
        if ($areaId) {
            $equipmentQuery->whereHas('location', function ($q) use ($areaId) {
                $q->where('area_id', $areaId);
            });
        }

        if ($equipmentTypeId) {
            $equipmentQuery->where('equipment_type_id', $equipmentTypeId);
        }

        if (!empty($searchEquipment)) {
            $equipmentQuery->where('equipment_code', 'like', "%{$searchEquipment}%");
        }

        $equipmentQuery->where('is_active', true);
        $equipments = $equipmentQuery->get();

        // Get all inspections for these equipments in one query for better performance
        $equipmentIds = $equipments->pluck('id');
        $inspections = Inspection::select([
            'id',
            'equipment_id',
            'inspection_date',
            'status',
            'approved_by',
            'approved_at',
            'notes'
        ])->with([
                    'details:id,inspection_id,status',
                    'approvedBy:id,name'
                ])->whereIn('equipment_id', $equipmentIds)
            ->whereBetween('inspection_date', [$startOfYear, $endOfYear])
            ->where('status', '!=', 'draft')
            ->orderBy('inspection_date', 'desc')
            ->get()
            ->groupBy('equipment_id');

        $summary = [];
        foreach ($equipments as $equipment) {
            $equipmentInspections = $inspections->get($equipment->id, collect());

            // Get monthly inspection status (Jan-Dec)
            $monthlyStatus = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $monthEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth();

                $monthInspections = $equipmentInspections->whereBetween('inspection_date', [$monthStart, $monthEnd]);
                $latestInspection = $monthInspections->first(); // Already ordered by desc

                $status = 'not_inspected';
                $ngCount = 0;
                $totalItems = 0;
                $inspectionId = null;
                $inspectionDate = null;
                $approvedBy = null;
                $approvedAt = null;

                if ($latestInspection) {
                    $status = $latestInspection->status;
                    $inspectionId = $latestInspection->id;
                    $inspectionDate = $latestInspection->inspection_date;
                    $approvedBy = $latestInspection->approvedBy ? $latestInspection->approvedBy->name : null;
                    $approvedAt = $latestInspection->approved_at;

                    $ngCount = $latestInspection->details->where('status', 'NG')->count();
                    $totalItems = $latestInspection->details->count();
                }

                $monthlyStatus[Carbon::createFromDate($year, $month, 1)->format('M')] = [
                    'status' => $status,
                    'inspection_id' => $inspectionId,
                    'inspection_date' => $inspectionDate,
                    'ng_count' => $ngCount,
                    'total_items' => $totalItems,
                    'has_ng_items' => $ngCount > 0,
                    'approved_by' => $approvedBy,
                    'approved_at' => $approvedAt
                ];
            }

            // Calculate overall status for the year
            $totalInspections = $equipmentInspections->count();
            $approvedInspections = $equipmentInspections->where('status', 'approved')->count();
            $ngInspections = $equipmentInspections->filter(function ($inspection) {
                return $inspection->details->where('status', 'NG')->count() > 0;
            })->count();

            $overallStatus = 'not_inspected';
            if ($totalInspections > 0) {
                if ($ngInspections > 0) {
                    $overallStatus = 'has_issues';
                } elseif ($approvedInspections > 0) {
                    $overallStatus = 'good';
                } else {
                    $overallStatus = 'pending';
                }
            }

            $equipmentData = [
                'equipment_code' => $equipment->equipment_code,
                'equipment_name' => $equipment->equipmentType->equipment_name ?? 'Unknown',
                'equipment_type' => $equipment->equipmentType->equipment_type ?? 'Unknown',
                'location' => $equipment->location->location_code ?? 'Unknown',
                'area' => $equipment->location->area->area_name ?? 'Unknown',
                'company' => $equipment->location->area->company->company_name ?? 'Unknown',
                'overall_status' => $overallStatus,
                'total_inspections' => $totalInspections,
                'approved_inspections' => $approvedInspections,
                'ng_inspections' => $ngInspections,
                'monthly_status' => $monthlyStatus,
                'last_inspection' => optional($equipmentInspections->first())->inspection_date?->format('Y-m-d') ?? 'Never',
            ];

            // Apply inspection result filter
            if ($inspectionResult !== 'all') {
                if ($inspectionResult === 'ok' && $overallStatus !== 'good') {
                    continue;
                }
                if ($inspectionResult === 'ng' && $overallStatus !== 'has_issues') {
                    continue;
                }
            }

            $summary[] = $equipmentData;
        }

        return collect($summary);
    }

    /**
     * Get comprehensive statistics for AII company
     */
    private function getAiiComprehensiveStats($companyId, $areaId, $equipmentTypeId, $year, $searchEquipment = '')
    {
        $startOfYear = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endOfYear = Carbon::createFromDate($year, 12, 31)->endOfYear();

        // Get total areas for AII
        $totalAreas = Area::where('company_id', $companyId)
            ->where('is_active', true)
            ->count();

        // Get total equipment for AII (with filters)
        $equipmentQuery = Equipment::whereHas('location.area', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });

        if ($areaId) {
            $equipmentQuery->whereHas('location', function ($q) use ($areaId) {
                $q->where('area_id', $areaId);
            });
        }

        if ($equipmentTypeId) {
            $equipmentQuery->where('equipment_type_id', $equipmentTypeId);
        }

        if (!empty($searchEquipment)) {
            $equipmentQuery->where('equipment_code', 'like', "%{$searchEquipment}%");
        }

        $totalEquipment = $equipmentQuery->count();

        // Get total equipment types for AII
        $totalEquipmentTypes = EquipmentType::where('is_active', true)
            ->whereHas('equipments.location.area', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->count();

        // Get inspection statistics
        $inspectionStats = $this->getAiiInspectionStats($companyId, $areaId, $equipmentTypeId, $year, $searchEquipment);

        return [
            'total_areas' => $totalAreas,
            'total_equipment' => $totalEquipment,
            'total_equipment_types' => $totalEquipmentTypes,
            'total_inspections' => $inspectionStats['total_inspections'],
            'approved_inspections' => $inspectionStats['approved_inspections'],
            'pending_inspections' => $inspectionStats['pending_inspections'],
            'rejected_inspections' => $inspectionStats['rejected_inspections'],
            'approval_rate' => $inspectionStats['approval_rate']
        ];
    }

    /**
     * Get inspection statistics for AII company
     */
    private function getAiiInspectionStats($companyId, $areaId, $equipmentTypeId, $year, $searchEquipment = '')
    {
        $startOfYear = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endOfYear = Carbon::createFromDate($year, 12, 31)->endOfYear();

        // Build inspections query untuk AII
        $inspectionsQuery = Inspection::whereBetween('inspection_date', [$startOfYear, $endOfYear])
            ->where('status', '!=', 'draft')
            ->whereHas('equipment.location.area', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });

        // Apply filters
        if ($areaId) {
            $inspectionsQuery->whereHas('equipment.location', function ($q) use ($areaId) {
                $q->where('area_id', $areaId);
            });
        }

        if ($equipmentTypeId) {
            $inspectionsQuery->whereHas('equipment', function ($q) use ($equipmentTypeId) {
                $q->where('equipment_type_id', $equipmentTypeId);
            });
        }

        if (!empty($searchEquipment)) {
            $inspectionsQuery->whereHas('equipment', function ($q) use ($searchEquipment) {
                $q->where('equipment_code', 'like', "%{$searchEquipment}%");
            });
        }

        $totalInspections = $inspectionsQuery->count();
        $approvedInspections = (clone $inspectionsQuery)->where('status', 'approved')->count();
        $pendingInspections = (clone $inspectionsQuery)->where('status', 'pending')->count();
        $rejectedInspections = (clone $inspectionsQuery)->where('status', 'rejected')->count();

        return [
            'total_inspections' => $totalInspections,
            'approved_inspections' => $approvedInspections,
            'pending_inspections' => $pendingInspections,
            'rejected_inspections' => $rejectedInspections,
            'approval_rate' => $totalInspections > 0 ? round(($approvedInspections / $totalInspections) * 100, 2) : 0
        ];
    }

    /**
     * Get areas for AII company only
     */
    public function getAiiAreas()
    {
        try {
            $aiiCompany = Company::where(function ($query) {
                $query->where('company_name', '=', 'PT. AII')
                    ->orWhere('company_description', 'LIKE', '%PT. Aisin Indonesia');
            })
                ->where('is_active', true)
                ->first();

            if (!$aiiCompany) {
                return response()->json([
                    'success' => false,
                    'message' => 'AII company not found'
                ], 404);
            }

            $areas = Area::where('company_id', $aiiCompany->id)
                ->where('is_active', true)
                ->select('id', 'area_name')
                ->orderBy('area_name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $areas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get equipment types for AII company only
     */
    public function getAiiEquipmentTypes()
    {
        try {
            $aiiCompany = Company::where(function ($query) {
                $query->where('company_name', '=', 'PT. AII')
                    ->orWhere('company_description', 'LIKE', '%PT. Aisin Indonesia');
            })
                ->where('is_active', true)
                ->first();

            if (!$aiiCompany) {
                return response()->json([
                    'success' => false,
                    'message' => 'AII company not found'
                ], 404);
            }

            $equipmentTypes = EquipmentType::where('is_active', true)
                ->whereHas('equipments.location.area', function ($q) use ($aiiCompany) {
                    $q->where('company_id', $aiiCompany->id);
                })
                ->select('id', 'equipment_name', 'equipment_type')
                ->orderBy('equipment_name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $equipmentTypes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get inspections list for AII company with OK/NG status
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAiiInspectionsList(Request $request)
    {
        try {
            // Validasi parameter
            $year = $request->get('year', date('Y'));
            $areaId = $request->get('area_id');
            $equipmentTypeId = $request->get('equipment_type_id');
            $searchEquipment = $request->get('search_equipment', '');
            $limit = $request->get('limit', 50); // Default limit 50 inspections

            // Validasi year
            if (!is_numeric($year) || $year < 2020 || $year > date('Y')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid year parameter'
                ], 400);
            }

            // Get AII company ID (hardcoded for security) 
            $aiiCompany = Company::where(function ($query) {
                $query->where('company_name', '=', 'PT. AII')
                    ->orWhere('company_description', 'LIKE', '%PT. Aisin Indonesia');
            })
                ->where('is_active', true)
                ->first();

            if (!$aiiCompany) {
                return response()->json([
                    'success' => false,
                    'message' => 'AII company not found'
                ], 404);
            }

            $companyId = $aiiCompany->id;

            // Get inspections list untuk AII
            $inspectionsList = $this->getAiiInspectionsData(
                $companyId,
                $areaId,
                $equipmentTypeId,
                $year,
                $searchEquipment,
                $limit
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'company' => [
                        'id' => $aiiCompany->id,
                        'name' => $aiiCompany->company_name
                    ],
                    'filters' => [
                        'year' => $year,
                        'area_id' => $areaId,
                        'equipment_type_id' => $equipmentTypeId,
                        'search_equipment' => $searchEquipment,
                        'limit' => $limit
                    ],
                    'inspections' => $inspectionsList
                ],
                'generated_at' => now(),
                'total_records' => count($inspectionsList)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching inspections data',
                'error' => config('app.debug') ? $e->getMessage() : 'Something went wrong'
            ], 500);
        }
    }

    /**
     * Get inspections data for AII company with OK/NG status only
     */
    private function getAiiInspectionsData($companyId, $areaId, $equipmentTypeId, $year, $searchEquipment = '', $limit = 50)
    {
        $startOfYear = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endOfYear = Carbon::createFromDate($year, 12, 31)->endOfYear();

        // Build inspections query untuk AII
        $inspectionsQuery = Inspection::select([
            'id',
            'equipment_id',
            'inspection_date',
            'status',
            'notes',
            'user_id',
            'approved_by',
            'approved_at'
        ])->with([
                    'equipment:id,equipment_code,equipment_type_id,location_id',
                    'equipment.equipmentType:id,equipment_name,equipment_type',
                    'equipment.location:id,location_code,area_id',
                    'equipment.location.area:id,area_name,company_id',
                    'equipment.location.area.company:id,company_name',
                    'details:id,inspection_id,status',
                    'user:id,name',
                    'approvedBy:id,name'
                ]);

        // Date range filter
        $inspectionsQuery->whereBetween('inspection_date', [$startOfYear, $endOfYear]);

        // Only show non-draft inspections
        $inspectionsQuery->where('status', '!=', 'draft');

        // Filter by AII company
        $inspectionsQuery->whereHas('equipment.location.area', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });

        // Apply additional filters
        if ($areaId) {
            $inspectionsQuery->whereHas('equipment.location', function ($q) use ($areaId) {
                $q->where('area_id', $areaId);
            });
        }

        if ($equipmentTypeId) {
            $inspectionsQuery->whereHas('equipment', function ($q) use ($equipmentTypeId) {
                $q->where('equipment_type_id', $equipmentTypeId);
            });
        }

        if (!empty($searchEquipment)) {
            $inspectionsQuery->whereHas('equipment', function ($q) use ($searchEquipment) {
                $q->where('equipment_code', 'like', "%{$searchEquipment}%");
            });
        }

        $inspections = $inspectionsQuery->orderBy('inspection_date', 'desc')
            ->limit($limit)
            ->get();

        $inspectionsList = [];
        foreach ($inspections as $inspection) {
            // Determine inspection result based on NG items
            $ngCount = $inspection->details->where('status', 'NG')->count();
            $totalItems = $inspection->details->count();
            $hasNgItems = $ngCount > 0;

            // Set result as OK/NG instead of approval status
            $result = $hasNgItems ? 'NG' : 'OK';

            $inspectionsList[] = [
                'id' => $inspection->id,
                'date' => $inspection->inspection_date->format('Y-m-d'),
                'date_formatted' => $inspection->inspection_date->format('d/m/Y'),
                'equipment_code' => $inspection->equipment->equipment_code ?? 'N/A',
                'equipment_name' => $inspection->equipment->equipmentType->equipment_name ?? 'N/A',
                'equipment_type' => $inspection->equipment->equipmentType->equipment_type ?? 'N/A',
                'location' => $inspection->equipment->location->location_code ?? 'N/A',
                'area' => $inspection->equipment->location->area->area_name ?? 'N/A',
                'company' => $inspection->equipment->location->area->company->company_name ?? 'N/A',
                'inspector' => $inspection->user->name ?? 'Unknown',
                'result' => $result,
                'total_items' => $totalItems,
                'ok_items' => $totalItems - $ngCount,
                'ng_items' => $ngCount,
                'has_ng_items' => $hasNgItems,
                'notes' => $inspection->notes ?? '',
                'approval_status' => $inspection->status, // Keep for reference but don't use in UI
                'approved_by' => $inspection->approvedBy->name ?? null,
                'approved_at' => $inspection->approved_at ? $inspection->approved_at->format('Y-m-d H:i:s') : null
            ];
        }

        return $inspectionsList;
    }

    /**
     * Get detailed inspection data with checksheet items for AII company
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAiiInspectionDetail(Request $request)
    {
        try {
            $inspectionId = $request->get('inspection_id');

            if (!$inspectionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inspection ID is required'
                ], 400);
            }

            // Get AII company ID (hardcoded for security) 
            $aiiCompany = Company::where(function ($query) {
                $query->where('company_name', '=', 'PT. AII')
                    ->orWhere('company_description', 'LIKE', '%PT. Aisin Indonesia');
            })
                ->where('is_active', true)
                ->first();

            if (!$aiiCompany) {
                return response()->json([
                    'success' => false,
                    'message' => 'AII company not found'
                ], 404);
            }

            // Get inspection with details, but only for AII company
            $inspection = Inspection::select([
                'id',
                'equipment_id',
                'inspection_date',
                'status',
                'notes',
                'user_id',
                'approved_by',
                'approved_at'
            ])->with([
                        'equipment:id,equipment_code,equipment_type_id,location_id',
                        'equipment.equipmentType:id,equipment_name,equipment_type',
                        'equipment.location:id,location_code,area_id',
                        'equipment.location.area:id,area_name,company_id',
                        'equipment.location.area.company:id,company_name',
                        'details:id,inspection_id,checksheet_id,status,picture',
                        'details.checksheetTemplate:id,item_name',
                        'user:id,name',
                        'approvedBy:id,name'
                    ])->whereHas('equipment.location.area', function ($q) use ($aiiCompany) {
                        $q->where('company_id', $aiiCompany->id);
                    })->where('id', $inspectionId)
                ->where('status', '!=', 'draft')
                ->first();

            if (!$inspection) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inspection not found or access denied'
                ], 404);
            }

            // Format inspection details
            $inspectionDetails = [];
            $ngCount = 0;
            $okCount = 0;

            foreach ($inspection->details as $index => $detail) {
                $status = $detail->status;
                if ($status === 'NG') {
                    $ngCount++;
                } else {
                    $okCount++;
                }

                $inspectionDetails[] = [
                    'no' => $index + 1,
                    'item_name' => $detail->checksheetTemplate->item_name ?? 'Unknown Item',
                    'status' => $status,
                    'picture' => $detail->picture ?? null,
                    'picture_url' => $detail->picture ? url('storage/' . $detail->picture) : null
                ];
            }

            $totalItems = count($inspectionDetails);
            $result = $ngCount > 0 ? 'NG' : 'OK';

            return response()->json([
                'success' => true,
                'data' => [
                    'inspection' => [
                        'id' => $inspection->id,
                        'date' => $inspection->inspection_date->format('Y-m-d'),
                        'date_formatted' => $inspection->inspection_date->format('d/m/Y'),
                        'equipment_code' => $inspection->equipment->equipment_code ?? 'N/A',
                        'equipment_name' => $inspection->equipment->equipmentType->equipment_name ?? 'N/A',
                        'equipment_type' => $inspection->equipment->equipmentType->equipment_type ?? 'N/A',
                        'location' => $inspection->equipment->location->location_code ?? 'N/A',
                        'area' => $inspection->equipment->location->area->area_name ?? 'N/A',
                        'company' => $inspection->equipment->location->area->company->company_name ?? 'N/A',
                        'inspector' => $inspection->user->name ?? 'Unknown',
                        'result' => $result,
                        'total_items' => $totalItems,
                        'ok_items' => $okCount,
                        'ng_items' => $ngCount,
                        'notes' => $inspection->notes ?? '',
                        'approval_status' => $inspection->status,
                        'approved_by' => $inspection->approvedBy->name ?? null,
                        'approved_at' => $inspection->approved_at ? $inspection->approved_at->format('Y-m-d H:i:s') : null
                    ],
                    'details' => $inspectionDetails
                ],
                'generated_at' => now()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching inspection detail',
                'error' => config('app.debug') ? $e->getMessage() : 'Something went wrong'
            ], 500);
        }
    }

    /**
     * Get monthly inspection statistics for bar chart visualization
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAiiMonthlyChartData(Request $request)
    {
        try {
            // Validasi parameter
            $year = $request->get('year', date('Y'));
            $equipmentTypeId = $request->get('equipment_type_id');

            // Validasi year
            if (!is_numeric($year) || $year < 2020 || $year > date('Y')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid year parameter'
                ], 400);
            }

            // Get AII company ID
            // Note: Based on user's data sample, the company name is "PT. AII"
            $aiiCompany = Company::where(function ($query) {
                $query->where('company_name', '=', 'PT. AII')
                    ->orWhere('company_description', 'LIKE', '%PT. Aisin Indonesia');
            })
                ->where('is_active', true)
                ->first();

            if (!$aiiCompany) {
                return response()->json([
                    'success' => false,
                    'message' => 'AII company not found'
                ], 404);
            }

            // Get monthly chart data
            $monthlyData = $this->getMonthlyChartData($aiiCompany->id, $year, $equipmentTypeId);

            return response()->json([
                'success' => true,
                'message' => 'Monthly chart data retrieved successfully',
                'data' => [
                    'year' => $year,
                    'equipment_type_id' => $equipmentTypeId,
                    'equipment_type_name' => $equipmentTypeId
                        ? (($type = EquipmentType::find($equipmentTypeId))
                            ? ($type->equipment_name ?? 'Unknown') . ' - ' . ($type->equipment_type ?? 'Unknown')
                            : 'Unknown')
                        : 'All Types',
                    'company' => [
                        'id' => $aiiCompany->id,
                        'name' => $aiiCompany->company_name
                    ],
                    'monthly_data' => $monthlyData,
                    'chart_config' => [
                        'type' => 'bar',
                        'categories' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        'series' => [
                            ['name' => 'OK', 'color' => '#5cb85c'],
                            ['name' => 'NG', 'color' => '#d9534f'],
                            ['name' => 'Not Inspected', 'color' => '#777777']
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve monthly chart data',
                'error' => config('app.debug') ? $e->getMessage() : 'Something went wrong'
            ], 500);
        }
    }

    /**
     * Get monthly chart data for AII company
     */
    private function getMonthlyChartData($companyId, $year, $equipmentTypeId = null)
    {
        $monthlyData = [];
        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec'
        ];

        // Get all equipment for AII company with optional equipment type filter
        $equipmentQuery = Equipment::whereHas('location.area', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        });

        if ($equipmentTypeId) {
            $equipmentQuery->where('equipment_type_id', $equipmentTypeId);
        }

        $equipmentQuery->where('is_active', true);
        $totalEquipments = $equipmentQuery->count();

        foreach ($months as $monthNum => $monthName) {
            $startOfMonth = Carbon::createFromDate($year, $monthNum, 1)->startOfMonth();
            $endOfMonth = Carbon::createFromDate($year, $monthNum, 1)->endOfMonth();

            // Get inspections for this month
            $inspectionsQuery = Inspection::whereBetween('inspection_date', [$startOfMonth, $endOfMonth])
                ->where('status', '!=', 'draft')
                ->whereHas('equipment.location.area', function ($q) use ($companyId) {
                    $q->where('company_id', $companyId);
                });

            if ($equipmentTypeId) {
                $inspectionsQuery->whereHas('equipment', function ($q) use ($equipmentTypeId) {
                    $q->where('equipment_type_id', $equipmentTypeId);
                });
            }

            $inspections = $inspectionsQuery->with(['details'])->get();

            // Count OK and NG inspections
            $okCount = 0;
            $ngCount = 0;

            foreach ($inspections as $inspection) {
                $hasNgItems = $inspection->details->where('status', 'NG')->count() > 0;

                if ($hasNgItems) {
                    $ngCount++;
                } else {
                    $okCount++;
                }
            }

            // Calculate not inspected count
            $inspectedEquipmentCount = $inspections->pluck('equipment_id')->unique()->count();
            $notInspectedCount = $totalEquipments - $inspectedEquipmentCount;

            $monthlyData[] = [
                'month' => $monthName,
                'month_number' => $monthNum,
                'ok_count' => $okCount,
                'ng_count' => $ngCount,
                'not_inspected_count' => $notInspectedCount,
                'total_equipments' => $totalEquipments,
                'total_inspections' => $okCount + $ngCount,
                'inspection_rate' => $totalEquipments > 0 ? round((($okCount + $ngCount) / $totalEquipments) * 100, 2) : 0
            ];
        }

        return $monthlyData;
    }
}