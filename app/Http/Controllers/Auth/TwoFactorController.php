<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    protected $twoFactorService;

    public function __construct(TwoFactorService $twoFactorService)
    {
        $this->twoFactorService = $twoFactorService;
    }

    //  Generate QR Code and Secret (but don't enable yet)
    public function setup(Request $request)
    {
        $result = $this->twoFactorService->generateTwoFactor($request->user());

        if ($result['success']) {
            session([
                '2fa:qrCode' => $result['qrCode'],
                '2fa:secret' => $result['secret'],
                '2fa:recoveryCodes' => $result['recoveryCodes'],
            ]);
        }

        return redirect()->route('profile')->with(
            $result['success'] ? 'success' : 'error',
            $result['message'] ?? 'An error occurred'
        );
    }

    //  Verify Code and Enable 2FA
    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $result = $this->twoFactorService->enableTwoFactor($request->user(), $request->code);

        if ($result['success']) {
            session()->forget(['2fa:qrCode', '2fa:secret', '2fa:recoveryCodes']);
        }

        return redirect()->route('profile')->with(
            $result['success'] ? 'success' : 'error',
            $result['message'] ?? 'An error occurred'
        );
    }

    //  Disable 2FA using Authenticator Code
    public function disableWithAuthenticator(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $result = $this->twoFactorService->disableWithAuthenticator(
            $request->user(),
            $request->code
        );

        if ($result['success']) {
            session()->forget(['2fa:qrCode', '2fa:secret', '2fa:recoveryCodes']);
        }

        return redirect()->route('profile')->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    //  Disable 2FA using Recovery Code
    public function disableWithRecoveryCode(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $result = $this->twoFactorService->disableWithRecoveryCode(
            $request->user(),
            $request->code
        );

        if ($result['success']) {
            session()->forget(['2fa:qrCode', '2fa:secret', '2fa:recoveryCodes']);
        }

        return redirect()->route('profile')->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    //  Regenerate Recovery Codes (Using Authenticator Code Only)
    public function regenerate(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $result = $this->twoFactorService->regenerateRecoveryCodes(
            $request->user(),
            $request->code
        );

        if ($result['success']) {
            session()->put('2fa:recoveryCodes', $result['recoveryCodes']);
        }

        return redirect()->route('profile')->with(
            $result['success'] ? 'success' : 'error',
            $result['message'] ?? 'An error occurred'
        );
    }


    //  Show 2FA Challenge (After Login)
    public function showChallenge()
    {
        if (!session('2fa:user:id')) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        return view('auth.two-factor-challenge');
    }

    public function verifyWithAuthenticator(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = session('2fa:user:id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = \App\Models\User::find($userId);

        $result = $this->twoFactorService->verifyWithAuthenticator(
            $user,
            $request->code
        );

        if ($result['success']) {
            session()->forget('2fa:user:id');

            Auth::login($user);

            return $this->redirectAfterLogin($user);
        }

        return back()->with('error', $result['message']);
    }

    //  Verify with Recovery Code
    public function verifyWithRecoveryCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $userId = session('2fa:user:id');
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        $user = \App\Models\User::find($userId);

        $result = $this->twoFactorService->verifyWithRecoveryCode(
            $user,
            $request->code
        );

        if ($result['success']) {
            session()->forget('2fa:user:id');

            Auth::login($user);

            return $this->redirectAfterLogin($user);
        }

        return back()->with('error', $result['message']);
    }
    //  Redirect After Login (Role-Based)
    protected function redirectAfterLogin($user)
    {
        $route = match (true) {
            $user->hasRole('admin') => 'dashboard',
            $user->hasRole('merchant') => 'dashboard',
            default => 'home',
        };

        return redirect()->route($route)->with('success', 'Welcome back!');
    }
}
