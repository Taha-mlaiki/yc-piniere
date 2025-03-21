<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    protected $policies = [
        'App\Models\Category' => 'App\Policies\CategoryPolicy',
        'App\Models\Plant' => 'App\Policies\PlantPolicy',
        'App\Models\Order' => 'App\Policies\OrderPolicy',
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define gates for user roles
        Gate::define('manage-plants', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-categories', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('view-statistics', function (User $user) {
            return $user->isAdmin();
        });

        Gate::define('manage-orders', function (User $user) {
            return $user->isEmployee();
        });
    }
}
