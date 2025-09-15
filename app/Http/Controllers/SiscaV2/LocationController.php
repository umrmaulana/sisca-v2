<?php

namespace App\Http\Controllers\SiscaV2;

use Illuminate\Routing\Controller;
use App\Models\SiscaV2\Location;
use App\Models\SiscaV2\Plant;
use App\Models\SiscaV2\Area;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct()
    {
        // Only Admin can create, update, delete
        $this->middleware('role:Admin')->except(['index', 'show', 'getAreasByPlant']);
        // All roles can view (index, show) and use AJAX helper
        $this->middleware('role:Admin,Supervisor,Management')->only(['index', 'show', 'getAreasByPlant']);
    }

    public function index(Request $request)
    {
        $query = Location::with(['plant', 'area']);

        // Filter by plant
        if ($request->filled('plant_id')) {
            $query->where('plant_id', $request->plant_id);
        }

        // Filter by area
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        // Search by location code
        if ($request->filled('search')) {
            $query->where('location_code', 'like', '%' . $request->search . '%');
        }

        $locations = $query->paginate(10)->appends($request->query());
        $plants = Plant::where('is_active', true)->get();
        $areas = Area::where('is_active', true)->get();

        return view('sisca-v2.locations.index', compact('locations', 'plants', 'areas'));
    }

    public function create(Request $request)
    {
        $plants = Plant::where('is_active', true)->get();

        // Get areas based on selected plant
        $areas = collect();
        if ($request->filled('plant_id')) {
            $areas = Area::where('is_active', true)
                ->where('plant_id', $request->plant_id)
                ->get();
        }

        return view('sisca-v2.locations.create', compact('plants', 'areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_code' => 'required|string|max:20|unique:tm_locations_new,location_code',
            'plant_id' => 'required|exists:tm_plants,id',
            'area_id' => 'required|exists:tm_areas,id',
            'pos' => 'nullable|string|max:255',
            'coordinate_x' => 'nullable|numeric|between:-999999.999999,999999.999999',
            'coordinate_y' => 'nullable|numeric|between:-999999.999999,999999.999999',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;

        Location::create($data);

        return redirect()->route('sisca-v2.locations.index')
            ->with('success', 'Location created successfully.');
    }

    public function show(Location $location)
    {
        $location->load(['plant', 'area', 'equipments']);
        return view('sisca-v2.locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        $plants = Plant::where('is_active', true)->get();
        $areas = Area::where('is_active', true)
            ->where('plant_id', $location->plant_id)
            ->get();

        return view('sisca-v2.locations.edit', compact('location', 'plants', 'areas'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'location_code' => 'required|string|max:20|unique:tm_locations_new,location_code,' . $location->id,
            'plant_id' => 'required|exists:tm_plants,id',
            'area_id' => 'required|exists:tm_areas,id',
            'pos' => 'nullable|string|max:255',
            'coordinate_x' => 'nullable|numeric|between:-999999.999999,999999.999999',
            'coordinate_y' => 'nullable|numeric|between:-999999.999999,999999.999999',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;

        $location->update($data);

        return redirect()->route('sisca-v2.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('sisca-v2.locations.index')
            ->with('success', 'Location deleted successfully.');
    }

    // AJAX method to get areas by plant
    public function getAreasByPlant($plantId)
    {
        $areas = Area::where('plant_id', $plantId)
            ->where('is_active', true)
            ->get(['id', 'area_name', 'mapping_picture']);

        return response()->json($areas);
    }
}
