<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must log in first.');
        }

        if ($user->hasRole('admin')) {
            return $next($request);
        }

        if ($user->hasAnyRole($roles)) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'Unauthorized access.');
    }
}
