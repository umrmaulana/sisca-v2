<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectToRoleDashboard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Jika akses ke /dashboard, arahkan berdasarkan role
        if ($request->is('dashboard') || $request->is('/')) {
            switch ($user->role) {
                case 'GA':
                    return redirect()->route('ga.stock'); // route untuk monitoring stock
                case 'Admin':
                case 'User':
                case 'MTE':
                    return $next($request); // lanjut ke dashboard grafik
                default:
                    return redirect()->route('no-access');
            }
        }
        return $next($request);
    }
}
