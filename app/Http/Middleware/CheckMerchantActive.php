<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMerchantActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Block super admins (merchant_id = 1) and inactive merchants
        if (!$user || $user->merchant_id === 1 || optional($user->merchant)->is_active === false) {
            return redirect()->route('dashboard')->with('error', 'You are not allowed to access merchant features.');
        }

        return $next($request);
    }
}
