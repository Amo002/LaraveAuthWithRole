<?php

namespace App\Services\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->two_factor_secret) {
                session(['2fa:user:id' => $user->id]);
                Auth::logout();
                return redirect()->route('2fa.challenge');
            }

            return $this->redirectAfterLogin($user);
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }

    protected function redirectAfterLogin($user)
    {
        // Global Admins → Dashboard
        if ($user->hasRole(['merchant_admin', 'admin'])) {
            return redirect()->route('dashboard')
                ->with('status', 'success')
                ->with('message', 'Welcome back, Admin!');
        }



        // Default fallback → Home
        return redirect()->route('home')
            ->with('status', 'success')
            ->with('message', 'Welcome back!');
    }





    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }
}
