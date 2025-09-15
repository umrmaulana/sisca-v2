<?php

namespace App\Http\Controllers\SiscaV2;

use Illuminate\Routing\Controller;
use App\Models\SiscaV2\EquipmentType;
use Illuminate\Http\Request;

class EquipmentTypeController extends Controller
{
    public function __construct()
    {
        // Only Admin can create, update, delete
        $this->middleware('role:Admin', ['except' => ['index', 'show']]);
        // All roles can view (index, show)
        $this->middleware('role:Admin,Supervisor,Management', ['only' => ['index', 'show']]);
    }

    public function index(Request $request)
    {
        $query = EquipmentType::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->where('equipment_name', 'like', '%' . $request->search . '%');
        }

        // Filter by equipment type
        // if ($request->filled('equipment_type')) {
        //     $query->where('equipment_type', $request->equipment_type);
        // }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Order by created_at desc by default
        $query->orderBy('created_at', 'desc');

        $equipmentTypes = $query->paginate(10)->appends(request()->query());

        return view('sisca-v2.equipment-types.index', compact('equipmentTypes'));
    }

    public function create()
    {
        return view('sisca-v2.equipment-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipment_name' => 'required|string|max:255',
            'equipment_type' => 'required|string|max:255',
            'desc' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;

        EquipmentType::create($data);

        return redirect()->route('sisca-v2.equipment-types.index')
            ->with('success', 'Equipment Type created successfully.');
    }

    public function show(EquipmentType $equipmentType)
    {
        return view('sisca-v2.equipment-types.show', compact('equipmentType'));
    }

    public function edit(EquipmentType $equipmentType)
    {
        return view('sisca-v2.equipment-types.edit', compact('equipmentType'));
    }

    public function update(Request $request, EquipmentType $equipmentType)
    {
        $request->validate([
            'equipment_name' => 'required|string|max:255' . $equipmentType->id,
            'equipment_type' => 'required|string|max:255',
            'desc' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;

        $equipmentType->update($data);

        return redirect()->route('sisca-v2.equipment-types.index')
            ->with('success', 'Equipment Type updated successfully.');
    }

    public function destroy(EquipmentType $equipmentType)
    {
        // Check if equipment type has related equipment
        if ($equipmentType->equipments()->count() > 0) {
            return redirect()->route('sisca-v2.equipment-types.index')
                ->with('error', 'Cannot delete equipment type. It has related equipment records.');
        }

        $equipmentType->delete();

        return redirect()->route('sisca-v2.equipment-types.index')
            ->with('success', 'Equipment Type deleted successfully.');
    }
}
