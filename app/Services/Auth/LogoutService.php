<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutService
{
    public function logout(Request $request)
    {
        Auth::logout(); // Logs out the user

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate CSRF token to prevent session fixation attacks
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}
