<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * 
     * This middleware checks if the authenticated user has one or more required roles.
     * Usage examples:
     * - Route::get('/admin', [AdminController::class, 'index'])->middleware('role:Admin');
     * - Route::get('/staff', [StaffController::class, 'index'])->middleware('role:Admin,Employee');
     * - Route::group(['middleware' => 'role:Admin'], function () { ... });
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        $user = Auth::user();

        // If no roles specified, just check if user is authenticated
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has any of the required roles
        $hasRequiredRole = false;
        foreach ($roles as $role) {
            // Split comma-separated roles (e.g., "Admin,Employee")
            $roleList = array_map('trim', explode(',', $role));
            
            foreach ($roleList as $singleRole) {
                if ($user->hasRole($singleRole)) {
                    $hasRequiredRole = true;
                    break 2; // Break out of both loops
                }
            }
        }

        // If user doesn't have required role, deny access
        if (!$hasRequiredRole) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Insufficient permissions. Required role(s): ' . implode(', ', $roles)
                ], 403);
            }

            return redirect()->back()->with('error', 
                'Access denied. You need one of these roles: ' . implode(', ', $roles)
            );
        }

        // User has required role, proceed with request
        return $next($request);
    }
} 