<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Global Admin (Super Admin)
        Gate::define('admin', fn($user) => $user->hasRole('admin') && $user->merchant_id === 1);

        // Yahala Admin
        Gate::define(
            'yahala-users',
            fn($user) =>
            $user->merchant_id === 2 && $user->hasAnyRole(['yahala_admin', 'yahala_editor', 'yahala_user'])
        );

        // ZeroGame Admin
        Gate::define(
            'zerogame-users',
            fn($user) =>
            $user->merchant_id === 3 && $user->hasAnyRole(['zerogame_admin', 'zerogame_editor', 'zerogame_user'])
        );

        Gate::define('dashboard', fn($user) => $user->hasRole(['yahala_admin', 'zerogame_admin', 'admin']));
    }
}
