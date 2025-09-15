<?php

namespace App\Http\Controllers\SiscaV2;

use Illuminate\Routing\Controller;
use App\Models\SiscaV2\PeriodCheck;
use Illuminate\Http\Request;

class PeriodCheckController extends Controller
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
        $query = PeriodCheck::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('period_check', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status'));
        }

        // Order by created_at desc for newest first
        $query->orderBy('created_at', 'desc');

        $periodChecks = $query->paginate(10)->appends($request->query());

        return view('sisca-v2.period-checks.index', compact('periodChecks'));
    }

    public function create()
    {
        return view('sisca-v2.period-checks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'period_check' => 'required|string|max:255|unique:tm_period_checks,period_check',
            'is_active' => 'boolean',
        ]);

        // Set default value for is_active if not provided
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;

        try {
            PeriodCheck::create($validated);

            return redirect()->route('sisca-v2.period-checks.index')
                ->with('success', 'Period Check created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create period check. Please try again.');
        }
    }

    public function show(PeriodCheck $period_check)
    {
        // Load relationships
        $period_check->load(['equipments']);

        return view('sisca-v2.period-checks.show', compact('period_check'));
    }

    public function edit(PeriodCheck $period_check)
    {
        return view('sisca-v2.period-checks.edit', compact('period_check'));
    }

    public function update(Request $request, PeriodCheck $period_check)
    {
        $validated = $request->validate([
            'period_check' => 'required|string|max:255|unique:tm_period_checks,period_check,' . $period_check->id,
            'is_active' => 'boolean',
        ]);

        // Set default value for is_active if not provided
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

        try {
            $period_check->update($validated);

            return redirect()->route('sisca-v2.period-checks.index')
                ->with('success', 'Period Check updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update period check. Please try again.');
        }
    }

    public function destroy(PeriodCheck $period_check)
    {
        try {
            // Check if period check has related data
            if ($period_check->equipments()->exists()) {
                return redirect()->route('sisca-v2.period-checks.index')
                    ->with('error', 'Cannot delete period check. It has related equipments.');
            }

            $period_check->delete();

            return redirect()->route('sisca-v2.period-checks.index')
                ->with('success', 'Period Check deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('sisca-v2.period-checks.index')
                ->with('error', 'Failed to delete period check. Please try again.');
        }
    }
}
