<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Page;
use App\Policies\PagePolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Register policies
        Gate::policy(Page::class, PagePolicy::class);

        // Register permission gates
        Gate::define('manage_property_types', function (User $user) {
            return $user->role && $user->role->hasPermission('manage_property_types');
        });

        Gate::define('manage_property_operations', function (User $user) {
            return $user->role && $user->role->hasPermission('manage_property_operations');
        });

        Gate::define('manage_property_statuses', function (User $user) {
            return $user->role && $user->role->hasPermission('manage_property_statuses');
        });

        // Register page-related gates
        Gate::define('edit-page', function (User $user, Page $page) {
            return (new PagePolicy())->update($user, $page);
        });
    }
}
