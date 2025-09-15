<?php

namespace App\Http\Controllers\SiscaV2;

use App\Models\SiscaV2\Area;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Admin', ['except' => ['index', 'show']]);
        $this->middleware('role:Admin,Supervisor,Management', ['only' => ['index', 'show']]);
    }

    public function index(Request $request)
    {
        $query = Area::with('plant');

        // Search functionality
        if ($request->filled('search')) {
            $query->where('area_name', 'like', '%' . $request->search . '%')
                ->orWhereHas('plant', function ($q) use ($request) {
                    $q->where('plant_name', 'like', '%' . $request->search . '%');
                });
        }

        // Filter by plant
        if ($request->filled('plant_id')) {
            $query->where('plant_id', $request->plant_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Order by created_at desc by default
        $query->orderBy('created_at', 'desc');

        $areas = $query->paginate(10)->appends(request()->query());
        $plants = \App\Models\SiscaV2\Plant::where('is_active', true)->orderBy('plant_name')->get();

        return view('sisca-v2.areas.index', compact('areas', 'plants'));
    }

    public function create()
    {
        $plants = \App\Models\SiscaV2\Plant::where('is_active', true)->orderBy('plant_name')->get();
        return view('sisca-v2.areas.create', compact('plants'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'area_name' => 'required|string|max:255|unique:tm_areas,area_name',
            'plant_id' => 'required|exists:tm_plants,id',
            'mapping_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['area_name', 'plant_id']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle mapping picture upload
        if ($request->hasFile('mapping_picture')) {
            $file = $request->file('mapping_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('sisca-v2/templates/mapping', $filename, 'public');
            $data['mapping_picture'] = $path;
        }

        Area::create($data);

        return redirect()->route('sisca-v2.areas.index')
            ->with('success', 'Area created successfully.');
    }

    public function show(Area $area)
    {
        $plants = \App\Models\SiscaV2\Plant::where('is_active', true)->orderBy('plant_name')->get();
        $area->load('plant', 'locations');
        return view('sisca-v2.areas.show', compact('area', 'plants'));

        $this->authorize('view', $area);

        $area->load(['locations', 'plant']);

        return view('sisca-v2.areas.show', compact('area', 'plants'));
    }

    public function edit(Area $area)
    {
        $plants = \App\Models\SiscaV2\Plant::where('is_active', true)->orderBy('plant_name')->get();
        return view('sisca-v2.areas.edit', compact('area', 'plants'));
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'area_name' => 'required|string|max:255|unique:tm_areas,area_name,' . $area->id,
            'plant_id' => 'required|exists:tm_plants,id',
            'mapping_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['area_name', 'plant_id']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle mapping picture upload
        if ($request->hasFile('mapping_picture')) {
            // Delete old file if exists
            if ($area->mapping_picture && \Storage::disk('public')->exists($area->mapping_picture)) {
                \Storage::disk('public')->delete($area->mapping_picture);
            }

            $file = $request->file('mapping_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('sisca-v2/templates/mapping', $filename, 'public');
            $data['mapping_picture'] = $path;
        }

        $area->update($data);

        return redirect()->route('sisca-v2.areas.index')
            ->with('success', 'Area updated successfully.');
    }

    public function destroy(Area $area)
    {
        // Check if area has related locations
        if ($area->locations()->count() > 0) {
            return redirect()->route('sisca-v2.areas.index')
                ->with('error', 'Cannot delete area. It has related location records.');
        }

        // Delete mapping picture if exists
        if ($area->mapping_picture && \Storage::disk('public')->exists($area->mapping_picture)) {
            \Storage::disk('public')->delete($area->mapping_picture);
        }

        $area->delete();

        return redirect()->route('sisca-v2.areas.index')
            ->with('success', 'Area deleted successfully.');
    }
}
