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
        // Global or Merchant Admins → Dashboard
        if ($user->hasRole(['admin', 'yahala_admin', 'zerogame_admin'])) {
            return redirect()->route('dashboard')
                ->with('status', 'success')
                ->with('message', 'Welcome back, Admin!');
        }

        // Merchant Editors or Users → Merchant Users Page
        if ($user->hasAnyRole(['yahala_editor', 'yahala_user', 'zerogame_editor', 'zerogame_user'])) {
            return redirect()->route('merchant.users.index')
                ->with('status', 'success')
                ->with('message', 'Welcome back to your merchant panel!');
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
