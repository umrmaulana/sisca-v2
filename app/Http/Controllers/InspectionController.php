<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\InspectionDetail;
use App\Models\InspectionNgHistory;
use App\Models\Equipment;
use App\Models\ChecksheetTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InspectionController extends Controller
{
    /**
     * Display a listing of the inspections.
     */
    public function index(Request $request)
    {
        $query = Inspection::with(['user', 'equipment', 'details.checksheetTemplate']);

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('inspection_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('inspection_date', '<=', $request->end_date);
        }

        // Filter by equipment
        if ($request->has('equipment_id') && $request->equipment_id) {
            $query->where('equipment_id', $request->equipment_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $inspections = $query->orderBy('inspection_date', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $inspections
        ]);
    }

    /**
     * Show the form for creating a new inspection.
     */
    public function create()
    {
        $equipments = Equipment::with('equipmentType')->get();
        $checksheetTemplates = ChecksheetTemplate::all();

        return response()->json([
            'success' => true,
            'data' => [
                'equipments' => $equipments,
                'checksheet_templates' => $checksheetTemplates
            ]
        ]);
    }

    /**
     * Store a newly created inspection in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|exists:tm_equipments,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'details' => 'required|array',
            'details.*.checksheet_id' => 'required|exists:tm_checksheet_templates,id',
            'details.*.status' => 'required|in:OK,NG,NA',
            'details.*.picture' => 'nullable|image|mimes:jpeg,png,jpg|max:10240'
        ]);

        DB::beginTransaction();

        try {
            $inspection = Inspection::create([
                'user_id' => Auth::id(),
                'equipment_id' => $request->equipment_id,
                'inspection_date' => $request->inspection_date,
                'notes' => $request->notes,
                'status' => 'pending'
            ]);

            foreach ($request->details as $index => $detail) {
                $picturePath = null;

                if (isset($detail['picture']) && $detail['picture']) {
                    $picturePath = $detail['picture']->store('inspections', 'public');
                }

                $inspectionDetail = InspectionDetail::create([
                    'inspection_id' => $inspection->id,
                    'checksheet_id' => $detail['checksheet_id'],
                    'picture' => $picturePath,
                    'status' => $detail['status']
                ]);

                // If status is NG, create history record
                if ($detail['status'] === 'NG') {
                    InspectionNgHistory::create([
                        'original_inspection_id' => $inspection->id,
                        'original_inspection_detail_id' => $inspectionDetail->id,
                        'equipment_id' => $inspection->equipment_id,
                        'checksheet_id' => $detail['checksheet_id'],
                        'user_id' => Auth::id(),
                        'inspection_date' => $inspection->inspection_date,
                        'picture' => $picturePath,
                        'status' => 'NG', // menggunakan field status sesuai migration
                        'notes' => $detail['notes'] ?? null
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inspection created successfully',
                'data' => $inspection->load(['details.checksheetTemplate', 'equipment'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create inspection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified inspection.
     */
    public function show($id)
    {
        $inspection = Inspection::with([
            'user',
            'equipment.equipmentType',
            'equipment.location.area.company',
            'details.checksheetTemplate',
            'ngHistories.checksheetTemplate',
            'approvedBy'
        ])->findOrFail($id);

        // Check if this is an API request
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $inspection
            ]);
        }

        // Return view for web requests
        return view('inspections.show', compact('inspection'));
    }

    /**
     * Show the form for editing the specified inspection.
     */
    public function edit($id)
    {
        $inspection = Inspection::with(['details.checksheetTemplate'])->findOrFail($id);

        if (!$inspection->canBeUpdated()) {
            return response()->json([
                'success' => false,
                'message' => 'This inspection cannot be updated because it has been approved'
            ], 403);
        }

        $equipments = Equipment::with('equipmentType')->get();
        $checksheetTemplates = ChecksheetTemplate::all();

        return response()->json([
            'success' => true,
            'data' => [
                'inspection' => $inspection,
                'equipments' => $equipments,
                'checksheet_templates' => $checksheetTemplates
            ]
        ]);
    }

    /**
     * Update the specified inspection in storage.
     */
    public function update(Request $request, $id)
    {
        $inspection = Inspection::findOrFail($id);

        if (!$inspection->canBeUpdated()) {
            return response()->json([
                'success' => false,
                'message' => 'This inspection cannot be updated because it has been approved'
            ], 403);
        }

        $request->validate([
            'equipment_id' => 'required|exists:tm_equipments,id',
            'inspection_date' => 'required|date',
            'notes' => 'nullable|string',
            'details' => 'required|array',
            'details.*.checksheet_id' => 'required|exists:tm_checksheet_templates,id',
            'details.*.status' => 'required|in:OK,NG,NA',
            'details.*.picture' => 'nullable|image|mimes:jpeg,png,jpg|max:10240'
        ]);

        DB::beginTransaction();

        try {
            $inspection->update([
                'equipment_id' => $request->equipment_id,
                'inspection_date' => $request->inspection_date,
                'notes' => $request->notes
            ]);

            // Delete old details and NG histories
            $inspection->details()->delete();
            $inspection->ngHistories()->delete();

            foreach ($request->details as $index => $detail) {
                $picturePath = null;

                if (isset($detail['picture']) && $detail['picture']) {
                    $picturePath = $detail['picture']->store('inspections', 'public');
                }

                $inspectionDetail = InspectionDetail::create([
                    'inspection_id' => $inspection->id,
                    'checksheet_id' => $detail['checksheet_id'],
                    'picture' => $picturePath,
                    'status' => $detail['status']
                ]);

                // If status is NG, create history record
                if ($detail['status'] === 'NG') {
                    InspectionNgHistory::create([
                        'original_inspection_id' => $inspection->id,
                        'original_inspection_detail_id' => $inspectionDetail->id,
                        'equipment_id' => $inspection->equipment_id,
                        'checksheet_id' => $detail['checksheet_id'],
                        'user_id' => Auth::id(),
                        'inspection_date' => $inspection->inspection_date,
                        'picture' => $picturePath,
                        'status' => 'NG', // menggunakan field status sesuai migration
                        'notes' => $detail['notes'] ?? null
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inspection updated successfully',
                'data' => $inspection->load(['details.checksheetTemplate', 'equipment'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update inspection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified inspection from storage.
     */
    public function destroy($id)
    {
        $inspection = Inspection::findOrFail($id);

        if (!$inspection->canBeUpdated()) {
            return response()->json([
                'success' => false,
                'message' => 'This inspection cannot be deleted because it has been approved'
            ], 403);
        }

        DB::beginTransaction();

        try {
            // Delete associated pictures
            foreach ($inspection->details as $detail) {
                if ($detail->picture && Storage::disk('public')->exists($detail->picture)) {
                    Storage::disk('public')->delete($detail->picture);
                }
            }

            // Delete NG histories pictures
            foreach ($inspection->ngHistories as $ngHistory) {
                if ($ngHistory->picture && Storage::disk('public')->exists($ngHistory->picture)) {
                    Storage::disk('public')->delete($ngHistory->picture);
                }
            }

            $inspection->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inspection deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete inspection: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get NG history for specific equipment
     */
    public function getNgHistory(Request $request)
    {
        $query = InspectionNgHistory::with([
            'originalInspection.user',
            'equipment.equipmentType',
            'checksheetTemplate',
            'user'
        ]);

        // Filter by equipment
        if ($request->has('equipment_id') && $request->equipment_id) {
            $query->where('equipment_id', $request->equipment_id);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('inspection_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('inspection_date', '<=', $request->end_date);
        }

        $ngHistories = $query->orderBy('inspection_date', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $ngHistories
        ]);
    }
}
