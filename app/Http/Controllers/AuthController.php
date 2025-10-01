<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'npk' => 'required',
            'password' => 'required'
        ]);

        if (Auth::guard()->attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect to landing page after successful login
            return redirect()->intended(route('index'));
        }

        return back()->with('loginError', 'NPK atau Password salah!');

    }

    public function logout(Request $request)
    {
        Auth::guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('message', 'Logout berhasil!');
    }
}
