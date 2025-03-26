<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\PermissionRegistrar;

class TeamsPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        $permissions = app(PermissionRegistrar::class);

        if ($user) {
            if ($user->merchant_id) {
                // Set team context for merchants
                $permissions->setPermissionsTeamId($user->merchant_id);
            } else {
                // 🔥 Reset to global (null) if super admin
                $permissions->setPermissionsTeamId(null);
            }
        }

        return $next($request);
    }
}
