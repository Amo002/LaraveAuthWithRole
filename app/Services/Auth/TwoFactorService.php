<?php

namespace App\Services\Auth;

use PragmaRX\Google2FAQRCode\Google2FA as Google2FAQrCode;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\RateLimiter;


class TwoFactorService
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    //  Generate QR Code and Secret (Store in session only)
    public function generateTwoFactor($user)
    {
        if ($user->two_factor_secret) {
            return ['success' => false, 'message' => '2FA is already enabled'];
        }

        $secret = $this->google2fa->generateSecretKey();
        $qrCode = $this->generateQrCode($user->email, $secret);
        $recoveryCodes = $this->generateRecoveryCodes();

        // Store in session (but not in DB yet)
        session([
            '2fa:secret' => $secret,
            '2fa:recovery_codes' => $recoveryCodes,
        ]);

        return [
            'success' => true,
            'message' => 'Scan the QR code and enter the code to enable 2FA.',
            'qrCode' => $qrCode,
            'secret' => $secret,
            'recoveryCodes' => $recoveryCodes,
        ];
    }

    //  Verify and Enable 2FA
    public function enableTwoFactor($user, $code)
    {
        $secret = session('2fa:secret');
        $recoveryCodes = session('2fa:recovery_codes');

        if (!$secret) {
            return ['success' => false, 'message' => 'Session expired. Please try again.'];
        }

        if ($this->google2fa->verifyKey($secret, $code)) {
            $user->forceFill([
                'two_factor_secret' => encrypt($secret),
                'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
                'two_factor_confirmed_at' => now(),
            ])->save();

            // Clear session after enabling
            session()->forget(['2fa:secret', '2fa:recovery_codes']);

            return ['success' => true, 'message' => '2FA enabled successfully!'];
        }

        return ['success' => false, 'message' => 'Invalid authentication code'];
    }

    //  Disable 2FA using Authenticator Code
    public function disableWithAuthenticator($user, $code)
    {
        if (!$user->two_factor_secret) {
            return ['success' => false, 'message' => '2FA is not enabled'];
        }

        $secret = decrypt($user->two_factor_secret);

        if (!$this->google2fa->verifyKey($secret, $code)) {
            return ['success' => false, 'message' => 'Invalid authenticator code'];
        }

        $this->clearTwoFactorSettings($user);

        return ['success' => true, 'message' => '2FA disabled successfully'];
    }

    //  Disable 2FA using Recovery Code
    public function disableWithRecoveryCode($user, $code)
    {
        if (!$user->two_factor_secret) {
            return ['success' => false, 'message' => '2FA is not enabled'];
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        if (!in_array($code, $recoveryCodes)) {
            return ['success' => false, 'message' => 'Invalid recovery code'];
        }

        $this->clearTwoFactorSettings($user);

        return ['success' => true, 'message' => '2FA disabled successfully using recovery code'];
    }

    //  Regenerate Recovery Codes (Using Authenticator Code)
    public function regenerateRecoveryCodes($user, $code)
    {
        if (!$user->two_factor_secret) {
            return ['success' => false, 'message' => '2FA is not enabled'];
        }

        if (!$this->google2fa->verifyKey(decrypt($user->two_factor_secret), $code)) {
            return ['success' => false, 'message' => 'Invalid authenticator code'];
        }

        $recoveryCodes = $this->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ])->save();

        return [
            'success' => true,
            'recoveryCodes' => $recoveryCodes,
            'message' => 'Recovery codes regenerated successfully!',
        ];
    }


    public function verifyWithAuthenticator($user, $code)
    {
        $key = $this->throttleKey($user, 'auth');

        // Check if too many attempts were made
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return ['success' => false, 'message' => "Too many attempts. Try again in $seconds seconds."];
        }

        if (!$user->two_factor_secret) {
            return ['success' => false, 'message' => '2FA is not enabled'];
        }

        $secret = decrypt($user->two_factor_secret);

        if ($this->google2fa->verifyKey($secret, $code)) {
            RateLimiter::clear($key); // Clear attempts on success
            return ['success' => true];
        }

        RateLimiter::hit($key, 60); // Increment attempts (expire after 60 seconds)

        return ['success' => false, 'message' => 'Invalid authenticator code'];
    }

    //  Verify with Recovery Code
    public function verifyWithRecoveryCode($user, $code)
    {
        $key = $this->throttleKey($user, 'recovery');

        // Check if too many attempts were made
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return ['success' => false, 'message' => "Too many attempts. Try again in $seconds seconds."];
        }

        if (!$user->two_factor_secret) {
            return ['success' => false, 'message' => '2FA is not enabled'];
        }

        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        if (!in_array($code, $recoveryCodes)) {
            RateLimiter::hit($key, 60); // Increment attempts (expire after 60 seconds)
            return ['success' => false, 'message' => 'Invalid recovery code'];
        }

        // Remove used recovery code
        $newCodes = array_diff($recoveryCodes, [$code]);

        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($newCodes)),
        ])->save();

        // If no recovery codes left, disable 2FA completely
        if (empty($newCodes)) {
            $user->forceFill([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ])->save();

            RateLimiter::clear($key);
            return ['success' => true, 'message' => '2FA disabled successfully (all recovery codes used)'];
        }

        RateLimiter::clear($key);
        return ['success' => true];
    }

    // Create a key for throttling based on user and type
    protected function throttleKey($user, $type)
    {
        return strtolower($user->email) . '|' . $type . '|' . request()->ip();
    }

    //  Clear 2FA Settings
    protected function clearTwoFactorSettings($user)
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    //  Generate QR Code
    protected function generateQrCode($email, $secret)
    {
        $google2fa = new Google2FAQrCode();

        return $google2fa->getQRCodeInline(
            config('app.name'),
            $email,
            $secret
        );
    }

    //  Generate Recovery Codes (Only Numbers, 6 Digits, No Zero)
    protected function generateRecoveryCodes()
    {
        return collect(range(1, 8))->map(function () {
            return collect(range(1, 9))
                ->shuffle()
                ->take(6)
                ->implode('');
        })->toArray();
    }
}
