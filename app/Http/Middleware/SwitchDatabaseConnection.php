<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\DatabaseConnectionManager;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SwitchDatabaseConnection
{
    /**
     * Handle an incoming request and switch to role-appropriate database connection.
     *
     * This middleware automatically switches the database connection based on the 
     * authenticated user's role, providing database-level security in addition
     * to application-level authorization.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Switch to appropriate database connection based on user role
            $connectionUsed = DatabaseConnectionManager::switchToUserRoleConnection();
            
            // Add connection info to request for debugging/logging
            $request->attributes->set('db_connection', $connectionUsed);
            
            // Log connection switch for audit purposes
            if (auth()->check()) {
                $user = auth()->user();
                $userRoles = $user->roles->pluck('name')->toArray();
                
                Log::info('Database connection switched', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_roles' => $userRoles,
                    'connection' => $connectionUsed,
                    'route' => $request->route()?->getName(),
                    'ip' => $request->ip(),
                ]);
            } else {
                Log::info('Database connection for unauthenticated request', [
                    'connection' => $connectionUsed,
                    'route' => $request->route()?->getName(),
                    'ip' => $request->ip(),
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to switch database connection: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'route' => $request->route()?->getName(),
                'exception' => $e->getTrace()
            ]);

            // Continue with default connection on error
        }

        $response = $next($request);

        // Add database connection info to response headers (for debugging in development)
        if (config('app.debug')) {
            $response->headers->set('X-Database-Connection', DatabaseConnectionManager::getCurrentConnection());
        }

        return $response;
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    public function terminate(Request $request, Response $response): void
    {
        // Clean up connection if needed
        // DatabaseConnectionManager::resetToDefaultConnection();
    }
} 