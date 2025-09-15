<?php

namespace App\Http\Controllers\SiscaV2;

use Illuminate\Routing\Controller;
use App\Models\SiscaV2\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        // All authenticated users can access profile
        $this->middleware('role:Admin,Supervisor,Management,Pic');
    }

    /**
     * Show the user's profile
     */
    public function show()
    {
        $user = Auth::guard('sisca-v2')->user();
        return view('sisca-v2.profile.show', compact('user'));
    }

    /**
     * Show the form for editing the user's profile
     */
    public function edit()
    {
        $user = Auth::guard('sisca-v2')->user();

        // Only Admin can edit all fields, others can only change password
        $canEditProfile = $user->role === 'Admin';

        return view('sisca-v2.profile.edit', compact('user', 'canEditProfile'));
    }

    /**
     * Update the user's profile
     */
    public function update(Request $request)
    {
        $user = Auth::guard('sisca-v2')->user();

        // Validation rules based on role
        $rules = [
            'current_password' => 'required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ];

        // Only Admin can update profile information
        if ($user->role === 'Admin') {
            $rules = array_merge($rules, [
                'name' => 'required|string|max:255',
                'npk' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('tm_users_new', 'npk')->ignore($user->id)
                ],
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                    Rule::unique('tm_users_new', 'email')->ignore($user->id)
                ],
                'phone' => 'nullable|string|max:20',
                'department' => 'nullable|string|max:100',
                'position' => 'nullable|string|max:100',
                'role' => 'required|in:Admin,Supervisor,Management,PIC',
            ]);
        }

        $request->validate($rules);

        // Verify current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
        }

        // Update user data
        $updateData = [];

        // Only Admin can update profile fields
        if ($user->role === 'Admin') {
            $updateData = [
                'name' => $request->name,
                'npk' => $request->npk,
                'email' => $request->email,
                'phone' => $request->phone,
                'department' => $request->department,
                'position' => $request->position,
                'role' => $request->role,
            ];
        }

        // All users can change password
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('sisca-v2.profile.show')
            ->with('success', 'Profile updated successfully!');
    }

    /**
     * Show change password form
     */
    public function changePassword()
    {
        return view('sisca-v2.profile.change-password');
    }

    /**
     * Update password only
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::guard('sisca-v2')->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('sisca-v2.profile.show')
            ->with('success', 'Password updated successfully!');
    }
}
