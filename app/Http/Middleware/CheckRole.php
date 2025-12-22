<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // If no roles specified, allow access (fallback to other middleware)
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user is authenticated
        if (!$request->user()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this resource.');
        }

        // Check role assignment
        $hasRole = false;
        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            // Log unauthorized access attempt
            Log::warning('Unauthorized access attempt', [
                'user_id' => $request->user()->id,
                'roles_required' => $roles,
                'user_roles' => $request->user()->roles->pluck('name')->toArray(),
                'url' => $request->url(),
                'ip' => $request->ip(),
            ]);

            // Return 403 Forbidden
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }

    /**
     * Get the priority of middleware execution.
     */
    public function getPriority(): int
    {
        return 10; // Higher priority than auth middleware
    }
}