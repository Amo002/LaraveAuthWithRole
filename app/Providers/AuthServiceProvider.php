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

        Gate::define('admin', fn($user) => $user->hasRole('admin') && $user->merchant_id === 1);

        Gate::define('dashboard', fn(User $user) => $user->hasRole(['merchant_admin', 'admin']));

        Gate::define('merchant', fn($user) => $user->hasRole(['merchant_admin', 'yahala_viewer']) && $user->merchant_id !== 1);
    }
}
