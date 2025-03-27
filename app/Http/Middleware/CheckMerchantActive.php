<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMerchantActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->merchant_id && optional($user->merchant)->is_active === false) {
            // Redirect them away if trying to access merchant stuff
            return redirect()->route('dashboard')->with('error', 'Your merchant is disabled.');
        }

        return $next($request);
    }
}
