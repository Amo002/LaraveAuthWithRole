<?php

use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\InviteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Main\HomeController;
<<<<<<< HEAD
use App\Http\Controllers\Merchant\UserController as MerchantUserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ===============================
//  Public Routes (No Cache)
// ===============================
=======
use App\Http\Controllers\MerchantUserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

//  Public Routes (No Cache)
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
Route::middleware(['nocache'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

<<<<<<< HEAD
// ===============================
//  Guest Routes (For Unauthenticated Users)
// ===============================
=======
//  Guest Routes (For Unauthenticated Users)
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
Route::middleware(['guest', 'nocache'])->group(function () {

    // Login Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Two Factor Challenge
    Route::get('/2fa-challenge', [TwoFactorController::class, 'showChallenge'])->name('2fa.challenge');
<<<<<<< HEAD
    Route::post('/2fa/verify-auth', [TwoFactorController::class, 'verifyWithAuthenticator'])->name('2fa.verify.auth');
    Route::post('/2fa/verify-recovery', [TwoFactorController::class, 'verifyWithRecoveryCode'])->name('2fa.verify.recovery');

=======
    Route::post('/2fa-verify-auth', [TwoFactorController::class, 'verifyWithAuthenticator'])->name('2fa.verify.auth');
    Route::post('/2fa-verify-recovery', [TwoFactorController::class, 'verifyWithRecoveryCode'])->name('2fa.verify.recovery');
    
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
    // Registration Routes
    Route::get('/register/invite/{payload}', [RegisterController::class, 'showInviteForm'])->name('register.invite');
    Route::post('/register/complete', [RegisterController::class, 'completeRegistration'])->name('register.complete');
});

<<<<<<< HEAD
// ===============================
//  Authenticated Routes (For Authenticated Users)
// ===============================
Route::middleware(['auth', 'nocache', 'teams'])->group(function () {

    // ===========================
    //  Profile Route
    // ===========================
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    // ===========================
    //  Two Factor Authentication Routes
    // ===========================
    Route::prefix('2fa')->group(function () {
        Route::post('/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
        Route::post('/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
        Route::post('/disable/auth', [TwoFactorController::class, 'disableWithAuthenticator'])->name('2fa.disable.auth');
        Route::post('/disable/recovery-code', [TwoFactorController::class, 'disableWithRecoveryCode'])->name('2fa.disable.recovery');
        Route::post('/regenerate-recovery', [TwoFactorController::class, 'regenerate'])->name('2fa.recovery');
    });

    // ===========================
    //  Dashboard Routes
    // ===========================
    // Admin Dashboard - Scoped to Super Admin Only
    Route::middleware('role:admin,zerogame_admin,yahala_admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });



    // ===========================
    //  Admin Routes (Super Admin Only)
    // ===========================
    Route::middleware('role:admin')->prefix('admin')->group(function () {

=======
//  Authenticated Routes (For Authenticated Users)
Route::middleware(['auth', 'nocache'])->group(function () {

    //  Profile Route (Display Only)
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

    //  Two Factor Authentication Routes
    Route::prefix('2fa')->group(function () {
        Route::post('/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
        Route::post('/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
        Route::post('/2fa/disable/authenticator', [TwoFactorController::class, 'disableWithAuthenticator'])->name('2fa.disable.authenticator');
        Route::post('/2fa/disable/recovery', [TwoFactorController::class, 'disableWithRecoveryCode'])->name('2fa.disable.recovery');
        Route::post('/recovery', [TwoFactorController::class, 'regenerate'])->name('2fa.recovery');
    });


    //  Dashboard Routes (Admin + Merchant)
    Route::middleware('role:admin|merchant')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    //  Admin Routes (Protected by Role Middleware)
    Route::middleware('role:admin')->prefix('admin')->group(function () {

>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
        // Admin Users
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::patch('/users/{id}/update-role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');

<<<<<<< HEAD
        //Admin Merchant
        Route::get('/merchants', [MerchantController::class, 'index'])->name('admin.merchants.index');
        Route::post('/merchants', [MerchantController::class, 'store'])->name('admin.merchants.store');
        Route::delete('/merchants/{id}', [MerchantController::class, 'destroy'])->name('admin.merchants.destroy');

=======
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
        // Admin Invites
        Route::get('/invites', [InviteController::class, 'index'])->name('invites.index');
        Route::post('/invites', [InviteController::class, 'invite'])->name('invites.send');
        Route::delete('/invites/{id}', [InviteController::class, 'deleteInvite'])->name('invites.delete');
        Route::post('/invites/{id}/resend', [InviteController::class, 'resendInvite'])->name('invites.resend');
    });

<<<<<<< HEAD
    // ===========================
    //  Merchant Routes (Scoped to Merchant ID)
    // ===========================
    Route::prefix('merchant')->group(function () {
=======
    //  Merchant Routes (Protected by Role Middleware)
    Route::middleware('role:merchant')->prefix('merchant')->group(function () {

        // Merchant Users
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
        Route::get('/users', [MerchantUserController::class, 'index'])->name('merchant.users.index');
        Route::post('/users', [MerchantUserController::class, 'store'])->name('merchant.users.store');
        Route::delete('/users/{id}', [MerchantUserController::class, 'destroy'])->name('merchant.users.destroy');
    });

<<<<<<< HEAD


    // ===========================
    //  Logout Route
    // ===========================
=======
    //  Logout Route
>>>>>>> 5facc614503652ba13d316d933c77bc46416dbd2
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
