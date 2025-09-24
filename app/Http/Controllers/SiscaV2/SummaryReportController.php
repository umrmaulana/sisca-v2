<?php

namespace App\Http\Controllers\SiscaV2;

use Illuminate\Routing\Controller;
use App\Models\SiscaV2\Inspection;
use App\Models\SiscaV2\Equipment;
use App\Models\SiscaV2\Company;
use App\Models\SiscaV2\Area;
use App\Models\SiscaV2\EquipmentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class SummaryReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sisca-v2');

        // Only allow Supervisor, Management, Admin (exclude PIC)
        $this->middleware(function ($request, $next) {
            $user = Auth::guard('sisca-v2')->user();
            if (!in_array($user->role, ['Supervisor', 'Management', 'Admin'])) {
                return redirect()->route('sisca-v2.no-access');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = Auth::guard('sisca-v2')->user();
        $userRole = $user->role;

        // Filter defaults
        $selectedYear = $request->get('year', date('Y'));
        $selectedStatus = $request->get('status', 'all');
        $selectedCompanyId = null;
        $selectedAreaId = $request->get('area_id');
        $selectedEquipmentTypeId = $request->get('equipment_type_id');
        $searchEquipment = $request->get('search_equipment', '');

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

        // Get equipment types based on role (same logic as mapping-area)
        $equipmentTypes = collect();
        if (in_array($userRole, ['Admin', 'Management'])) {
            // Admin & Management can see all equipment types
            $equipmentTypes = EquipmentType::where('is_active', true)
                ->orderBy('equipment_name')
                ->get();
        } else {
            // PIC & Supervisor only see equipment types available in their company
            if ($user->company_id) {
                $equipmentTypes = EquipmentType::where('is_active', true)
                    ->whereHas('equipments', function ($q) use ($user) {
                        $q->whereHas('location.area', function ($loc) use ($user) {
                            $loc->where('company_id', $user->company_id);
                        });
                    })
                    ->orderBy('equipment_name')
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

        // Build inspections query for annual data
        $inspectionsQuery = Inspection::with([
            'user',
            'equipment.equipmentType',
            'equipment.location.area.company',
            'details.checksheetTemplate',
            'approvedBy'
        ]);

        // Annual date range filter
        $startOfYear = Carbon::createFromDate($selectedYear, 1, 1)->startOfYear();
        $endOfYear = Carbon::createFromDate($selectedYear, 12, 31)->endOfYear();
        $inspectionsQuery->whereBetween('inspection_date', [$startOfYear, $endOfYear]);

        // Company filter
        if ($selectedCompanyId) {
            $inspectionsQuery->whereHas('equipment.location.area', function ($q) use ($selectedCompanyId) {
                $q->where('company_id', $selectedCompanyId);
            });
        }

        // Area filter
        if ($selectedAreaId) {
            $inspectionsQuery->whereHas('equipment.location', function ($q) use ($selectedAreaId) {
                $q->where('area_id', $selectedAreaId);
            });
        }

        // Equipment type filter
        if ($selectedEquipmentTypeId) {
            $inspectionsQuery->whereHas('equipment', function ($q) use ($selectedEquipmentTypeId) {
                $q->where('equipment_type_id', $selectedEquipmentTypeId);
            });
        }

        // Equipment search filter
        if (!empty($searchEquipment)) {
            $inspectionsQuery->whereHas('equipment', function ($q) use ($searchEquipment) {
                $q->where('equipment_code', 'like', "%{$searchEquipment}%");
            });
        }

        // Status filter
        if ($selectedStatus !== 'all') {
            $inspectionsQuery->where('status', $selectedStatus);
        }

        // Only show inspections that are not draft
        $inspectionsQuery->where('status', '!=', 'draft');

        // Get all inspections without pagination for the details section
        $allInspections = $inspectionsQuery->orderBy('inspection_date', 'desc')->get();

        // Get paginated inspections for the main summary (if needed)
        $inspections = $inspectionsQuery->orderBy('inspection_date', 'desc')
            ->paginate(20);

        // Calculate annual summary statistics
        $totalInspections = $inspectionsQuery->count();
        $approvedInspections = $inspectionsQuery->where('status', 'approved')->count();
        $pendingInspections = $inspectionsQuery->where('status', 'pending')->count();
        $rejectedInspections = $inspectionsQuery->where('status', 'rejected')->count();

        // Get annual equipment summary data with pagination
        $equipmentSummaryData = $this->getAnnualEquipmentSummaryWithPagination($selectedCompanyId, $selectedAreaId, $selectedEquipmentTypeId, $selectedYear, $searchEquipment, $request);

        return view('sisca-v2.summary-report.index', compact(
            'inspections',
            'allInspections',
            'companies',
            'areas',
            'equipmentTypes',
            'selectedYear',
            'selectedStatus',
            'selectedCompanyId',
            'selectedAreaId',
            'selectedEquipmentTypeId',
            'user',
            'userRole',
            'totalInspections',
            'approvedInspections',
            'pendingInspections',
            'rejectedInspections',
            'equipmentSummaryData', // For paginated table
            'searchEquipment'
        ))->with('equipmentSummary', $equipmentSummaryData);
    }

    public function approve(Request $request, $id)
    {
        $user = Auth::guard('sisca-v2')->user();

        // Only Supervisor, Management, and Admin can approve
        if (!in_array($user->role, ['Supervisor', 'Management', 'Admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $inspection = Inspection::findOrFail($id);

        // Check if inspection can be approved
        if ($inspection->status !== 'pending') {
            return response()->json(['error' => 'Only pending inspections can be approved'], 400);
        }

        $inspection->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->get('approval_notes')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inspection approved successfully'
        ]);
    }

    public function reject(Request $request, $id)
    {
        $user = Auth::guard('sisca-v2')->user();

        // Only Supervisor, Management, and Admin can reject
        if (!in_array($user->role, ['Supervisor', 'Management', 'Admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $inspection = Inspection::findOrFail($id);

        // Check if inspection can be rejected
        if ($inspection->status !== 'pending') {
            return response()->json(['error' => 'Only pending inspections can be rejected'], 400);
        }

        $request->validate([
            'approval_notes' => 'required|string|max:1000'
        ]);

        $inspection->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->approval_notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inspection rejected successfully'
        ]);
    }

    public function bulkApprove(Request $request)
    {
        $user = Auth::guard('sisca-v2')->user();

        // Only Supervisor, Management, and Admin can approve
        if (!in_array($user->role, ['Supervisor', 'Management', 'Admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'inspection_ids' => 'required|array',
            'inspection_ids.*' => 'exists:tt_inspections,id',
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        $inspectionIds = $request->inspection_ids;
        $currentMonth = now()->format('Y-m');

        // Check if supervisor has already approved inspections this month
        $existingApprovals = Inspection::whereIn('id', $inspectionIds)
            ->where('approved_by', $user->id)
            ->whereRaw("DATE_FORMAT(approved_at, '%Y-%m') = ?", [$currentMonth])
            ->count();

        if ($existingApprovals > 0 && $user->role === 'Supervisor') {
            return response()->json([
                'error' => 'You have already approved inspections this month. Supervisors can only approve once per month.'
            ], 400);
        }

        // Get only pending inspections
        $inspections = Inspection::whereIn('id', $inspectionIds)
            ->where('status', 'pending')
            ->get();

        if ($inspections->isEmpty()) {
            return response()->json(['error' => 'No pending inspections found'], 400);
        }

        $approvedCount = 0;
        foreach ($inspections as $inspection) {
            $inspection->update([
                'status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'approval_notes' => $request->get('approval_notes')
            ]);
            $approvedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully approved {$approvedCount} inspection(s)"
        ]);
    }

    public function bulkReject(Request $request)
    {
        $user = Auth::guard('sisca-v2')->user();

        // Only Supervisor, Management, and Admin can reject
        if (!in_array($user->role, ['Supervisor', 'Management', 'Admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'inspection_ids' => 'required|array',
            'inspection_ids.*' => 'exists:tt_inspections,id',
            'approval_notes' => 'required|string|max:1000'
        ]);

        $inspectionIds = $request->inspection_ids;

        // Get only pending inspections
        $inspections = Inspection::whereIn('id', $inspectionIds)
            ->where('status', 'pending')
            ->get();

        if ($inspections->isEmpty()) {
            return response()->json(['error' => 'No pending inspections found'], 400);
        }

        $rejectedCount = 0;
        foreach ($inspections as $inspection) {
            $inspection->update([
                'status' => 'rejected',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'approval_notes' => $request->approval_notes
            ]);
            $rejectedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully rejected {$rejectedCount} inspection(s)"
        ]);
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

    public function exportPdf(Request $request)
    {
        $user = Auth::guard('sisca-v2')->user();
        $userRole = $user->role;

        // Get the same filters as index
        $selectedYear = $request->get('year', date('Y'));
        $selectedCompanyId = null;
        $selectedAreaId = $request->get('area_id');
        $selectedEquipmentTypeId = $request->get('equipment_type_id');
        $searchEquipment = $request->get('search_equipment', '');

        // Get company based on role
        if (in_array($userRole, ['Admin', 'Management'])) {
            $selectedCompanyId = $request->get('company_id');
        } else {
            $selectedCompanyId = $user->company_id;
        }

        // Get annual equipment summary data
        $equipmentSummary = $this->getAnnualEquipmentSummary($selectedCompanyId, $selectedAreaId, $selectedEquipmentTypeId, $selectedYear, $searchEquipment);

        // Get company and area names for PDF header
        $companyName = $selectedCompanyId ? Company::find($selectedCompanyId)->company_name : 'All Companies';
        $areaName = $selectedAreaId ? Area::find($selectedAreaId)->area_name : 'All Areas';
        $equipmentTypeName = $selectedEquipmentTypeId ? EquipmentType::find($selectedEquipmentTypeId)->equipment_name : 'All Equipment Types';

        $data = [
            'equipmentSummary' => $equipmentSummary,
            'companyName' => $companyName,
            'areaName' => $areaName,
            'equipmentTypeName' => $equipmentTypeName,
            'year' => $selectedYear,
            'generatedBy' => $user->name,
            'generatedAt' => now()->format('d/m/Y H:i:s')
        ];

        $pdf = Pdf::loadView('sisca-v2.summary-report.pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        $filename = "Annual_Summary_Report_{$selectedYear}_{$companyName}.pdf";

        return $pdf->download($filename);
    }

    private function getAnnualEquipmentSummary($companyId, $areaId, $equipmentTypeId, $year, $searchEquipment = '')
    {
        $startOfYear = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endOfYear = Carbon::createFromDate($year, 12, 31)->endOfYear();

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

        // Apply equipment search filter
        if (!empty($searchEquipment)) {
            $equipmentQuery->where(function ($q) use ($searchEquipment) {
                $q->where('equipment_code', 'like', "%{$searchEquipment}%");
            });
        }

        $equipmentQuery->where('is_active', true);

        $equipments = $equipmentQuery->get();

        $summary = [];

        foreach ($equipments as $equipment) {
            // Get monthly inspection data
            $monthlyData = [];
            $hasInspections = false;
            $hasNgItems = false;

            for ($month = 1; $month <= 12; $month++) {
                $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

                // Get the latest inspection for this month
                $inspection = $equipment->inspections()
                    ->whereBetween('inspection_date', [$startOfMonth, $endOfMonth])
                    ->where('status', '!=', 'draft')
                    ->orderBy('inspection_date', 'desc')
                    ->first();

                $status = 'not_inspected';
                $ngItems = [];
                $hasNgInMonth = false;

                if ($inspection) {
                    $hasInspections = true;
                    $status = $inspection->status;

                    // Check for NG items
                    $ngDetails = $inspection->details()
                        ->where('status', 'NG')
                        ->with('checksheetTemplate')
                        ->get();

                    if ($ngDetails->isNotEmpty()) {
                        $hasNgInMonth = true;
                        $hasNgItems = true;
                        $ngItems = $ngDetails->pluck('checksheetTemplate.item_name')->filter()->toArray();
                    }
                }

                $monthlyData[] = [
                    'month' => $month,
                    'status' => $status,
                    'has_ng_items' => $hasNgInMonth,
                    'ng_items' => $ngItems
                ];
            }

            $summary[] = [
                'equipment_code' => $equipment->equipment_code,
                'equipment_name' => $equipment->desc ?? '',
                'equipment_type' => $equipment->equipmentType->equipment_name ?? 'Unknown',
                'location' => $equipment->location->location_code ?? 'Unknown',
                'area' => $equipment->location->area->area_name ?? 'Unknown',
                'company' => $equipment->location->area->company->company_name ?? 'Unknown',
                'expired_date' => $equipment->expired_date ? Carbon::parse($equipment->expired_date)->format('Y-m-d') : null,
                'monthly_data' => $monthlyData,
                'has_inspections' => $hasInspections,
                'has_ng_items' => $hasNgItems
            ];
        }

        return collect($summary);
    }

    private function getAnnualEquipmentSummaryWithPagination($companyId, $areaId, $equipmentTypeId, $year, $searchEquipment = '', $request = null)
    {
        $startOfYear = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endOfYear = Carbon::createFromDate($year, 12, 31)->endOfYear();

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

        // Apply equipment search filter
        if (!empty($searchEquipment)) {
            $equipmentQuery->where(function ($q) use ($searchEquipment) {
                $q->where('equipment_code', 'like', "%{$searchEquipment}%");
            });
        }

        $equipmentQuery->where('is_active', true);

        // Apply pagination
        $equipments = $equipmentQuery->paginate(15); // 15 items per page
        if ($request) {
            $equipments->appends($request->query());
        }

        $paginatedSummary = [];

        foreach ($equipments as $equipment) {
            // Get monthly inspection data
            $monthlyData = [];

            for ($month = 1; $month <= 12; $month++) {
                $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth();

                // Get the latest approved inspection for this month
                $inspection = Inspection::where('equipment_id', $equipment->id)
                    ->whereBetween('inspection_date', [$startOfMonth, $endOfMonth])
                    ->where('status', 'approved')
                    ->latest('inspection_date')
                    ->first();

                // If no approved inspection, check for pending or rejected
                if (!$inspection) {
                    $inspection = Inspection::where('equipment_id', $equipment->id)
                        ->whereBetween('inspection_date', [$startOfMonth, $endOfMonth])
                        ->whereIn('status', ['pending', 'rejected'])
                        ->latest('inspection_date')
                        ->first();
                }

                $monthlyData[$month] = [
                    'status' => $inspection ? $inspection->status : null,
                    'ng_items' => []
                ];

                // Get NG items if inspection exists and is approved
                if ($inspection && $inspection->status === 'approved') {
                    $ngItems = $inspection->details()
                        ->where('status', 'NG')
                        ->with('checksheetTemplate')
                        ->get()
                        ->pluck('checksheetTemplate.item_name')
                        ->filter()
                        ->toArray();

                    $monthlyData[$month]['ng_items'] = $ngItems;
                }
            }

            $paginatedSummary[] = [
                'equipment_code' => $equipment->equipment_code,
                'equipment_name' => $equipment->equipmentType->equipment_type ?? '-',
                'equipment_type' => $equipment->equipmentType->equipment_name ?? '-',
                'location' => $equipment->location->location_name ?? '-',
                'area' => $equipment->location->area->area_name ?? '-',
                'pos' => $equipment->location->pos ?? '-',
                'company' => $equipment->location->area->company->company_name ?? '-',
                'expired_date' => $equipment->expired_date ? Carbon::parse($equipment->expired_date)->format('Y-m-d') : null,
                'monthly_data' => $monthlyData
            ];
        }

        // Create a paginated collection-like object
        $paginatedCollection = new \Illuminate\Pagination\LengthAwarePaginator(
            collect($paginatedSummary),
            $equipments->total(),
            $equipments->perPage(),
            $equipments->currentPage(),
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        if ($request) {
            $paginatedCollection->appends($request->query());
        }

        return $paginatedCollection;
    }
}
