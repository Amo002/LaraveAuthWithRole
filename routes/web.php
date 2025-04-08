<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Main\HomeController;
use App\Http\Controllers\ProfileController;

//  Public Routes (No Cache)
Route::middleware(['nocache'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

//  Guest Routes (For Unauthenticated Users)
Route::middleware(['guest', 'nocache'])->group(function () {

    // Login Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Two Factor Challenge
    Route::get('/2fa-challenge', [TwoFactorController::class, 'showChallenge'])->name('2fa.challenge');
    Route::post('/2fa/verify-auth', [TwoFactorController::class, 'verifyWithAuthenticator'])->name('2fa.verify.auth');
    Route::post('/2fa/verify-recovery', [TwoFactorController::class, 'verifyWithRecoveryCode'])->name('2fa.verify.recovery');

    // Registration Routes
    Route::get('/register/invite/{payload}', [RegisterController::class, 'showInviteForm'])->name('register.invite');
    Route::post('/register/complete', [RegisterController::class, 'completeRegistration'])->name('register.complete');
});

//  Authenticated Routes (For Authenticated Users)
Route::middleware(['auth', 'nocache', 'teams'])->group(function () {

    //  Profile Route
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    //  Two Factor Authentication Routes
    Route::prefix('2fa')->group(function () {
        Route::post('/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
        Route::post('/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
        Route::post('/disable/auth', [TwoFactorController::class, 'disableWithAuthenticator'])->name('2fa.disable.auth');
        Route::post('/disable/recovery-code', [TwoFactorController::class, 'disableWithRecoveryCode'])->name('2fa.disable.recovery');
        Route::post('/regenerate-recovery', [TwoFactorController::class, 'regenerate'])->name('2fa.recovery');
    });

    //  Dashboard Routes
    Route::middleware('role:admin,merchant_admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    //  Logout Route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    //  Split Admin & Merchant Routes
    require __DIR__.'/admin.php';
    require __DIR__.'/merchant.php';
});
