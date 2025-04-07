<?php

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

        // Set current team context for Spatie permissions
        if ($user->merchant_id) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($user->merchant_id);
        }

        // Allow if user is global admin
        if ($user->hasRole(['merchant_admin', 'admin'])) {
            return $next($request);
        }



        return redirect()->route('home')->with('error', 'Unauthorized access.');
    }
}
