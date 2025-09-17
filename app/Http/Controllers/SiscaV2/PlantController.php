<?php

namespace App\Http\Controllers\SiscaV2;

use Illuminate\Routing\Controller;
use App\Models\SiscaV2\Plant;
use Illuminate\Http\Request;

class PlantController extends Controller
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
        $query = Plant::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('plant_name', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status'));
        }

        // Order by created_at desc for newest first
        $query->orderBy('created_at', 'desc');

        $plants = $query->paginate(10)->appends($request->query());

        return view('sisca-v2.plants.index', compact('plants'));
    }

    public function create()
    {
        return view('sisca-v2.plants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plant_name' => 'required|string|max:255|unique:tm_plants,plant_name',
            'plant_description' => 'nullable|string|max:1000',
            'plant_mapping_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'is_active' => 'boolean',
        ]);

        // Handle plant mapping picture upload
        if ($request->hasFile('plant_mapping_picture')) {
            $file = $request->file('plant_mapping_picture');
            $filename = time() . '_plant_' . $file->getClientOriginalName();
            $path = $file->storeAs('sisca-v2/templates/mapping', $filename, 'public');
            $validated['plant_mapping_picture'] = $path;
        }

        // Set default value for is_active if not provided
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;

        try {
            Plant::create($validated);

            return redirect()->route('sisca-v2.plants.index')
                ->with('success', 'Plant created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create plant. Please try again.');
        }
    }

    public function show(Plant $plant)
    {
        // Load relationships
        $plant->load(['locations', 'users']);

        return view('sisca-v2.plants.show', compact('plant'));
    }

    public function edit(Plant $plant)
    {
        return view('sisca-v2.plants.edit', compact('plant'));
    }

    public function update(Request $request, Plant $plant)
    {
        $validated = $request->validate([
            'plant_name' => 'required|string|max:255|unique:tm_plants,plant_name,' . $plant->id,
            'plant_description' => 'nullable|string|max:1000',
            'plant_mapping_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'is_active' => 'boolean',
        ]);

        // Handle plant mapping picture upload
        if ($request->hasFile('plant_mapping_picture')) {
            // Delete old file if exists
            if ($plant->plant_mapping_picture && \Storage::disk('public')->exists($plant->plant_mapping_picture)) {
                \Storage::disk('public')->delete($plant->plant_mapping_picture);
            }

            $file = $request->file('plant_mapping_picture');
            $filename = time() . '_plant_' . $file->getClientOriginalName();
            $path = $file->storeAs('sisca-v2/templates/mapping', $filename, 'public');
            $validated['plant_mapping_picture'] = $path;
        }

        // Set default value for is_active if not provided
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

        try {
            $plant->update($validated);

            return redirect()->route('sisca-v2.plants.index')
                ->with('success', 'Plant updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update plant. Please try again.');
        }
    }

    public function destroy(Plant $plant)
    {
        try {
            // Check if plant has related data
            if ($plant->locations()->exists() || $plant->users()->exists()) {
                return redirect()->route('sisca-v2.plants.index')
                    ->with('error', 'Cannot delete plant. It has related locations or users.');
            }

            // Delete plant mapping picture if exists
            if ($plant->plant_mapping_picture && \Storage::disk('public')->exists($plant->plant_mapping_picture)) {
                \Storage::disk('public')->delete($plant->plant_mapping_picture);
            }

            $plant->delete();

            return redirect()->route('sisca-v2.plants.index')
                ->with('success', 'Plant deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('sisca-v2.plants.index')
                ->with('error', 'Failed to delete plant. Please try again.');
        }
    }
}
