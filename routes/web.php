<?php

use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\MerchantManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\InviteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Main\HomeController;
use App\Http\Controllers\Merchant\RoleController;
use App\Http\Controllers\Admin\RoleController as AdminRoleController; 
use App\Http\Controllers\Merchant\UserController as MerchantUserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// ===============================
//  Public Routes (No Cache)
// ===============================
Route::middleware(['nocache'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

// ===============================
//  Guest Routes (For Unauthenticated Users)
// ===============================
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
    // Admin Dashboard -
    Route::middleware('role:admin,merchant_admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });



    // ===========================
    //  Admin Routes (Super Admin Only)
    // ===========================
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        // Admin Users
        Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
        Route::patch('/users/{id}/update-role', [UserController::class, 'updateRole'])->name('admin.users.updateRole');

        // Admin Merchant
        Route::get('/merchants', [MerchantController::class, 'index'])->name('admin.merchants.index');
        Route::post('/merchants', [MerchantController::class, 'store'])->name('admin.merchants.store');
        Route::delete('/merchants/{id}', [MerchantController::class, 'destroy'])->name('admin.merchants.destroy');
        Route::patch('/admin/merchants/{id}/toggle', [MerchantController::class, 'toggleStatus'])->name('admin.merchants.toggle');

        // MERCHANT MANAGEMENT 
        Route::get('merchants/{id}/manage', [MerchantManagementController::class, 'show'])->name('admin.merchants.manage');
        Route::post('merchants/{merchant}/permissions', [MerchantManagementController::class, 'storePermission'])->name('admin.merchants.permissions.store');
        Route::delete('merchants/{merchant}/permissions/{permission}', [MerchantManagementController::class, 'destroyPermission'])->name('admin.merchants.permissions.destroy');
        Route::post('merchants/{merchant}/roles/{role}/assign-permission', [MerchantManagementController::class, 'assignPermission'])->name('admin.merchants.roles.assignPermission');
        Route::delete('merchants/{merchant}/roles/{role}/permissions/{permission}', [MerchantManagementController::class, 'revokePermission'])->name('admin.merchants.roles.revokePermission');
        Route::post('/admin/merchants/{merchant}/permissions/unlock', [MerchantManagementController::class, 'unlockDevPermissions'])->name('admin.merchants.permissions.unlock');
        Route::delete('merchants/{merchant}/roles/{role}', [MerchantManagementController::class, 'destroyRole'])->name('admin.merchants.roles.destroy');


        // Admin Invites
        Route::get('/invites', [InviteController::class, 'index'])->name('invites.index');
        Route::post('/invites', [InviteController::class, 'invite'])->name('invites.send');
        Route::delete('/invites/{id}', [InviteController::class, 'deleteInvite'])->name('invites.delete');
        Route::post('/invites/{id}/resend', [InviteController::class, 'resendInvite'])->name('invites.resend');


        // Merchant Roles
        Route::get('/roles', [AdminRoleController::class, 'index'])->name('admin.roles.index');
        Route::post('/roles', [AdminRoleController::class, 'store'])->name('admin.roles.store');
        Route::post('/roles/{role}/assign-permissions', [AdminRoleController::class, 'assignPermission'])->name('admin.roles.assignPermission');
        Route::delete('/roles/{role}/permissions/{permission}', [AdminRoleController::class, 'revokePermission'])->name('admin.roles.revokePermission');
        Route::delete('/roles/{role}', [AdminRoleController::class, 'destroy'])->name('admin.roles.destroy');
        });

    // ===========================
    //  Merchant Routes (Scoped to Merchant ID)
    // ===========================

    Route::middleware('merchant.active')->prefix('merchant')->group(function () {
        // Merchant Users
        Route::get('/users', [MerchantUserController::class, 'index'])->name('merchant.users.index')->middleware('can:view-merchant-users');
        Route::post('/users', [MerchantUserController::class, 'store'])->name('merchant.users.store')->middleware('can:create-merchant-users');
        Route::delete('/users/{id}', [MerchantUserController::class, 'destroy'])->name('merchant.users.destroy')->middleware('can:delete-merchant-users');

        // Merchant Roles
        Route::get('/roles', [RoleController::class, 'index'])->name('merchant.roles.index')->middleware('can:view-roles');
        Route::post('/roles', [RoleController::class, 'store'])->name('merchant.roles.store')->middleware('can:create-roles');
        Route::post('/roles/{role}/assign-permissions', [RoleController::class, 'assignPermission'])->name('merchant.roles.assignPermission')->middleware('can:edit-roles');
        Route::delete('/roles/{role}/revoke-permission/{permission}', [RoleController::class, 'revokePermission'])->name('merchant.roles.revokePermission')->middleware('can:edit-roles');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('merchant.roles.destroy')->middleware('can:delete-roles');
    });






    // ===========================
    //  Logout Route
    // ===========================
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
