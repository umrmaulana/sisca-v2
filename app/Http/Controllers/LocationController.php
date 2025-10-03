<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Company;
use App\Models\Area;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct()
    {
        // Only Admin and Supervisor can create, update, delete
        $this->middleware('role:Admin')->except(['index', 'show', 'getAreasByCompany', 'getAreasByCompanyForFilter', 'getCompanyData']);
        // All roles can view (index, show) and use AJAX helper
        $this->middleware('role:Admin,Supervisor,Management')->only(['index', 'show', 'getAreasByCompany', 'getAreasByCompanyForFilter', 'getCompanyData']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Location::with(['company', 'area'])
            ->orderBy('created_at', 'desc');

        // Apply company filter for non-admin users
        if ($user->role === 'Supervisor' && $user->company_id) {
            $query->where('company_id', $user->company_id);
        }

        // Filter by company (for Admin/Management when specified)
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by area
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        // Search by location code
        if ($request->filled('search')) {
            $query->where('location_code', 'like', '%' . $request->search . '%')
                ->orwhere('pos', 'like', '%' . $request->search . '%');
        }

        $locations = $query->paginate(10)->appends($request->query());

        // Companies dropdown (only for Admin/Management)
        $companies = collect();
        if ($user->role === 'Admin' || $user->role === 'Management') {
            $companies = Company::where('is_active', true)->get();
        } elseif ($user->role === 'Supervisor' && $user->company_id) {
            $companies = Company::where('id', $user->company_id)->where('is_active', true)->get();
        }

        // Areas dropdown (filtered by accessible companies and selected company)
        $areas = collect();
        if ($user->role === 'Admin' || $user->role === 'Management') {
            if ($request->filled('company_id')) {
                $areas = Area::where('company_id', $request->company_id)->where('is_active', true)->get();
            } else {
                $areas = Area::where('is_active', true)->get();
            }
        } elseif ($user->role === 'Supervisor' && $user->company_id) {
            $areas = Area::where('company_id', $user->company_id)->where('is_active', true)->get();
        }

        return view('locations.index', compact('locations', 'companies', 'areas'));
    }

    public function create(Request $request)
    {
        // $user = auth()->user();

        // // For supervisor, auto-select their assigned company
        // if ($user->role === 'Supervisor' && $user->company_id) {
        //     $companies = Company::where('id', $user->company_id)
        //         ->where('is_active', true)
        //         ->get();

        //     // Auto-load areas for supervisor's company
        //     $areas = Area::where('company_id', $user->company_id)
        //         ->where('is_active', true)
        //         ->get();
        // } else {
        //     // For Admin/Management - show all companies
        // }
        $companies = Company::where('is_active', true)->get();

        // Get areas based on selected company
        $areas = collect();
        if ($request->filled('company_id')) {
            $areas = Area::where('is_active', true)
                ->where('company_id', $request->company_id)
                ->get();
        }

        return view('locations.create', compact('companies', 'areas'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'location_name' => 'required|string|max:100|unique:tm_locations_new,location_code',
            'pos' => 'nullable|string|max:255',
            'company_id' => 'required|exists:tm_companies,id',
            'area_id' => 'required|exists:tm_areas,id',
            'coordinate_x' => 'nullable|numeric|between:-999999.999999,999999.999999',
            'coordinate_y' => 'nullable|numeric|between:-999999.999999,999999.999999',
            'company_coordinate_x' => 'nullable|numeric|between:-999999.999999,999999.999999',
            'company_coordinate_y' => 'nullable|numeric|between:-999999.999999,999999.999999',
        ], [
            'location_name.unique' => 'The location code has already been taken.',
        ]);

        // For supervisor, ensure they can only create locations in their assigned company
        if ($user->role === 'Supervisor' && $user->company_id) {
            if ($request->company_id != $user->company_id) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You can only create locations in your assigned company.');
            }
        }

        // Validate area belongs to the selected company
        $area = Area::where('id', $request->area_id)
            ->where('company_id', $request->company_id)
            ->first();

        if (!$area) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Selected area does not belong to the selected company.');
        }

        // Prepare data for insertion
        $data = [
            'location_code' => $request->location_name, // Map location_name to location_code
            'company_id' => $request->company_id,
            'area_id' => $request->area_id,
            'coordinate_x' => $request->coordinate_x,
            'coordinate_y' => $request->coordinate_y,
            'company_coordinate_x' => $request->company_coordinate_x,
            'company_coordinate_y' => $request->company_coordinate_y,
            'pos' => $request->pos ?? null,
            'is_active' => $request->has('is_active') ? true : false,
        ];

        try {
            \Log::info('Creating location with data:', $data);
            $location = Location::create($data);
            \Log::info('Location created successfully with ID: ' . $location->id);

            return redirect()->route('locations.index')
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
        $location->load(['company', 'area', 'equipments']);
        return view('locations.show', compact('location'));
    }

    public function edit(Location $location)
    {
        $user = auth()->user();

        // For supervisor, ensure they can only edit locations in their assigned company
        if ($user->role === 'Supervisor' && $user->company_id) {
            if ($location->company_id != $user->company_id) {
                return redirect()->route('locations.index')
                    ->with('error', 'You can only edit locations in your assigned company.');
            }
        }

        $companies = Company::where('is_active', true)->get();
        $areas = Area::where('is_active', true)
            ->where('company_id', $location->company_id)
            ->get();

        return view('locations.edit', compact('location', 'companies', 'areas'));
    }

    public function update(Request $request, Location $location)
    {
        $user = auth()->user();

        // For supervisor, ensure they can only update locations in their assigned company
        if ($user->role === 'Supervisor' && $user->company_id) {
            if ($location->company_id != $user->company_id || $request->company_id != $user->company_id) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'You can only update locations in your assigned company.');
            }
        }

        $request->validate([
            'location_code' => 'required|string|max:100|unique:tm_locations_new,location_code,' . $location->id,
            'company_id' => 'required|exists:tm_companies,id',
            'area_id' => 'required|exists:tm_areas,id',
            'pos' => 'nullable|string|max:255',
            'coordinate_x' => 'nullable|numeric|between:-999999.999999,999999.999999',
            'coordinate_y' => 'nullable|numeric|between:-999999.999999,999999.999999',
            'company_coordinate_x' => 'nullable|numeric|between:-999999.999999,999999.999999',
            'company_coordinate_y' => 'nullable|numeric|between:-999999.999999,999999.999999',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? true : false;

        try {
            $location->update($data);

            return redirect()->route('locations.index')
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
                return redirect()->route('locations.index')
                    ->with('error', 'Cannot delete location. It has associated equipments.');
            }

            $location->delete();

            return redirect()->route('locations.index')
                ->with('success', 'Location deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('locations.index')
                ->with('error', 'Failed to delete location: ' . $e->getMessage());
        }
    }

    // AJAX method to get areas by company
    public function getAreasByCompany($companyId)
    {
        $areas = Area::where('company_id', $companyId)
            ->where('is_active', true)
            ->get(['id', 'area_name', 'mapping_picture']);

        return response()->json($areas);
    }

    // AJAX method for area filtering (compatible with location filter)
    public function getAreasByCompanyForFilter(Request $request)
    {
        $companyId = $request->get('company_id');

        if (!$companyId) {
            return response()->json(['areas' => []]);
        }

        $areas = Area::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('area_name')
            ->get(['id', 'area_name']);

        return response()->json(['areas' => $areas]);
    }

    // AJAX method to get company data
    public function getCompanyData($companyId)
    {
        $company = Company::where('id', $companyId)
            ->where('is_active', true)
            ->first(['id', 'company_name', 'company_mapping_picture']);

        if (!$company) {
            return response()->json(['error' => 'Company not found'], 404);
        }

        return response()->json($company);
    }
}

