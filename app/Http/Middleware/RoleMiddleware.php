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
        // Cek apakah pengguna sudah login dengan guard  dan memiliki role yang sesuai
        if (Auth::guard()->check() && in_array(Auth::guard()->user()->role, $roles)) {
            return $next($request);
        }

        // Jika tidak, redirect ke halaman yang sesuai atau tampilkan error
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return redirect()->route('no-access')->with('error', 'You do not have permission to access this resource.');
    }
}
