<?php

namespace App\Http\Controllers\SiscaV2;

use Illuminate\Routing\Controller;
use App\Models\SiscaV2\Equipment;
use App\Models\SiscaV2\Inspection;
use App\Models\SiscaV2\InspectionDetail;
use App\Models\SiscaV2\ChecksheetTemplate;
use App\Models\SiscaV2\PeriodCheck;
use App\Models\SiscaV2\User;
use App\Models\SiscaV2\EquipmentType;
use App\Models\SiscaV2\Company;
use App\Models\SiscaV2\Area;
use App\Models\SiscaV2\Location;
use App\Models\SiscaV2\InspectionNgHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ChecksheetController extends Controller
{
    /**
     * Display checksheet dashboard with scanner and recent checks
     */
    public function index(Request $request)
    {
        $user = auth('sisca-v2')->user();

        // Role-based access check
        if (!in_array($user->role, ['Pic', 'Admin'])) {
            return redirect()->route('sisca-v2.checksheets.history')
                ->with('info', 'You can only view inspection records.');
        }

        $equipmentCode = $request->get('code');

        // Build query for recent inspections based on user's company
        $inspectionsQuery = Inspection::with([
            'user',
            'equipment.equipmentType',
            'equipment.location.company',
            'equipment.location.area',
            'details'
        ])->whereHas('equipment.location', function ($query) use ($user) {
            if ($user->role !== 'Admin' && $user->role !== 'Management' && $user->company_id) {
                $query->where('company_id', $user->company_id);
            }
        });

        // Apply filters
        if ($request->equipment_type_id) {
            $inspectionsQuery->whereHas('equipment', function ($query) use ($request) {
                $query->where('equipment_type_id', $request->equipment_type_id);
            });
        }

        if ($request->month) {
            $inspectionsQuery->whereMonth('inspection_date', $request->month);
        }

        if ($request->year) {
            $inspectionsQuery->whereYear('inspection_date', $request->year);
        }

        if ($request->company_id && ($user->role === 'Admin' || $user->role === 'Management')) {
            $inspectionsQuery->whereHas('equipment.location', function ($query) use ($request) {
                $query->where('company_id', $request->company_id);
            });
        }

        if ($request->area_id) {
            $inspectionsQuery->whereHas('equipment.location', function ($query) use ($request) {
                $query->where('area_id', $request->area_id);
            });
        }

        $recentInspections = $inspectionsQuery->latest('inspection_date')->paginate(10);

        // Append query parameters to pagination links
        $recentInspections->appends($request->query());

        // Get filter data based on user's company access
        $equipmentTypesQuery = EquipmentType::where('is_active', true);
        if ($user->role !== 'Admin' && $user->role !== 'Management' && $user->company_id) {
            $equipmentTypesQuery->whereHas('equipments.location', function ($query) use ($user) {
                $query->where('company_id', $user->company_id);
            });
        }
        $equipmentTypes = $equipmentTypesQuery->get();

        // Areas dropdown (available for all roles, filtered by company access)
        $areas = collect(); // Initialize as empty collection

        // For non-admin users, load areas from their assigned company
        if ($user->role !== 'Admin' && $user->role !== 'Management' && $user->company_id) {
            $areas = Area::with(['company'])
                ->where('company_id', $user->company_id)
                ->where('is_active', true)
                ->get();
        }
        // For admin/management, load areas based on selected company filter
        elseif (($user->role === 'Admin' || $user->role === 'Management') && $request->company_id) {
            $areas = Area::with(['company'])
                ->where('company_id', $request->company_id)
                ->where('is_active', true)
                ->get();
        }

        // Companies dropdown (only for Admin/Management)
        $companies = [];
        if ($user->role === 'Admin' || $user->role === 'Management') {
            $companies = Company::where('is_active', true)->get();
        }

        // Check if scanning equipment
        $equipment = null;
        $templates = null;
        $periodRestriction = null;

        if ($equipmentCode) {
            // Redirect to create form instead of showing form in index
            return redirect()->route('sisca-v2.checksheets.create', ['code' => $equipmentCode]);
        }

        return view('sisca-v2.checksheets.index', compact(
            'recentInspections',
            'equipmentTypes',
            'areas',
            'companies'
        ));
    }

    /**
     * Show form to create new inspection
     */
    public function create(Request $request)
    {
        $user = auth('sisca-v2')->user();

        // Role-based access check
        if (!in_array($user->role, ['Pic', 'Admin'])) {
            return redirect()->route('sisca-v2.checksheets.history')
                ->with('error', 'Access denied. Only Pic and Admin can perform inspections.');
        }

        $equipmentCode = $request->get('code');

        if (!$equipmentCode) {
            return redirect()->route('sisca-v2.checksheets.index')
                ->with('error', 'Equipment code is required.');
        }

        // Find equipment by code with company filter
        $equipmentQuery = Equipment::where('equipment_code', $equipmentCode)
            ->with(['equipmentType', 'location.company', 'location.area', 'periodCheck']);

        // Apply company filter for non-admin users
        if ($user->role !== 'Admin' && $user->role !== 'Management' && $user->company_id) {
            $equipmentQuery->whereHas('location', function ($query) use ($user) {
                $query->where('company_id', $user->company_id);
            });
        }

        $equipment = $equipmentQuery->first();

        if (!$equipment) {
            return redirect()->route('sisca-v2.checksheets.index')
                ->with('error', 'Equipment with code "' . $equipmentCode . '" not found or not accessible.');
        }

        if (!$equipment->is_active) {
            return redirect()->route('sisca-v2.checksheets.index')
                ->with('error', 'Equipment is not active.');
        }

        // Check period restrictions and existing inspections
        $periodRestriction = $this->checkPeriodRestrictions($equipment);

        // Check if this is a recheck from previous NG inspection
        $previousInspection = null;
        $isRecheck = false;
        $ngOnlyTemplates = collect();

        if ($periodRestriction && isset($periodRestriction['existing_inspection']) && $periodRestriction['has_ng_items']) {
            $previousInspection = $periodRestriction['existing_inspection'];
            $isRecheck = true;

            // Get only NG items for recheck
            $ngDetails = $previousInspection->details()->where('status', 'NG')->get();
            $ngTemplateIds = $ngDetails->pluck('checksheet_id');

            $ngOnlyTemplates = ChecksheetTemplate::whereIn('id', $ngTemplateIds)
                ->where('is_active', true)
                ->orderBy('order_number')
                ->get();
        }

        // Get all templates for normal inspection
        $templates = ChecksheetTemplate::where('equipment_type_id', $equipment->equipment_type_id)
            ->where('is_active', true)
            ->orderBy('order_number')
            ->get();

        if ($templates->isEmpty()) {
            return redirect()->route('sisca-v2.checksheets.index')
                ->with('error', 'No checksheet template found for this equipment type.');
        }

        return view('sisca-v2.checksheets.create', compact(
            'equipment',
            'templates',
            'ngOnlyTemplates',
            'periodRestriction',
            'previousInspection',
            'isRecheck'
        ));
    }

    /**
     * Check period restrictions for equipment
     */
    private function checkPeriodRestrictions($equipment)
    {
        if (!$equipment->periodCheck) {
            return null;
        }

        $period = $equipment->periodCheck->period_check;
        $today = Carbon::today();

        // Get existing inspection for current period
        $existingInspection = $this->getExistingInspection($equipment, $period, $today);

        $canInspect = true;
        $reason = '';
        $hasNgItems = false;

        if ($existingInspection) {
            // Check if inspection is approved
            if ($existingInspection->status === 'approved') {
                $canInspect = false;
                $reason = 'Equipment has been approved and cannot be re-inspected.';
            } else {
                // Check if there are any NG items
                $hasNgItems = $existingInspection->details()->where('status', 'NG')->exists();

                if ($period === 'Daily') {
                    // Daily check - can only inspect once per day unless has NG items
                    if (!$hasNgItems) {
                        $canInspect = false;
                        $reason = 'Daily inspection already completed today. Can only re-inspect if there were NG items.';
                    }
                } elseif ($period === 'Weekly') {
                    // Weekly check - can only inspect once per week unless has NG items
                    if (!$hasNgItems) {
                        $canInspect = false;
                        $reason = 'Weekly inspection already completed this week. Can only re-inspect if there were NG items.';
                    }
                } elseif ($period === 'Monthly') {
                    // Monthly check - can only inspect once per month unless has NG items
                    if (!$hasNgItems) {
                        $canInspect = false;
                        $reason = 'Monthly inspection already completed this month. Can only re-inspect if there were NG items.';
                    }
                } elseif ($period === 'Quarterly') {
                    // Quarterly check - can only inspect once per quarter unless has NG items
                    if (!$hasNgItems) {
                        $canInspect = false;
                        $reason = 'Quarterly inspection already completed this quarter. Can only re-inspect if there were NG items.';
                    }
                } elseif ($period === 'Annual' || $period === 'Yearly') {
                    // Annual/Yearly check - can only inspect once per year unless has NG items
                    if (!$hasNgItems) {
                        $canInspect = false;
                        $reason = 'Annual inspection already completed this year. Can only re-inspect if there were NG items.';
                    }
                }
            }
        }

        return [
            'can_inspect' => $canInspect,
            'reason' => $reason,
            'existing_inspection' => $existingInspection,
            'has_ng_items' => $hasNgItems,
            'period' => $period
        ];
    }

    /**
     * Get existing inspection for the current period
     */
    private function getExistingInspection($equipment, $period, $date)
    {
        $query = Inspection::where('equipment_id', $equipment->id)
            ->with('details');

        switch ($period) {
            case 'Daily':
                $query->whereDate('inspection_date', $date);
                break;
            case 'Weekly':
                // Get the start and end of the current week (Monday to Sunday)
                $startOfWeek = $date->copy()->startOfWeek();
                $endOfWeek = $date->copy()->endOfWeek();
                $query->whereBetween('inspection_date', [$startOfWeek, $endOfWeek]);
                break;
            case 'Monthly':
                $query->whereYear('inspection_date', $date->year)
                    ->whereMonth('inspection_date', $date->month);
                break;
            case 'Quarterly':
                $quarter = ceil($date->month / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $quarter * 3;
                $query->whereYear('inspection_date', $date->year)
                    ->whereMonth('inspection_date', '>=', $startMonth)
                    ->whereMonth('inspection_date', '<=', $endMonth);
                break;
            case 'Annual':
            case 'Yearly':
                $query->whereYear('inspection_date', $date->year);
                break;
            default:
                return null;
        }

        return $query->latest()->first();
    }

    /**
     * Store checksheet inspection
     */

    public function store(Request $request)
    {
        // DEBUG: Log semua input yang masuk
        \Log::info('=== CHECKSHEET STORE DEBUG START ===');
        \Log::info('Request Method: ' . $request->method());
        \Log::info('All Request Data:', $request->all());
        \Log::info('Has is_recheck field: ' . ($request->has('is_recheck') ? 'YES' : 'NO'));
        \Log::info('is_recheck value: ' . $request->is_recheck);
        \Log::info('previous_inspection_id: ' . $request->previous_inspection_id);

        $request->validate([
            'equipment_id' => 'required|exists:tm_equipments,id',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array',
            'items.*.checksheet_id' => 'required|exists:tm_checksheet_templates,id',
            'items.*.status' => 'required|in:OK,NG,NA',
            'items.*.picture' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
            'is_recheck' => 'nullable|boolean',
            'previous_inspection_id' => 'nullable|exists:tt_inspections,id',
        ]);

        try {
            DB::beginTransaction();

            $equipment = Equipment::findOrFail($request->equipment_id);
            $isRecheck = $request->has('is_recheck') && $request->is_recheck;
            $previousInspectionId = $request->previous_inspection_id;

            \Log::info('After processing:', [
                'isRecheck' => $isRecheck,
                'previousInspectionId' => $previousInspectionId,
                'equipment_id' => $equipment->id
            ]);

            // Check if user has permission (only PIC and Admin can perform checksheet)
            if (!in_array(auth('sisca-v2')->user()->role, ['Pic', 'Admin'])) {
                return redirect()->back()->with('error', 'You are not authorized to perform inspections.');
            }

            // Process recheck scenario  
            if ($isRecheck && $previousInspectionId) {
                \Log::info('=== PROCESSING RECHECK SCENARIO ===');

                $existingInspection = Inspection::findOrFail($previousInspectionId);
                \Log::info('Found existing inspection:', [
                    'id' => $existingInspection->id,
                    'equipment_id' => $existingInspection->equipment_id,
                    'inspection_date' => $existingInspection->inspection_date
                ]);

                // CRITICAL FIX: Get ALL NG details that were present during initial inspection 
                // including ones that might have been updated in previous recheck attempts
                $previousNgDetails = $existingInspection->details()->where('status', 'NG')->get();

                // Also check if there are any NG items in the current form submission
                $currentNgItemIds = collect($request->items)
                    ->filter(function ($item) {
                        return $item['status'] === 'NG';
                    })
                    ->pluck('checksheet_id');

                \Log::info('Analysis:', [
                    'current_ng_items_in_form' => $currentNgItemIds->toArray(),
                    'previous_ng_details_count' => $previousNgDetails->count(),
                    'form_items_count' => count($request->items)
                ]);

                // Get NG items that were being rechecked (from form) but are still NG
                $recheckNgItems = collect($request->items)
                    ->filter(function ($item) {
                        return $item['status'] === 'NG';
                    })
                    ->pluck('checksheet_id');

                \Log::info('Recheck NG items that are still NG:', $recheckNgItems->toArray());

                // IMPORTANT: Create history for items that WERE NG and are now being addressed
                // This includes items that are now OK or still NG in the recheck
                $itemsBeingRechecked = collect($request->items)->pluck('checksheet_id');

                foreach ($previousNgDetails as $ngDetail) {
                    // Only create history if this NG item is being addressed in current recheck
                    if ($itemsBeingRechecked->contains($ngDetail->checksheet_id)) {
                        \Log::info('Creating history for NG item being addressed:', [
                            'detail_id' => $ngDetail->id,
                            'checksheet_id' => $ngDetail->checksheet_id
                        ]);

                        // Check if history already exists
                        $historyExists = InspectionNgHistory::where('original_inspection_id', $existingInspection->id)
                            ->where('checksheet_id', $ngDetail->checksheet_id)
                            ->exists();

                        \Log::info('History exists check:', [
                            'exists' => $historyExists,
                            'original_inspection_id' => $existingInspection->id,
                            'checksheet_id' => $ngDetail->checksheet_id
                        ]);

                        if (!$historyExists) {
                            $historyData = [
                                'original_inspection_id' => $existingInspection->id,
                                'equipment_id' => $existingInspection->equipment_id,
                                'checksheet_id' => $ngDetail->checksheet_id,
                                'user_id' => $existingInspection->user_id,
                                'inspection_date' => $existingInspection->inspection_date,
                                'picture' => $ngDetail->picture,
                                'status' => 'NG',
                                'notes' => $existingInspection->notes,
                            ];

                            \Log::info('Attempting to create NG History with data:', $historyData);

                            try {
                                $createdHistory = InspectionNgHistory::create($historyData);
                                \Log::info('NG History created successfully:', [
                                    'history_id' => $createdHistory->id,
                                    'checksheet_id' => $ngDetail->checksheet_id
                                ]);
                            } catch (\Exception $historyError) {
                                \Log::error('FAILED to create NG History:', [
                                    'error_message' => $historyError->getMessage(),
                                    'error_line' => $historyError->getLine(),
                                    'error_file' => $historyError->getFile(),
                                    'data_attempted' => $historyData
                                ]);
                                throw $historyError; // Re-throw to trigger rollback
                            }
                        } else {
                            \Log::info('NG History already exists, skipping creation');
                        }
                    } else {
                        \Log::info('NG item not being rechecked, skipping history creation:', [
                            'checksheet_id' => $ngDetail->checksheet_id
                        ]);
                    }
                }

                // Get current inspection details that are OK (before deleting)
                $currentOkDetails = $existingInspection->details()->where('status', 'OK')->get();
                \Log::info('Found OK details count: ' . $currentOkDetails->count());

                // CRITICAL FIX: Store OK details data before deletion to avoid losing reference
                $okDetailsData = $currentOkDetails->map(function ($detail) {
                    return [
                        'checksheet_id' => $detail->checksheet_id,
                        'status' => $detail->status,
                        'picture' => $detail->picture
                    ];
                })->toArray();

                \Log::info('Stored OK details data:', $okDetailsData);

                // Update existing inspection BEFORE deleting details
                $inspection = $existingInspection;
                $inspection->update([
                    'user_id' => auth('sisca-v2')->id(),
                    'inspection_date' => Carbon::today(),
                    'notes' => $request->notes,
                    'status' => 'pending',
                    'updated_at' => now()
                ]);
                \Log::info('Updated existing inspection:', ['id' => $inspection->id]);

                // IMPORTANT: Delete old details AFTER creating NG History to avoid cascade delete
                $inspection->details()->delete();
                \Log::info('Deleted old inspection details');

                // Re-create OK details that weren't rechecked using stored data
                foreach ($okDetailsData as $okData) {
                    $wasRechecked = collect($request->items)->firstWhere('checksheet_id', $okData['checksheet_id']);

                    if (!$wasRechecked) {
                        // Keep the OK status for items not rechecked
                        InspectionDetail::create([
                            'inspection_id' => $inspection->id,
                            'checksheet_id' => $okData['checksheet_id'],
                            'status' => $okData['status'],
                            'picture' => $okData['picture']
                        ]);
                        \Log::info('Re-created OK detail:', ['checksheet_id' => $okData['checksheet_id']]);
                    }
                }

                $message = 'Equipment re-inspection completed successfully. Previous NG items have been moved to history.';
            } else {
                \Log::info('=== PROCESSING NEW INSPECTION SCENARIO ===');

                // Create new inspection
                $inspection = Inspection::create([
                    'user_id' => auth('sisca-v2')->id(),
                    'equipment_id' => $request->equipment_id,
                    'inspection_date' => Carbon::today(),
                    'notes' => $request->notes,
                    'status' => 'pending',
                ]);
                \Log::info('Created new inspection:', ['id' => $inspection->id]);

                $message = 'Equipment inspection completed successfully.';
            }

            \Log::info('=== STORING NEW DETAILS FROM FORM ===');
            \Log::info('Items to store count: ' . count($request->items));

            // Store new inspection details from form submission
            foreach ($request->items as $index => $item) {
                \Log::info("Processing item #$index:", [
                    'checksheet_id' => $item['checksheet_id'],
                    'status' => $item['status'],
                    'has_picture' => isset($item['picture'])
                ]);

                $detail = new InspectionDetail([
                    'inspection_id' => $inspection->id,
                    'checksheet_id' => $item['checksheet_id'],
                    'status' => $item['status'],
                ]);

                // Handle picture upload
                if (isset($item['picture']) && $item['picture']) {
                    $file = $item['picture'];
                    $filename = 'checksheet_' . $inspection->id . '_' . $item['checksheet_id'] . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('sisca-v2/checksheet-photos', $filename, 'public');
                    $detail->picture = $path;
                    \Log::info('Uploaded picture:', ['path' => $path]);
                }

                $detail->save();
                \Log::info('Saved detail:', ['detail_id' => $detail->id]);
            }

            DB::commit();
            \Log::info('=== TRANSACTION COMMITTED SUCCESSFULLY ===');

            // Final verification - count NG histories
            if ($isRecheck && $previousInspectionId) {
                $historyCount = InspectionNgHistory::where('original_inspection_id', $previousInspectionId)->count();
                \Log::info('Final NG History count in database: ' . $historyCount);
            }

            \Log::info('=== CHECKSHEET STORE DEBUG END ===');

            return redirect()->route('sisca-v2.checksheets.show', $inspection->id)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('=== TRANSACTION ROLLED BACK ===');
            \Log::error('Error details:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Error saving inspection: ' . $e->getMessage());
        }
    }

    /**
     * Show inspection details
     */
    public function show($id)
    {
        $inspection = Inspection::with([
            'user',
            'equipment.equipmentType',
            'equipment.location.company',
            'equipment.location.area',
            'details.checksheetTemplate'
        ])->findOrFail($id);

        return view('sisca-v2.checksheets.show', compact('inspection'));
    }

    /**
     * Show inspection history for equipment
     */
    public function history(Request $request)
    {
        $user = auth('sisca-v2')->user();

        $query = Inspection::with([
            'user',
            'equipment.equipmentType',
            'equipment.location.company',
            'equipment.location.area'
        ])->whereHas('equipment.location', function ($locationQuery) use ($user) {
            // Filter by user's company if not Admin/Management
            if ($user->role !== 'Admin' && $user->role !== 'Management' && $user->company_id) {
                $locationQuery->where('company_id', $user->company_id);
            }
        })->orderBy('created_at', 'desc');

        // Filter by equipment if provided
        if ($request->equipment_id) {
            $query->where('equipment_id', $request->equipment_id);
        }

        // Filter by date range
        if ($request->from_date) {
            $query->whereDate('inspection_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('inspection_date', '<=', $request->to_date);
        }

        // Filter by inspector
        if ($request->inspector_id) {
            $query->where('user_id', $request->inspector_id);
        }

        // Filter by company (only for Admin/Management)
        if ($request->company_id && ($user->role === 'Admin' || $user->role === 'Management')) {
            $query->whereHas('equipment.location', function ($locationQuery) use ($request) {
                $locationQuery->where('company_id', $request->company_id);
            });
        }

        // Filter by area (available for all roles)
        if ($request->area_id) {
            $query->whereHas('equipment.location', function ($equipmentQuery) use ($request) {
                $equipmentQuery->where('area_id', $request->area_id);
            });
        }

        $inspections = $query->latest('inspection_date')->paginate(20);

        // Append query parameters to pagination links
        $inspections->appends($request->query());

        // Get data for filters based on user's company access
        $equipmentsQuery = Equipment::with(['equipmentType', 'location.company'])->where('is_active', true);
        if ($user->role !== 'Admin' && $user->role !== 'Management' && $user->company_id) {
            $equipmentsQuery->whereHas('location', function ($query) use ($user) {
                $query->where('company_id', $user->company_id);
            });
        }
        $equipments = $equipmentsQuery->get();

        $inspectors = User::where('role', 'Pic')->get();

        // Areas dropdown (available for all roles, filtered by company access)
        $areas = collect(); // Initialize as empty collection

        // For non-admin users, load areas from their assigned company
        if ($user->role !== 'Admin' && $user->role !== 'Management' && $user->company_id) {
            $areas = Area::with(['company'])
                ->where('company_id', $user->company_id)
                ->where('is_active', true)
                ->get();
        }
        // For admin/management, load areas based on selected company filter
        elseif (($user->role === 'Admin' || $user->role === 'Management') && $request->company_id) {
            $areas = Area::with(['company'])
                ->where('company_id', $request->company_id)
                ->where('is_active', true)
                ->get();
        }

        // Companies dropdown (only for Admin/Management)
        $companies = [];
        if ($user->role === 'Admin' || $user->role === 'Management') {
            $companies = Company::where('is_active', true)->get();
        }

        return view('sisca-v2.checksheets.history', compact('inspections', 'equipments', 'inspectors', 'areas', 'companies'));
    }

    /**
     * Get equipment details via AJAX
     */
    public function getEquipment($code)
    {
        $equipment = Equipment::where('equipment_code', $code)
            ->with(['equipmentType', 'location.company', 'location.area', 'periodCheck'])
            ->first();

        if (!$equipment) {
            return response()->json(['error' => 'Equipment not found'], 404);
        }

        return response()->json([
            'equipment' => $equipment,
            'templates' => ChecksheetTemplate::where('equipment_type_id', $equipment->equipment_type_id)
                ->where('is_active', true)
                ->orderBy('order_number')
                ->get()
        ]);
    }

    /**
     * Get areas by company via AJAX
     */
    public function getAreasByCompany(Request $request)
    {
        $companyId = $request->get('company_id');
        $user = auth('sisca-v2')->user();

        if (!$companyId) {
            return response()->json(['areas' => []]);
        }

        // Check if user has access to this company
        if ($user->role !== 'Admin' && $user->role !== 'Management') {
            if ($user->company_id && $user->company_id != $companyId) {
                return response()->json(['error' => 'Unauthorized access to this company'], 403);
            }
        }

        $areas = Area::where('company_id', $companyId)
            ->where('is_active', true)
            ->get()
            ->map(function ($area) {
                return [
                    'id' => $area->id,
                    'text' => $area->area_name,
                    'area_name' => $area->area_name,
                ];
            });

        return response()->json(['areas' => $areas]);
    }

    /**
     * Approve an inspection (Supervisor/Management only)
     */
    public function approve(Request $request, $id)
    {
        $user = auth('sisca-v2')->user();

        // Check authorization
        if (!in_array($user->role, ['Supervisor', 'Management', 'Admin'])) {
            return redirect()->back()->with('error', 'You are not authorized to approve inspections.');
        }

        $inspection = Inspection::findOrFail($id);

        // Check if already approved
        if ($inspection->status === 'approved') {
            return redirect()->back()->with('info', 'This inspection is already approved.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'approval_notes' => 'nullable|string|max:1000',
        ]);

        $inspection->update([
            'status' => $request->action === 'approve' ? 'approved' : 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'approval_notes' => $request->approval_notes,
        ]);

        $message = $request->action === 'approve'
            ? 'Inspection approved successfully.'
            : 'Inspection rejected successfully.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * View all NG History (Management/Supervisor/Admin only)
     */
    public function ngHistoryIndex(Request $request)
    {
        $user = auth('sisca-v2')->user();

        // Check authorization - only Management, Supervisor, and Admin can access
        if (!in_array($user->role, ['Management', 'Supervisor', 'Admin'])) {
            abort(403, 'You do not have permission to access NG History.');
        }

        $query = InspectionNgHistory::with([
            'originalInspection.user',
            'equipment.equipmentType',
            'equipment.location.company',
            'equipment.location.area',
            'checksheetTemplate',
            'user'
        ]);

        // Apply company filter for non-admin users
        if ($user->role !== 'Admin' && $user->company_id) {
            $query->whereHas('equipment.location', function ($locationQuery) use ($user) {
                $locationQuery->where('company_id', $user->company_id);
            });
        }

        // Apply filters
        if ($request->equipment_code) {
            $query->whereHas('equipment', function ($equipmentQuery) use ($request) {
                $equipmentQuery->where('equipment_code', 'like', '%' . $request->equipment_code . '%');
            });
        }

        if ($request->equipment_type_id) {
            $query->whereHas('equipment', function ($equipmentQuery) use ($request) {
                $equipmentQuery->where('equipment_type_id', $request->equipment_type_id);
            });
        }

        if ($request->company_id && $user->role === 'Admin') {
            $query->whereHas('equipment.location', function ($locationQuery) use ($request) {
                $locationQuery->where('company_id', $request->company_id);
            });
        }

        if ($request->from_date) {
            $query->whereDate('inspection_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('inspection_date', '<=', $request->to_date);
        }

        if ($request->inspector_id) {
            $query->where('user_id', $request->inspector_id);
        }

        $ngHistories = $query->orderBy('inspection_date', 'desc')->paginate(20);

        // Append query parameters to pagination links
        $ngHistories->appends($request->query());

        // Get filter data
        $equipmentTypesQuery = EquipmentType::where('is_active', true);
        if ($user->role !== 'Admin' && $user->company_id) {
            $equipmentTypesQuery->whereHas('equipments.location', function ($query) use ($user) {
                $query->where('company_id', $user->company_id);
            });
        }
        $equipmentTypes = $equipmentTypesQuery->get();

        $inspectors = User::where('role', 'Pic')->get();

        // Companies dropdown (only for Admin)
        $companies = [];
        if ($user->role === 'Admin') {
            $companies = Company::where('is_active', true)->get();
        }

        return view('sisca-v2.ng-history.index', compact('ngHistories', 'equipmentTypes', 'inspectors', 'companies'));
    }
}
