<?php

namespace App\Http\Middleware;

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\PermissionRegistrar;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must log in first.');
        }

        // Set current team context only if user is team-scoped
        if ($user->merchant_id) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($user->merchant_id);
        }

        // Check role
        if ($user->hasRole('admin') || $user->hasAnyRole($roles)) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'Unauthorized access.');
    }
}
