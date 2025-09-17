<?php

namespace App\Http\Controllers\SiscaV2;

use App\Http\Controllers\Controller;
use App\Models\SiscaV2\Location;
use App\Models\SiscaV2\Plant;
use App\Models\SiscaV2\Area;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct()
    {
        // Only Admin and Supervisor can create, update, delete
        $this->middleware('role:Admin')->except(['index', 'show', 'getAreasByPlant', 'getPlantData']);
        // All roles can view (index, show) and use AJAX helper
        $this->middleware('role:Admin,Supervisor,Management')->only(['index', 'show', 'getAreasByPlant', 'getPlantData']);
    }

    public function index(Request $request)
    {
        $user = auth('sisca-v2')->user();

        $query = Location::with(['plant', 'area']);

        // Apply plant filter for non-admin users
        if ($user->role === 'Supervisor' && $user->plant_id) {
            $query->where('plant_id', $user->plant_id);
        }

        // Filter by plant (for Admin/Management or when specified)
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

        // Plants dropdown (only for Admin/Management)
        $plants = collect();
        if ($user->role === 'Admin' || $user->role === 'Management') {
            $plants = Plant::where('is_active', true)->get();
        } elseif ($user->role === 'Supervisor' && $user->plant_id) {
            $plants = Plant::where('id', $user->plant_id)->where('is_active', true)->get();
        }

        // Areas dropdown (filtered by accessible plants)
        $areas = collect();
        if ($user->role === 'Admin' || $user->role === 'Management') {
            $areas = Area::where('is_active', true)->get();
        } elseif ($user->role === 'Supervisor' && $user->plant_id) {
            $areas = Area::where('plant_id', $user->plant_id)->where('is_active', true)->get();
        }

        return view('sisca-v2.locations.index', compact('locations', 'plants', 'areas'));
    }

    public function create(Request $request)
    {
        // $user = auth('sisca-v2')->user();

        // // For supervisor, auto-select their assigned plant
        // if ($user->role === 'Supervisor' && $user->plant_id) {
        //     $plants = Plant::where('id', $user->plant_id)
        //         ->where('is_active', true)
        //         ->get();

        //     // Auto-load areas for supervisor's plant
        //     $areas = Area::where('plant_id', $user->plant_id)
        //         ->where('is_active', true)
        //         ->get();
        // } else {
        //     // For Admin/Management - show all plants
        // }
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
        $user = auth('sisca-v2')->user();

        $request->validate([
            'location_name' => 'required|string|max:100',
            'plant_id' => 'required|exists:tm_plants,id',
            'area_id' => 'required|exists:tm_areas,id',
            'coordinate_x' => 'nullable|numeric|between:-999999.999999,999999.999999',
            'coordinate_y' => 'nullable|numeric|between:-999999.999999,999999.999999',
            'plant_coordinate_x' => 'nullable|numeric|between:-999999.999999,999999.999999',
            'plant_coordinate_y' => 'nullable|numeric|between:-999999.999999,999999.999999',
        ]);

        // For supervisor, ensure they can only create locations in their assigned plant
        if ($user->role === 'Supervisor' && $user->plant_id) {
            if ($request->plant_id != $user->plant_id) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You can only create locations in your assigned plant.');
            }
        }

        // Validate area belongs to the selected plant
        $area = Area::where('id', $request->area_id)
            ->where('plant_id', $request->plant_id)
            ->first();

        if (!$area) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Selected area does not belong to the selected plant.');
        }

        // Prepare data for insertion
        $data = [
            'location_code' => $request->location_name, // Map location_name to location_code
            'plant_id' => $request->plant_id,
            'area_id' => $request->area_id,
            'coordinate_x' => $request->coordinate_x,
            'coordinate_y' => $request->coordinate_y,
            'plant_coordinate_x' => $request->plant_coordinate_x,
            'plant_coordinate_y' => $request->plant_coordinate_y,
            'pos' => $request->pos ?? null,
            'is_active' => $request->has('is_active') ? true : false,
        ];

        try {
            \Log::info('Creating location with data:', $data);
            $location = Location::create($data);
            \Log::info('Location created successfully with ID: ' . $location->id);

            return redirect()->route('sisca-v2.locations.index')
                ->with('success', 'Location created successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to create location: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create location: ' . $e->getMessage());
        }
    }

    public function show(Location $location)
    {
        $location->load(['plant', 'area', 'equipments']);
        return view('sisca-v2.locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        $user = auth('sisca-v2')->user();

        // For supervisor, ensure they can only edit locations in their assigned plant
        if ($user->role === 'Supervisor' && $user->plant_id) {
            if ($location->plant_id != $user->plant_id) {
                return redirect()->route('sisca-v2.locations.index')
                    ->with('error', 'You can only edit locations in your assigned plant.');
            }
        }

        $plants = Plant::where('is_active', true)->get();
        $areas = Area::where('is_active', true)
            ->where('plant_id', $location->plant_id)
            ->get();

        return view('sisca-v2.locations.edit', compact('location', 'plants', 'areas'));
    }

    public function update(Request $request, Location $location)
    {
        $user = auth('sisca-v2')->user();

        // For supervisor, ensure they can only update locations in their assigned plant
        if ($user->role === 'Supervisor' && $user->plant_id) {
            if ($location->plant_id != $user->plant_id || $request->plant_id != $user->plant_id) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You can only update locations in your assigned plant.');
            }
        }

        $request->validate([
            'location_code' => 'required|string|max:100|unique:tm_locations_new,location_code,' . $location->id,
            'plant_id' => 'required|exists:tm_plants,id',
            'area_id' => 'required|exists:tm_areas,id',
            'pos' => 'nullable|string|max:255',
            'coordinate_x' => 'nullable|numeric|between:-999999.999999,999999.999999',
            'coordinate_y' => 'nullable|numeric|between:-999999.999999,999999.999999',
            'plant_coordinate_x' => 'nullable|numeric|between:-999999.999999,999999.999999',
            'plant_coordinate_y' => 'nullable|numeric|between:-999999.999999,999999.999999',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;

        try {
            $location->update($data);

            return redirect()->route('sisca-v2.locations.index')
                ->with('success', 'Location updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update location: ' . $e->getMessage());
        }
    }

    public function destroy(Location $location)
    {
        try {
            // Check if location has equipments
            if ($location->equipments()->count() > 0) {
                return redirect()->route('sisca-v2.locations.index')
                    ->with('error', 'Cannot delete location. It has associated equipments.');
            }

            $location->delete();

            return redirect()->route('sisca-v2.locations.index')
                ->with('success', 'Location deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('sisca-v2.locations.index')
                ->with('error', 'Failed to delete location: ' . $e->getMessage());
        }
    }

    // AJAX method to get areas by plant
    public function getAreasByPlant($plantId)
    {
        $areas = Area::where('plant_id', $plantId)
            ->where('is_active', true)
            ->get(['id', 'area_name', 'mapping_picture']);

        return response()->json($areas);
    }

    // AJAX method to get plant data
    public function getPlantData($plantId)
    {
        $plant = Plant::where('id', $plantId)
            ->where('is_active', true)
            ->first(['id', 'plant_name', 'plant_mapping_picture']);

        if (!$plant) {
            return response()->json(['error' => 'Plant not found'], 404);
        }

        return response()->json($plant);
    }
}
