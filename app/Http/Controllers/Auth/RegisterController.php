<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\RegisterService;

class RegisterController extends Controller
{
    protected $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    public function showInviteForm($encryptedPayload)
    {
        $result = $this->registerService->decryptInvite($encryptedPayload);
    
        if (!$result['status']) {
            return redirect()->route('login')->with([
                'status' => 'error',
                'message' => $result['message']
            ]);
        }
    
        $validateResult = $this->registerService->validateInvite($result['email'], $result['id']);
    
        if (!$validateResult['status']) {
            return redirect()->route('login')->with([
                'status' => 'error',
                'message' => $validateResult['message']
            ]);
        }
    
        return view('auth.register', [
            'email' => $result['email'],
            'id' => $result['id']
        ]);
    }
    



    public function completeRegistration(RegisterRequest $request)
    {
        $result = $this->registerService->completeRegistration($request->validated());

        if (!$result['status']) {
            return redirect()->route('register.invite', [
                'payload' => encrypt([
                    'email' => $request->email,
                    'id' => $request->id,
                ])
            ])->with([
                'status' => 'error',
                'message' => $result['message']
            ]);
        }

        return redirect()->route('home')->with([
            'status' => 'success',
            'message' => $result['message']
        ]);
    }
}
