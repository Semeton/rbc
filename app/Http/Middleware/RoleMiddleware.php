<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admin has access to everything
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (! in_array($user->role, $roles)) {
            $requiredRoles = implode(', ', array_map(fn($role) => ucfirst(str_replace('_', ' ', $role)), $roles));
            abort(403, "You need one of the following roles to access this resource: {$requiredRoles}. Your current role is: " . ucfirst(str_replace('_', ' ', $user->role)));
        }

        return $next($request);
    }
}
