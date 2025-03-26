<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\LoginService;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        return $this->loginService->authenticate($request);
    }


    public function logout(Request $request)
    {
        return $this->loginService->logout($request);
    }
}
