<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticate
{
    public function handle(Request $request, Closure $next): Response
    {
   

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must log in first.');
        }

        return $next($request);
    }
}
