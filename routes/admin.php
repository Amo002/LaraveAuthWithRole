<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\MerchantManagementController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Auth\InviteController;

use Illuminate\Support\Facades\Route;

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

    // Merchant Management 
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
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('admin.roles.store');
    Route::post('/roles/{role}/assign-permissions', [RoleController::class, 'assignPermission'])->name('admin.roles.assignPermission');
    Route::delete('/roles/{role}/permissions/{permission}', [RoleController::class, 'revokePermission'])->name('admin.roles.revokePermission');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
});
