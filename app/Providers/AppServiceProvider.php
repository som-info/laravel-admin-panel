<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        // Role matrix: admin > manager > staff
        Gate::define('manage-catalog', fn (User $u) => $u->hasRole('admin', 'manager'));
        Gate::define('manage-invoices', fn (User $u) => $u->hasRole('admin', 'manager'));
        Gate::define('delete-records', fn (User $u) => $u->hasRole('admin', 'manager'));
        Gate::define('view-activity', fn (User $u) => $u->hasRole('admin', 'manager'));
        Gate::define('manage-settings', fn (User $u) => $u->isAdmin());
    }
}
