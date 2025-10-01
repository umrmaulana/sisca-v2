<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Company;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Admin', ['except' => ['index', 'show']]);
        $this->middleware('role:Admin,Supervisor,Management', ['only' => ['index', 'show']]);
    }

    public function index(Request $request)
    {
        $query = Area::with('company');

        // Search functionality
        if ($request->filled('search')) {
            $query->where('area_name', 'like', '%' . $request->search . '%')
                ->orWhereHas('company', function ($q) use ($request) {
                    $q->where('company_name', 'like', '%' . $request->search . '%');
                });
        }

        // Filter by company
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Order by created_at desc by default
        $query->orderBy('created_at', 'desc');

        $areas = $query->paginate(10)->appends(request()->query());
        $companies = Company::where('is_active', true)->orderBy('company_name')->get();

        return view('areas.index', compact('areas', 'companies'));
    }

    public function create()
    {
        $companies = Company::where('is_active', true)->orderBy('company_name')->get();
        return view('areas.create', compact('companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'area_name' => 'required|string|max:255|unique:tm_areas,area_name',
            'company_id' => 'required|exists:tm_companies,id',
            'mapping_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['area_name', 'company_id']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle mapping picture upload
        if ($request->hasFile('mapping_picture')) {
            $file = $request->file('mapping_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('templates/mapping', $filename, 'public');
            $data['mapping_picture'] = $path;
        }

        Area::create($data);

        return redirect()->route('areas.index')
            ->with('success', 'Area created successfully.');
    }

    public function show(Area $area)
    {
        $companies = Company::where('is_active', true)->orderBy('company_name')->get();
        $area->load(['company', 'locations']);
        return view('areas.show', compact('area', 'companies'));
    }

    public function edit(Area $area)
    {
        $companies = Company::where('is_active', true)->orderBy('company_name')->get();
        return view('areas.edit', compact('area', 'companies'));
    }

    public function update(Request $request, Area $area)
    {
        $request->validate([
            'area_name' => 'required|string|max:255|unique:tm_areas,area_name,' . $area->id,
            'company_id' => 'required|exists:tm_companies,id',
            'mapping_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['area_name', 'company_id']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle mapping picture upload
        if ($request->hasFile('mapping_picture')) {
            // Delete old file if exists
            if ($area->mapping_picture && \Storage::disk('public')->exists($area->mapping_picture)) {
                \Storage::disk('public')->delete($area->mapping_picture);
            }

            $file = $request->file('mapping_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('templates/mapping', $filename, 'public');
            $data['mapping_picture'] = $path;
        }

        $area->update($data);

        return redirect()->route('areas.index')
            ->with('success', 'Area updated successfully.');
    }

    public function destroy(Area $area)
    {
        // Check if area has related locations
        if ($area->locations()->count() > 0) {
            return redirect()->route('areas.index')
                ->with('error', 'Cannot delete area. It has related location records.');
        }

        // Delete mapping picture if exists
        if ($area->mapping_picture && \Storage::disk('public')->exists($area->mapping_picture)) {
            \Storage::disk('public')->delete($area->mapping_picture);
        }

        $area->delete();

        return redirect()->route('areas.index')
            ->with('success', 'Area deleted successfully.');
    }
}
