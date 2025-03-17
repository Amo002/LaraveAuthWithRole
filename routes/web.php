<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\InviteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Main\HomeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['nocache'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

Route::middleware(['guest', 'nocache'])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register/invite/{payload}', [RegisterController::class, 'showInviteForm'])->name('register.invite');
    Route::post('/register/complete', [RegisterController::class, 'completeRegistration'])->name('register.complete');
});

Route::middleware(['auth', 'nocache'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware('role:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{id}/update-role', [UserController::class, 'updateRole'])->name('users.updateRole');

        Route::get('/invites', [InviteController::class, 'index'])->name('invites.index');
        Route::post('/invites', [InviteController::class, 'invite'])->name('invites.send');
        Route::delete('/invites/{id}', [InviteController::class, 'deleteInvite'])->name('invites.delete');
        Route::post('/invites/{id}/resend', [InviteController::class, 'resendInvite'])->name('invites.resend');
    });
});
