<?php

use App\Http\Controllers\Merchant\UserController;
use App\Http\Controllers\Merchant\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('merchant.active')->prefix('merchant')->group(function () {
    // Merchant Users
    Route::get('/users', [UserController::class, 'index'])->name('merchant.users.index')->middleware('can:view-merchant-users');
    Route::post('/users', [UserController::class, 'store'])->name('merchant.users.store')->middleware('can:create-merchant-users');
    Route::patch('/users/{id}/update-role', [UserController::class, 'updateRole'])->name('merchant.users.updateRole')->middleware('can:assign-merchant-roles');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('merchant.users.destroy')->middleware('can:delete-merchant-users');

    // Merchant Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('merchant.roles.index')->middleware('can:view-roles');
    Route::post('/roles', [RoleController::class, 'store'])->name('merchant.roles.store')->middleware('can:create-roles');
    Route::post('/roles/{role}/assign-permissions', [RoleController::class, 'assignPermission'])->name('merchant.roles.assignPermission')->middleware('can:edit-roles');
    Route::delete('/roles/{role}/revoke-permission/{permission}', [RoleController::class, 'revokePermission'])->name('merchant.roles.revokePermission')->middleware('can:edit-roles');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('merchant.roles.destroy')->middleware('can:delete-roles');
});
