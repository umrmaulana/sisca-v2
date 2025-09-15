<?php

namespace App\Http\Controllers\SiscaV2;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('sisca-v2.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'npk' => 'required',
            'password' => 'required'
        ]);

        if (Auth::guard('sisca-v2')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('sisca-v2.dashboard'));
        }

        return back()->with('loginError', 'NPK atau Password salah!');
    }

    public function logout(Request $request)
    {
        Auth::guard('sisca-v2')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('sisca-v2.login')->with('message', 'Logout berhasil!');
    }
}
