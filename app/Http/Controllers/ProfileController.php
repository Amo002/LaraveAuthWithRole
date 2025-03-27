<?php

namespace App\Http\Controllers;

use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index(Request $request)
    {
        $status = $this->profileService->getTwoFactorStatus($request->user());

        // ✅ Retrieve QR code and recovery codes from session
        $qrCode = session('2fa:qrCode');
        $secret = session('2fa:secret');
        $recoveryCodes = session('2fa:recoveryCodes', []);

        return view('profile', compact('status', 'qrCode', 'secret', 'recoveryCodes'));
    }
}
