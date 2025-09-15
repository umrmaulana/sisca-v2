<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek apakah pengguna sudah login dengan guard sisca-v2 dan memiliki role yang sesuai
        if (Auth::guard('sisca-v2')->check() && in_array(Auth::guard('sisca-v2')->user()->role, $roles)) {
            return $next($request);
        }

        // Jika tidak, redirect ke halaman yang sesuai atau tampilkan error
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return redirect()->route('sisca-v2.no-access')->with('error', 'You do not have permission to access this resource.');
    }
}
