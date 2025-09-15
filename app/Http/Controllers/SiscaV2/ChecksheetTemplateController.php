<?php

namespace App\Http\Controllers\SiscaV2;

use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Models\SiscaV2\ChecksheetTemplate;
use App\Models\SiscaV2\EquipmentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChecksheetTemplateController extends Controller
{
    public function __construct()
    {
        // Only Admin can create, update, delete
        $this->middleware('role:Admin', ['except' => ['index', 'show']]);
        // All roles can view (index, show)
        $this->middleware('role:Admin,Supervisor,Management', ['only' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $this->authorize('viewAny', ChecksheetTemplate::class);
        $user = Auth::guard('sisca-v2')->user();

        // Handle AJAX request for next order number
        if ($request->ajax() && $request->get('ajax') === 'next_order') {
            $equipmentTypeId = $request->get('equipment_type_id');
            if ($equipmentTypeId) {
                $maxOrder = ChecksheetTemplate::where('equipment_type_id', $equipmentTypeId)->max('order_number');
                return response()->json(['next_order' => $maxOrder ? $maxOrder + 1 : 1]);
            }
            return response()->json(['next_order' => 1]);
        }

        $query = ChecksheetTemplate::with(['equipmentType', 'creator', 'updater']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('standar_condition', 'like', "%{$search}%")
                    ->orWhereHas('equipmentType', function ($eq) use ($search) {
                        $eq->where('equipment_name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by equipment type
        if ($request->filled('equipment_type')) {
            $query->where('equipment_type_id', $request->get('equipment_type'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status') == '1');
        }

        $checksheetTemplates = $query->orderBy('equipment_type_id')
            ->orderBy('order_number')
            ->paginate(15);

        // Get equipment types for filter
        $equipmentTypes = EquipmentType::where('is_active', true)->get();

        return view('sisca-v2.checksheet-templates.index', compact('checksheetTemplates', 'equipmentTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::guard('sisca-v2')->user();

        $equipmentTypes = EquipmentType::where('is_active', true)->get();

        return view('sisca-v2.checksheet-templates.create', compact('equipmentTypes'));
    }

    /**
     * Get next order number for equipment type
     */
    public function getNextOrder($equipmentTypeId)
    {
        $nextOrder = ChecksheetTemplate::getNextOrderNumber($equipmentTypeId);
        return response()->json(['next_order' => $nextOrder]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('sisca-v2')->user();

        $request->validate([
            'equipment_type_id' => 'required|exists:tm_equipment_types,id',
            'order_number' => 'nullable|integer|min:1',
            'item_name' => 'required|string|max:255',
            'standar_condition' => 'nullable|string|max:1000',
            'standar_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'sometimes|boolean'
        ], [
            'equipment_type_id.required' => 'Equipment type is required',
            'equipment_type_id.exists' => 'Selected equipment type is invalid',
            'order_number.integer' => 'Order number must be a number',
            'order_number.min' => 'Order number must be at least 1',
            'item_name.required' => 'Item name is required',
            'item_name.max' => 'Item name may not be greater than 255 characters',
            'standar_picture.image' => 'Standard picture must be an image',
            'standar_picture.mimes' => 'Standard picture must be a file of type: jpeg, png, jpg, gif',
            'standar_picture.max' => 'Standard picture may not be greater than 5MB'
        ]);

        // Auto-assign order number if not provided
        $orderNumber = $request->order_number;
        if (empty($orderNumber)) {
            $orderNumber = ChecksheetTemplate::getNextOrderNumber($request->equipment_type_id);
        }

        // Check for duplicate order number within same equipment type
        if (ChecksheetTemplate::orderExists($request->equipment_type_id, $orderNumber)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['order_number' => 'Order number already exists for this equipment type']);
        }

        try {
            DB::beginTransaction();

            $data = $request->only([
                'equipment_type_id',
                'item_name',
                'standar_condition'
            ]);

            $data['order_number'] = $orderNumber;
            $data['is_active'] = $request->has('is_active');
            $data['created_by'] = auth()->id();
            $data['updated_by'] = auth()->id();

            // Handle standard picture upload
            if ($request->hasFile('standar_picture')) {
                $file = $request->file('standar_picture');
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('sisca-v2/templates/checksheet', $filename, 'public');
                $data['standar_picture'] = $path;
            }

            $checksheetTemplate = ChecksheetTemplate::create($data);

            DB::commit();

            return redirect()->route('sisca-v2.checksheet-templates.show', $checksheetTemplate)
                ->with('success', 'Checksheet template created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded file if exists
            if (isset($path) && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create checksheet template: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ChecksheetTemplate $checksheetTemplate)
    {
        $user = Auth::guard('sisca-v2')->user();

        $checksheetTemplate->load(['equipmentType', 'creator', 'updater']);

        return view('sisca-v2.checksheet-templates.show', compact('checksheetTemplate'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChecksheetTemplate $checksheetTemplate)
    {
        $user = Auth::guard('sisca-v2')->user();

        $equipmentTypes = EquipmentType::where('is_active', true)->get();

        return view('sisca-v2.checksheet-templates.edit', compact('checksheetTemplate', 'equipmentTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ChecksheetTemplate $checksheetTemplate)
    {
        $user = Auth::guard('sisca-v2')->user();

        $request->validate([
            'equipment_type_id' => 'required|exists:tm_equipment_types,id',
            'order_number' => 'required|integer|min:1',
            'item_name' => 'required|string|max:255',
            'standar_condition' => 'nullable|string|max:1000',
            'standar_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'sometimes|boolean'
        ], [
            'equipment_type_id.required' => 'Equipment type is required',
            'equipment_type_id.exists' => 'Selected equipment type is invalid',
            'order_number.required' => 'Order number is required',
            'order_number.integer' => 'Order number must be a number',
            'order_number.min' => 'Order number must be at least 1',
            'item_name.required' => 'Item name is required',
            'item_name.max' => 'Item name may not be greater than 255 characters',
            'standar_picture.image' => 'Standard picture must be an image',
            'standar_picture.mimes' => 'Standard picture must be a file of type: jpeg, png, jpg, gif',
            'standar_picture.max' => 'Standard picture may not be greater than 5MB'
        ]);

        // Check for duplicate order number within same equipment type (excluding current record)
        if (ChecksheetTemplate::orderExists($request->equipment_type_id, $request->order_number, $checksheetTemplate->id)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['order_number' => 'Order number already exists for this equipment type']);
        }

        try {
            DB::beginTransaction();

            $data = $request->only([
                'equipment_type_id',
                'order_number',
                'item_name',
                'standar_condition'
            ]);

            $data['is_active'] = $request->has('is_active');
            $data['updated_by'] = auth()->id();

            // Handle standard picture upload
            if ($request->hasFile('standar_picture')) {
                // Delete old picture if exists
                if ($checksheetTemplate->standar_picture && Storage::disk('public')->exists($checksheetTemplate->standar_picture)) {
                    Storage::disk('public')->delete($checksheetTemplate->standar_picture);
                }

                $file = $request->file('standar_picture');
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('sisca-v2/templates/checksheet', $filename, 'public');
                $data['standar_picture'] = $path;
            }

            $checksheetTemplate->update($data);

            DB::commit();

            return redirect()->route('sisca-v2.checksheet-templates.show', $checksheetTemplate)
                ->with('success', 'Checksheet template updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update checksheet template: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChecksheetTemplate $checksheetTemplate)
    {
        $user = Auth::guard('sisca-v2')->user();

        try {
            DB::beginTransaction();

            // Delete standard picture if exists
            if ($checksheetTemplate->standar_picture && Storage::disk('public')->exists($checksheetTemplate->standar_picture)) {
                Storage::disk('public')->delete($checksheetTemplate->standar_picture);
            }

            $checksheetTemplate->delete();

            DB::commit();

            return redirect()->route('sisca-v2.checksheet-templates.index')
                ->with('success', 'Checksheet template deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete checksheet template: ' . $e->getMessage()]);
        }
    }
}
