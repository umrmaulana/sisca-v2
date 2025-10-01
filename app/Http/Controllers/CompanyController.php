<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
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
        $query = Company::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('company_name', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status'));
        }

        // Order by created_at desc for newest first
        $query->orderBy('created_at', 'desc');

        $companies = $query->paginate(10)->appends($request->query());

        return view('companies.index', compact('companies'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255|unique:tm_companies,company_name',
            'company_description' => 'nullable|string|max:1000',
            'company_mapping_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'is_active' => 'boolean',
        ]);

        // Handle company mapping picture upload
        if ($request->hasFile('company_mapping_picture')) {
            $file = $request->file('company_mapping_picture');
            $filename = time() . '_company_' . $file->getClientOriginalName();
            $path = $file->storeAs('templates/mapping', $filename, 'public');
            $validated['company_mapping_picture'] = $path;
        }

        // Set default value for is_active if not provided
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;

        try {
            Company::create($validated);

            return redirect()->route('companies.index')
                ->with('success', 'Company created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create company. Please try again.');
        }
    }

    public function show(Company $company)
    {
        // Load relationships
        $company->load(['locations', 'users']);

        return view('companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255|unique:tm_companies,company_name,' . $company->id,
            'company_description' => 'nullable|string|max:1000',
            'company_mapping_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'is_active' => 'boolean',
        ]);

        // Handle company mapping picture upload
        if ($request->hasFile('company_mapping_picture')) {
            // Delete old file if exists
            if ($company->company_mapping_picture && \Storage::disk('public')->exists($company->company_mapping_picture)) {
                \Storage::disk('public')->delete($company->company_mapping_picture);
            }

            $file = $request->file('company_mapping_picture');
            $filename = time() . '_company_' . $file->getClientOriginalName();
            $path = $file->storeAs('templates/mapping', $filename, 'public');
            $validated['company_mapping_picture'] = $path;
        }

        // Set default value for is_active if not provided
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

        try {
            $company->update($validated);

            return redirect()->route('companies.index')
                ->with('success', 'Company updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update company. Please try again.');
        }
    }

    public function destroy(Company $company)
    {
        try {
            // Check if company has related data
            if ($company->locations()->exists() || $company->users()->exists()) {
                return redirect()->route('companies.index')
                    ->with('error', 'Cannot delete company. It has related locations or users.');
            }

            // Delete company mapping picture if exists
            if ($company->company_mapping_picture && \Storage::disk('public')->exists($company->company_mapping_picture)) {
                \Storage::disk('public')->delete($company->company_mapping_picture);
            }

            $company->delete();

            return redirect()->route('companies.index')
                ->with('success', 'Company deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('companies.index')
                ->with('error', 'Failed to delete company. Please try again.');
        }
    }
}
