<?php

namespace App\Services;

class ProfileService
{
    // Get Two Factor Status (display only)
    public function getTwoFactorStatus($user)
    {
        return [
            'enabled' => !empty($user->two_factor_secret),
            'confirmed_at' => $user->two_factor_confirmed_at,
            'pending_setup' => session()->has('2fa:secret'),
        ];
    }
}
