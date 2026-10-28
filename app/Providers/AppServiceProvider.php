<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\Property;
use App\Models\User;
use App\Policies\PagePolicy;
use App\Policies\PropertyPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Paginator::useBootstrapFive();

        // Register policies
        Gate::policy(Page::class, PagePolicy::class);
        Gate::policy(Property::class, PropertyPolicy::class);

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
            return (new PagePolicy)->update($user, $page);
        });
    }
}
