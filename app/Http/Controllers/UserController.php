<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        // Only Admin can manage users
        $this->middleware('role:Admin');
        // $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('npk', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->get('role'));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->get('status'));
        }

        // Company filter
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->get('company_id'));
        }

        // Order by created_at desc for newest first
        $query->orderBy('created_at', 'desc');

        $users = $query->with('company')->paginate(10)->appends($request->query());

        // Get data for filters
        $companies = Company::where('is_active', true)->get();
        $roles = ['Admin', 'Supervisor', 'Management', 'User', 'Pic'];

        return view('users.index', compact('users', 'companies', 'roles'));
    }

    public function create()
    {
        $companies = Company::where('is_active', true)->get();
        $roles = ['Admin', 'Supervisor', 'Management', 'User', 'Pic'];
        return view('users.create', compact('companies', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'npk' => 'required|string|max:20|unique:users,npk',
            'role' => 'required|in:Admin,Supervisor,Management,User,Pic',
            'company_id' => 'nullable|exists:tm_companies,id',
            'password' => 'required|string|min:8',
            'is_active' => 'boolean',
        ]);

        // Set default value for is_active if not provided
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;
        $validated['password'] = Hash::make($request->password);

        try {
            User::create($validated);

            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create user. Please try again.');
        }
    }

    public function show(User $user)
    {
        // Load relationships if they exist
        $user->load('company');

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $companies = Company::where('is_active', true)->get();
        $roles = ['Admin', 'Supervisor', 'Management', 'User', 'Pic'];
        return view('users.edit', compact('user', 'companies', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'npk' => 'required|string|max:20|unique:users,npk,' . $user->id,
            'role' => 'required|in:Admin,Supervisor,Management,User,Pic',
            'company_id' => 'nullable|exists:tm_companies,id',
            'password' => 'nullable|string|min:8',
            'is_active' => 'boolean',
        ]);

        // Set default value for is_active if not provided
        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

        // Handle password update
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        try {
            $user->update($validated);

            return redirect()->route('users.index')
                ->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update user. Please try again.');
        }
    }

    public function destroy(User $user)
    {
        try {
            // Check if user is trying to delete themselves
            if (auth()->id() === $user->id) {
                return redirect()->route('users.index')
                    ->with('error', 'You cannot delete your own account.');
            }

            $user->delete();

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Failed to delete user. Please try again.');
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(User $user)
    {
        try {
            // Only admin can reset passwords
            if (auth()->user()->role !== 'Admin') {
                return redirect()->back()
                    ->with('error', 'You are not authorized to reset passwords.');
            }

            // Generate temporary password
            $temporaryPassword = 'Aisin123';

            $user->update([
                'password' => Hash::make($temporaryPassword),
            ]);

            // For now, we'll store it in session to show to admin
            session()->flash('temporary_password', $temporaryPassword);

            return redirect()->back()
                ->with('success', "Password reset successfully. Temporary password: {$temporaryPassword}");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reset password: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user, Request $request)
    {
        try {
            // Only admin can toggle status
            if (auth()->user()->role !== 'Admin') {
                return redirect()->back()
                    ->with('error', 'You are not authorized to change user status.');
            }

            // Prevent deactivating own account
            if (auth()->id() === $user->id && !$request->is_active) {
                return redirect()->back()
                    ->with('error', 'You cannot deactivate your own account.');
            }

            $user->update([
                'is_active' => $request->is_active
            ]);

            $status = $request->is_active ? 'activated' : 'deactivated';

            return redirect()->back()
                ->with('success', "User {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update user status: ' . $e->getMessage());
        }
    }
}
