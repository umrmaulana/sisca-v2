<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\Equipment;
use App\Models\Company;
use App\Models\Area;
use App\Models\EquipmentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SummaryReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Only allow Supervisor, Management, Admin (exclude PIC)
        $this->middleware(function ($request, $next) {
            $user = Auth::guard()->user();
            if (!in_array($user->role, ['Supervisor', 'Management', 'Admin'])) {
                return redirect()->route('no-access');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = Auth::guard()->user();
        $userRole = $user->role;

        // Filter defaults
        $selectedYear = $request->get('year', date('Y'));
        $selectedStatus = $request->get('status', 'all');
        $selectedInspectionResult = $request->get('inspection_result', 'all');
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

        // Get equipment types based on selected company
        $equipmentTypes = collect();
        $companyIdForEquipmentTypes = null;

        if (in_array($userRole, ['Admin', 'Management'])) {
            // Admin & Management use selected company or show all if no company selected
            $companyIdForEquipmentTypes = $selectedCompanyId;
        } else {
            // PIC & Supervisor use their company
            $companyIdForEquipmentTypes = $user->company_id;
        }

        if ($companyIdForEquipmentTypes) {
            // Get equipment types that are active in the selected/assigned company
            $equipmentTypes = EquipmentType::where('is_active', true)
                ->whereHas('equipments', function ($q) use ($companyIdForEquipmentTypes) {
                    $q->where('is_active', true)
                        ->whereHas('location.area', function ($loc) use ($companyIdForEquipmentTypes) {
                            $loc->where('company_id', $companyIdForEquipmentTypes);
                        });
                })
                ->orderBy('equipment_name')
                ->get();
        } else if (in_array($userRole, ['Admin', 'Management'])) {
            // Admin/Management with no company selected - show all equipment types
            $equipmentTypes = EquipmentType::where('is_active', true)
                ->orderBy('equipment_name')
                ->get();
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

        // Calculate annual summary statistics before pagination
        $totalInspections = $inspectionsQuery->count();
        $approvedInspections = (clone $inspectionsQuery)->where('status', 'approved')->count();
        $pendingInspections = (clone $inspectionsQuery)->where('status', 'pending')->count();
        $rejectedInspections = (clone $inspectionsQuery)->where('status', 'rejected')->count();

        // Get equipment summary data with inspection result filter applied
        $equipmentSummary = $this->getAnnualEquipmentSummary(
            $selectedCompanyId,
            $selectedAreaId,
            $selectedEquipmentTypeId,
            $selectedYear,
            $searchEquipment,
            $selectedInspectionResult
        );

        // Get paginated inspections data for display (limit for better performance)
        $inspections = $inspectionsQuery->orderBy('inspection_date', 'desc')
            ->limit(1000)
            ->get();

        return view('summary-report.index', compact(
            'companies',
            'areas',
            'equipmentTypes',
            'selectedYear',
            'selectedStatus',
            'selectedCompanyId',
            'selectedAreaId',
            'selectedEquipmentTypeId',
            'selectedInspectionResult',
            'user',
            'userRole',
            'totalInspections',
            'approvedInspections',
            'pendingInspections',
            'rejectedInspections',
            'searchEquipment',
            'equipmentSummary',
            'inspections'
        ));
    }

    // Helper method to build inspections query
    private function buildInspectionsQuery($selectedYear, $selectedCompanyId, $selectedAreaId, $selectedEquipmentTypeId, $searchEquipment, $selectedStatus = 'all')
    {
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

        return $inspectionsQuery;
    }

    // DataTables endpoint for equipment summary
    public function getEquipmentSummaryData(Request $request)
    {
        $user = Auth::guard()->user();
        $userRole = $user->role;

        // Get filters from request
        $selectedYear = $request->get('year', date('Y'));
        $selectedInspectionResult = $request->get('inspection_result', 'all');
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

        // Get all equipment summary data with inspection result filter applied
        $equipmentSummary = $this->getAnnualEquipmentSummary($selectedCompanyId, $selectedAreaId, $selectedEquipmentTypeId, $selectedYear, $searchEquipment, $selectedInspectionResult);

        // Format data for DataTables
        $data = [];
        foreach ($equipmentSummary as $equipment) {
            $data[] = [
                'equipment_code' => $equipment['equipment_code'],
                'equipment_name' => $equipment['equipment_name'],
                'equipment_type' => $equipment['equipment_type'],
                'area' => $equipment['area'],
                'company' => $equipment['company'],
                'jan_status' => $equipment['jan_status'],
                'feb_status' => $equipment['feb_status'],
                'mar_status' => $equipment['mar_status'],
                'apr_status' => $equipment['apr_status'],
                'may_status' => $equipment['may_status'],
                'jun_status' => $equipment['jun_status'],
                'jul_status' => $equipment['jul_status'],
                'aug_status' => $equipment['aug_status'],
                'sep_status' => $equipment['sep_status'],
                'oct_status' => $equipment['oct_status'],
                'nov_status' => $equipment['nov_status'],
                'dec_status' => $equipment['dec_status'],
                'total_inspections' => $equipment['total_inspections'],
                'completion_rate' => $equipment['completion_rate']
            ];
        }

        return response()->json([
            'data' => $data,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data)
        ]);
    }

    // DataTables endpoint for inspections
    public function getInspectionsData(Request $request)
    {
        $user = Auth::guard()->user();
        $userRole = $user->role;

        // Get filters from request
        $selectedYear = $request->get('year', date('Y'));
        $selectedStatus = $request->get('status', 'all');
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

        // Build query
        $inspectionsQuery = $this->buildInspectionsQuery($selectedYear, $selectedCompanyId, $selectedAreaId, $selectedEquipmentTypeId, $searchEquipment, $selectedStatus);

        // Get all data for DataTables processing
        $inspections = $inspectionsQuery->orderBy('inspection_date', 'desc')->get();

        // Format data for DataTables
        $data = [];
        foreach ($inspections as $inspection) {
            $statusBadge = '';
            switch ($inspection->status) {
                case 'approved':
                    $statusBadge = '<span class="badge bg-success">Approved</span>';
                    break;
                case 'pending':
                    $statusBadge = '<span class="badge bg-warning">Pending</span>';
                    break;
                case 'rejected':
                    $statusBadge = '<span class="badge bg-danger">Rejected</span>';
                    break;
                default:
                    $statusBadge = '<span class="badge bg-secondary">' . ucfirst($inspection->status) . '</span>';
            }

            $actions = '';
            if (in_array($userRole, ['Supervisor', 'Management', 'Admin'])) {
                if ($inspection->status === 'pending') {
                    $actions .= '<div class="btn-group" role="group">';
                    $actions .= '<input type="checkbox" class="form-check-input inspection-checkbox me-2" value="' . $inspection->id . '" onchange="toggleBulkActions()">';
                    $actions .= '<button class="btn btn-success btn-sm me-1" onclick="approveInspection(' . $inspection->id . ')" title="Approve">';
                    $actions .= '<i class="fas fa-check"></i></button>';
                    $actions .= '<button class="btn btn-danger btn-sm" onclick="rejectInspection(' . $inspection->id . ')" title="Reject">';
                    $actions .= '<i class="fas fa-times"></i></button>';
                    $actions .= '</div>';
                } elseif (!empty($inspection->approval_notes)) {
                    $actions .= '<button class="btn btn-info btn-sm" onclick="showApprovalNotes(\'' . addslashes($inspection->approval_notes) . '\')" title="View Notes">';
                    $actions .= '<i class="fas fa-eye"></i></button>';
                }
            }

            $data[] = [
                'inspection_date' => $inspection->inspection_date->format('d/m/Y'),
                'equipment_code' => $inspection->equipment->equipment_code ?? '-',
                'equipment_name' => $inspection->equipment->equipmentType->equipment_name ?? '-',
                'equipment_type' => $inspection->equipment->equipmentType->equipment_type ?? '-',
                'area' => $inspection->equipment->location->area->area_name ?? '-',
                'inspector' => $inspection->user->name ?? '-',
                'status' => $statusBadge,
                'approved_by' => $inspection->approvedBy->name ?? '-',
                'approved_at' => $inspection->approved_at ? $inspection->approved_at->format('d/m/Y H:i') : '-',
                'actions' => $actions
            ];
        }

        return response()->json([
            'data' => $data,
            'recordsTotal' => count($data),
            'recordsFiltered' => count($data)
        ]);
    }

    public function approve(Request $request, $id)
    {
        $user = Auth::guard()->user();

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
        $user = Auth::guard()->user();

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
        $user = Auth::guard()->user();

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
        $user = Auth::guard()->user();

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

    public function getEquipmentTypesByCompany(Request $request)
    {
        $user = Auth::guard()->user();
        $userRole = $user->role;
        $companyId = $request->get('company_id');

        // Determine which company to use for equipment types
        $companyIdForEquipmentTypes = null;
        if (in_array($userRole, ['Admin', 'Management'])) {
            $companyIdForEquipmentTypes = $companyId;
        } else {
            $companyIdForEquipmentTypes = $user->company_id;
        }

        $equipmentTypes = collect();

        if ($companyIdForEquipmentTypes) {
            // Get equipment types that are active in the selected/assigned company
            $equipmentTypes = EquipmentType::where('is_active', true)
                ->whereHas('equipments', function ($q) use ($companyIdForEquipmentTypes) {
                    $q->where('is_active', true)
                        ->whereHas('location.area', function ($loc) use ($companyIdForEquipmentTypes) {
                            $loc->where('company_id', $companyIdForEquipmentTypes);
                        });
                })
                ->orderBy('equipment_name')
                ->get();
        } else if (in_array($userRole, ['Admin', 'Management'])) {
            // Admin/Management with no company selected - show all equipment types
            $equipmentTypes = EquipmentType::where('is_active', true)
                ->orderBy('equipment_name')
                ->get();
        }

        // Format response
        $formattedEquipmentTypes = $equipmentTypes->map(function ($equipmentType) {
            return [
                'id' => $equipmentType->id,
                'name' => $equipmentType->equipment_name . ' - ' . $equipmentType->equipment_type
            ];
        });

        return response()->json($formattedEquipmentTypes);
    }

    public function exportPdf(Request $request)
    {
        // Increase execution time for large datasets
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $user = Auth::guard()->user();
        $userRole = $user->role;

        // Get filters
        $selectedYear = $request->get('year', date('Y'));
        $selectedStatus = $request->get('status', 'all');
        $selectedCompanyId = in_array($userRole, ['Admin', 'Management']) ?
            $request->get('company_id') : $user->company_id;
        $selectedAreaId = $request->get('area_id');
        $selectedEquipmentTypeId = $request->get('equipment_type_id');
        $searchEquipment = $request->get('search_equipment', '');

        // Get optimized equipment summary (limit to 500 records for PDF)
        $equipmentSummary = $this->getOptimizedEquipmentSummary(
            $selectedCompanyId,
            $selectedAreaId,
            $selectedEquipmentTypeId,
            $selectedYear,
            $searchEquipment,
            500
        );

        // Get company name for filename
        $companyName = $selectedCompanyId ?
            Company::find($selectedCompanyId)?->company_name ?? 'Company' : 'All_Companies';

        // Get area name for header
        $areaName = $selectedAreaId ?
            Area::find($selectedAreaId)?->area_name ?? 'All Areas' : 'All Areas';

        // Get equipment type name for header
        $equipmentTypeName = $selectedEquipmentTypeId ?
            EquipmentType::find($selectedEquipmentTypeId)?->equipment_type ?? 'All Types' : 'All Types';

        $data = [
            'equipmentSummary' => $equipmentSummary,
            'companyName' => $companyName,
            'areaName' => $areaName,
            'equipmentTypeName' => $equipmentTypeName,
            'year' => $selectedYear,
            'generatedBy' => $user->name,
            'generatedAt' => now()->format('d/m/Y H:i:s'),
            'recordCount' => $equipmentSummary->count()
        ];

        $pdf = Pdf::loadView('summary-report.pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false]);

        $filename = "Annual_Summary_Report_{$selectedYear}_{$companyName}.pdf";
        return $pdf->download($filename);
    }

    public function exportExcel(Request $request)
    {
        try {
            // Clean any output buffer to prevent corruption
            if (ob_get_length()) {
                ob_end_clean();
            }

            // Increase execution time and memory for Excel export
            set_time_limit(300);
            ini_set('memory_limit', '512M');

            // Check authentication
            $user = Auth::guard()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userRole = $user->role;

            // Get filters including inspection result
            $selectedYear = $request->get('year', date('Y'));
            $selectedStatus = $request->get('status', 'all');
            $selectedInspectionResult = $request->get('inspection_result', 'all');
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

            // Get first company if none selected
            if (!$selectedCompanyId) {
                $company = Company::first();
                $selectedCompanyId = $company ? $company->id : null;
            }

            if (!$selectedCompanyId) {
                return response()->json(['error' => 'No company found'], 400);
            }

            // Get equipment summary data with inspection result filter applied
            $equipmentSummary = $this->getAnnualEquipmentSummary($selectedCompanyId, $selectedAreaId, $selectedEquipmentTypeId, $selectedYear, $searchEquipment, $selectedInspectionResult);

            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Equipment Status');

            // Add header - Use text for stability (images can cause Excel corruption)
            // $sheet->setCellValue('A1', 'PT. AISIN INDONESIA');
            // $sheet->getStyle('A1')->getFont()->setSize(18)->setBold(true)->getColor()->setRGB('FF0000');
            // $sheet->mergeCells('A1:Q1'); // Merge across all columns
            // $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $drawing = new Drawing();
            $drawing->setName('Logo Satu Aisin');
            $drawing->setDescription('PT. Aisin');
            $drawing->setPath(public_path('foto/satu-aisin-final.png'));
            $drawing->setHeight(80);
            $drawing->setCoordinates('M1');
            $drawing->setWorksheet($sheet);

            // Set row height for header
            $sheet->getRowDimension('1')->setRowHeight(30);
            $sheet->getRowDimension('2')->setRowHeight(15);

            $sheet->setCellValue('A1', 'EQUIPMENT ANNUAL INSPECTION STATUS');
            $sheet->mergeCells('A1:E1');
            $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true);

            $sheet->setCellValue('A2', 'Year: ' . $selectedYear);
            $sheet->mergeCells('A1:E1');
            $company = Company::find($selectedCompanyId);
            $sheet->setCellValue('A3', 'Company: ' . ($company ? $company->company_name : 'Unknown'));
            $sheet->mergeCells('A1:E1');

            // Set column headers (row 5)
            $headerRow = 5;
            $headers = [
                'CODE',
                'NAME',
                'TYPE',
                'LOCATION',
                'AREA',
                'JAN',
                'FEB',
                'MAR',
                'APR',
                'MAY',
                'JUN',
                'JUL',
                'AUG',
                'SEP',
                'OCT',
                'NOV',
                'DEC'
            ];

            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . $headerRow, $header);
                $sheet->getStyle($col . $headerRow)->getFont()->setBold(true);
                $col++;
            }

            // Add data rows
            $dataStartRow = $headerRow + 1;
            $row = $dataStartRow;

            foreach ($equipmentSummary as $equipment) {
                // Add data values directly without sanitization
                $sheet->setCellValue('A' . $row, $equipment['equipment_code'] ?? '');
                $sheet->setCellValue('B' . $row, $equipment['equipment_name'] ?? '');
                $sheet->setCellValue('C' . $row, $equipment['equipment_type'] ?? '');
                $sheet->setCellValue('D' . $row, $equipment['location'] ?? '');
                $sheet->setCellValue('E' . $row, $equipment['area'] ?? '');

                // Monthly data (columns F-Q) - Only show OK and NG
                $monthColumns = ['F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q'];
                for ($month = 1; $month <= 12; $month++) {
                    $monthCol = $monthColumns[$month - 1];
                    $monthData = $equipment['monthly_data'][$month] ?? null;

                    try {
                        if ($monthData && isset($monthData['status']) && $monthData['status'] === 'approved') {
                            if (isset($monthData['has_ng_items']) && $monthData['has_ng_items']) {
                                // Has issues - show NG
                                $sheet->setCellValue($monthCol . $row, 'NG');
                                $sheet->getStyle($monthCol . $row)->getFill()
                                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFB6C1');
                            } else {
                                // No issues - show OK
                                $sheet->setCellValue($monthCol . $row, 'OK');
                                $sheet->getStyle($monthCol . $row)->getFill()
                                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('90EE90');
                            }
                        } else {
                            // Not inspected or not approved - show dash
                            $sheet->setCellValue($monthCol . $row, '-');
                        }
                    } catch (\Exception $e) {
                        // If there's any error with a cell, just put dash
                        $sheet->setCellValue($monthCol . $row, '-');
                    }
                }
                $row++;
            }

            // Add Legend
            $legendStartRow = $row + 2;
            $sheet->setCellValue('A' . $legendStartRow, 'LEGEND:');
            $sheet->getStyle('A' . $legendStartRow)->getFont()->setBold(true)->setSize(12);

            $legendRow = $legendStartRow + 1;

            // OK Legend
            $sheet->setCellValue('A' . $legendRow, 'OK');
            $sheet->getStyle('A' . $legendRow)->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('90EE90');
            $sheet->setCellValue('B' . $legendRow, 'Inspected No Issues Found');

            // NG Legend
            $legendRow++;
            $sheet->setCellValue('A' . $legendRow, 'NG');
            $sheet->getStyle('A' . $legendRow)->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFB6C1');
            $sheet->setCellValue('B' . $legendRow, 'Inspected Issues Found (Not Good)');

            // Not Inspected Legend
            $legendRow++;
            $sheet->setCellValue('A' . $legendRow, '-');
            $sheet->setCellValue('B' . $legendRow, 'Not Inspected or Not Approved');

            // Auto-size columns
            foreach (range('A', 'Q') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Create filename with timestamp
            $company = Company::find($selectedCompanyId);
            $companyName = $company ? preg_replace('/[^A-Za-z0-9_\-]/', '_', $company->company_name) : 'Company';
            $timestamp = date('Y-m-d_H-i-s');
            $filename = "Equipment_Status_{$selectedYear}_{$companyName}_{$timestamp}.xlsx";

            // Create writer
            $writer = new Xlsx($spreadsheet);

            // Use temporary file approach for better stability
            $tempPath = storage_path('app/temp/');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            $tempFile = $tempPath . $filename;
            $writer->save($tempFile);

            // Verify file was created successfully
            if (!file_exists($tempFile)) {
                throw new \Exception('Failed to create Excel file');
            }

            // Return file download response
            return response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error('Excel Export Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to generate Excel file: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getAnnualEquipmentSummary($companyId, $areaId, $equipmentTypeId, $year, $searchEquipment = '', $inspectionResult = 'all')
    {
        // Use optimized version and apply inspection result filter
        $summary = $this->getOptimizedEquipmentSummary($companyId, $areaId, $equipmentTypeId, $year, $searchEquipment);

        // Apply inspection result filter
        if ($inspectionResult !== 'all') {
            $summary = $summary->filter(function ($equipment) use ($inspectionResult) {
                foreach ($equipment['monthly_data'] as $monthData) {
                    switch ($inspectionResult) {
                        case 'ok':
                            if ($monthData['status'] === 'approved' && !$monthData['has_ng_items']) {
                                return true;
                            }
                            break;
                        case 'ng':
                            if ($monthData['has_ng_items']) {
                                return true;
                            }
                            break;
                        case 'not_inspected':
                            if ($monthData['status'] === 'not_inspected') {
                                return true;
                            }
                            break;
                    }
                }
                return false;
            });
        }

        return $summary;

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
                        ->with('checksheetTemplate.equipmentType')
                        ->get();

                    if ($ngDetails->isNotEmpty()) {
                        $hasNgInMonth = true;
                        $hasNgItems = true;
                        $ngItems = $ngDetails->map(function ($detail) use ($inspection) {
                            $template = $detail->checksheetTemplate;

                            // Debug log
                            \Log::info('NG Detail Debug', [
                                'detail_id' => $detail->id,
                                'checksheet_id' => $detail->checksheet_id,
                                'template_exists' => $template ? 'yes' : 'no',
                                'template_item_name' => $template ? $template->item_name : 'null',
                                'template_id' => $template ? $template->id : 'null'
                            ]);

                            return [
                                'item_name' => $template ? $template->item_name : 'No Template Found',
                                'item_description' => $template ? ($template->standar_condition ?? '') : '',
                                'category' => ($template && $template->equipmentType) ? $template->equipmentType->equipment_type : 'Equipment Check',
                                'notes' => $inspection->notes ?? ''
                            ];
                        })->values()->toArray(); // Use values() to ensure numeric array
                    }
                }

                $isOk = ($status === 'approved' && !$hasNgInMonth);

                $monthlyData[$month] = [
                    'status' => $status,
                    'has_ng_items' => $hasNgInMonth,
                    'ng_items' => $ngItems,
                    'is_ok' => $isOk
                ];
            }

            $summary[] = [
                'equipment_code' => $equipment->equipment_code,
                'equipment_name' => $equipment->equipmentType->equipment_name ?? 'Unknown',
                'equipment_type' => $equipment->equipmentType->equipment_type ?? 'Unknown',
                'location' => $equipment->location->location_name ?? 'Unknown',
                'area' => $equipment->location->area->area_name ?? 'Unknown',
                'company' => $equipment->location->area->company->company_name ?? 'Unknown',
                'expired_date' => $equipment->expired_date ? Carbon::parse($equipment->expired_date)->format('Y-m-d') : null,
                'monthly_data' => $monthlyData,
                'has_inspections' => $hasInspections,
                'has_ng_items' => $hasNgItems
            ];
        }

        // Apply inspection result filter
        if ($inspectionResult !== 'all') {
            $summary = array_filter($summary, function ($equipment) use ($inspectionResult) {
                foreach ($equipment['monthly_data'] as $monthData) {
                    switch ($inspectionResult) {
                        case 'ok':
                            if ($monthData['status'] === 'approved' && !$monthData['has_ng_items']) {
                                return true;
                            }
                            break;
                        case 'ng':
                            if ($monthData['has_ng_items']) {
                                return true;
                            }
                            break;
                        case 'not_inspected':
                            if ($monthData['status'] === 'not_inspected') {
                                return true;
                            }
                            break;
                    }
                }
                return false;
            });
        }

        return collect($summary);
    }

    private function getOptimizedEquipmentSummary($companyId, $areaId, $equipmentTypeId, $year, $searchEquipment = '', $limit = null)
    {
        $startOfYear = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endOfYear = Carbon::createFromDate($year, 12, 31)->endOfYear();

        // Build optimized equipment query
        $equipmentQuery = Equipment::select([
            'id',
            'equipment_code',
            'equipment_type_id',
            'location_id'
        ])->with([
                    'equipmentType:id,equipment_name,equipment_type',
                    'location:id,area_id,location_code',
                    'location.area:id,area_name,company_id',
                    'location.area.company:id,company_name'
                ]);

        // Apply filters
        if ($companyId) {
            $equipmentQuery->whereHas('location.area.company', fn($q) => $q->where('id', $companyId));
        }
        if ($areaId) {
            $equipmentQuery->whereHas('location', fn($q) => $q->where('area_id', $areaId));
        }
        if ($equipmentTypeId) {
            $equipmentQuery->where('equipment_type_id', $equipmentTypeId);
        }
        if (!empty($searchEquipment)) {
            $equipmentQuery->where('equipment_code', 'like', "%{$searchEquipment}%");
        }

        $equipmentQuery->where('is_active', true);

        if ($limit) {
            $equipmentQuery->limit($limit);
        }

        $equipments = $equipmentQuery->get();

        // Get all inspections for these equipments in one query
        $equipmentIds = $equipments->pluck('id');
        $inspections = Inspection::select([
            'id',
            'equipment_id',
            'inspection_date',
            'status',
            'notes'
        ])->with([
                    'details:id,inspection_id,checksheet_id,status',
                    'details.checksheetTemplate:id,equipment_type_id,item_name,standar_condition',
                    'details.checksheetTemplate.equipmentType:id,equipment_type'
                ])
            ->whereIn('equipment_id', $equipmentIds)
            ->whereBetween('inspection_date', [$startOfYear, $endOfYear])
            ->where('status', '!=', 'draft')
            ->get()
            ->groupBy('equipment_id');

        $summary = [];
        foreach ($equipments as $equipment) {
            $equipmentInspections = $inspections->get($equipment->id, collect());
            $monthlyData = [];

            for ($month = 1; $month <= 12; $month++) {
                $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
                $monthEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth();

                $monthInspection = $equipmentInspections
                    ->whereBetween('inspection_date', [$monthStart, $monthEnd])
                    ->sortByDesc('inspection_date')
                    ->first();

                $status = 'not_inspected';
                $hasNgItems = false;
                $ngItems = [];

                if ($monthInspection) {
                    $status = $monthInspection->status;
                    $ngDetails = $monthInspection->details->where('status', 'NG');
                    if ($ngDetails->isNotEmpty()) {
                        $hasNgItems = true;
                        $ngItems = $ngDetails->map(function ($detail) use ($monthInspection) {
                            $template = $detail->checksheetTemplate;

                            // Debug log
                            \Log::info('NG Detail Debug Optimized', [
                                'detail_id' => $detail->id,
                                'checksheet_id' => $detail->checksheet_id,
                                'template_exists' => $template ? 'yes' : 'no',
                                'template_item_name' => $template ? $template->item_name : 'null',
                                'template_id' => $template ? $template->id : 'null'
                            ]);

                            return [
                                'item_name' => $template ? $template->item_name : 'No Template Found',
                                'item_description' => $template ? ($template->standar_condition ?? '') : '',
                                'category' => ($template && $template->equipmentType) ? $template->equipmentType->equipment_type : 'Equipment Check',
                                'notes' => $monthInspection->notes ?? ''
                            ];
                        })->values()->toArray(); // Use values() to ensure numeric array
                    }
                }

                $monthlyData[$month] = [
                    'status' => $status,
                    'has_ng_items' => $hasNgItems,
                    'ng_items' => $ngItems,
                    'is_ok' => ($status === 'approved' && !$hasNgItems)
                ];
            }

            $summary[] = [
                'equipment_code' => $equipment->equipment_code,
                'equipment_name' => $equipment->equipmentType->equipment_name ?? 'Unknown',
                'equipment_type' => $equipment->equipmentType->equipment_type ?? 'Unknown',
                'location' => $equipment->location->location_name ?? 'Unknown',
                'area' => $equipment->location->area->area_name ?? 'Unknown',
                'company' => $equipment->location->area->company->company_name ?? 'Unknown',
                'monthly_data' => $monthlyData
            ];
        }

        return collect($summary);
    }

    public function getInspection($id)
    {
        $user = Auth::guard()->user();

        // Only Management and Admin can access this
        if (!in_array($user->role, ['Management', 'Admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $inspection = Inspection::findOrFail($id);

        return response()->json([
            'success' => true,
            'inspection' => [
                'id' => $inspection->id,
                'inspection_date' => Carbon::parse($inspection->inspection_date)->format('Y-m-d'),
                'status' => $inspection->status,
                'approval_notes' => $inspection->approval_notes,
                'equipment_code' => $inspection->equipment->equipment_code ?? 'Unknown'
            ]
        ]);
    }

    public function editApproved(Request $request, $id)
    {
        $user = Auth::guard()->user();

        // Only Management and Admin can edit approved inspections
        if (!in_array($user->role, ['Management', 'Admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'inspection_date' => 'required|date',
            'status' => 'required|in:approved,pending,rejected',
            'edit_notes' => 'required|string|max:1000',
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        $inspection = Inspection::findOrFail($id);

        // Create audit log for the edit
        $originalData = [
            'inspection_date' => Carbon::parse($inspection->inspection_date)->format('Y-m-d'),
            'status' => $inspection->status,
            'approval_notes' => $inspection->approval_notes
        ];

        // Update the inspection
        $inspection->update([
            'inspection_date' => $request->inspection_date,
            'status' => $request->status,
            'approval_notes' => $request->approval_notes,
            'edited_by' => $user->id,
            'edited_at' => now(),
            'edit_notes' => $request->edit_notes,
            'original_data' => json_encode($originalData) // Store original data for audit
        ]);

        // Log the activity (you can implement logging to a separate audit table if needed)
        \Log::info('Approved inspection edited', [
            'inspection_id' => $inspection->id,
            'edited_by' => $user->id,
            'edited_by_name' => $user->name,
            'edit_notes' => $request->edit_notes,
            'original_data' => $originalData,
            'new_data' => [
                'inspection_date' => $request->inspection_date,
                'status' => $request->status,
                'approval_notes' => $request->approval_notes
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inspection updated successfully'
        ]);
    }

    /**
     * Get inspection history for back date modal
     */
    public function getInspectionHistory($id)
    {
        try {
            $inspection = Inspection::findOrFail($id);

            // Get date change history from stored original_data
            $dateHistory = [];

            if ($inspection->original_data) {
                $originalData = json_decode($inspection->original_data, true);

                // Check if there's date history array
                if (isset($originalData['date_history']) && is_array($originalData['date_history'])) {
                    $dateHistory = $originalData['date_history'];
                }
                // Legacy: check for single date change
                elseif (isset($originalData['inspection_date']) && $originalData['inspection_date'] !== $inspection->inspection_date) {
                    $dateHistory[] = [
                        'old_date' => $originalData['inspection_date'],
                        'new_date' => $inspection->inspection_date
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'inspection' => [
                    'id' => $inspection->id,
                    'inspection_date' => $inspection->inspection_date,
                    'status' => $inspection->status,
                    'equipment_code' => $inspection->equipment->equipment_code ?? 'N/A'
                ],
                'dateHistory' => array_reverse($dateHistory) // Show most recent changes first
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load inspection history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update inspection date (back date functionality)
     */
    public function backDateInspection(Request $request, $id)
    {
        try {
            $user = auth()->user();

            $request->validate([
                'new_inspection_date' => 'required|date'
            ]);

            $inspection = Inspection::findOrFail($id);

            // Store original date for history
            $originalDate = $inspection->inspection_date;
            $newDate = $request->new_inspection_date;

            // Don't update if date is the same
            if ($originalDate === $newDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'New date is the same as current date'
                ]);
            }

            // Prepare history data for audit trail
            $historyData = [
                'old_date' => $originalDate,
                'new_date' => $newDate
            ];

            // Get existing history and add new entry
            $existingHistory = [];
            if ($inspection->original_data) {
                $originalData = json_decode($inspection->original_data, true);
                if (isset($originalData['date_history'])) {
                    $existingHistory = $originalData['date_history'];
                }
            }

            // Add new history entry
            $existingHistory[] = $historyData;

            // Update only the inspection date
            $inspection->update([
                'inspection_date' => $newDate,
                'original_data' => json_encode([
                    'inspection_date' => $originalDate,
                    'status' => $inspection->status,
                    'approval_notes' => $inspection->approval_notes,
                    'date_history' => $existingHistory
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inspection date updated successfully',
                'data' => [
                    'old_date' => $originalDate,
                    'new_date' => $newDate,
                    'changed_by' => $user->name
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Back date inspection failed', [
                'inspection_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update inspection date: ' . $e->getMessage()
            ], 500);
        }
    }



}
