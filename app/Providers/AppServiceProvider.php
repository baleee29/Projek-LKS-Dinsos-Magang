<?php

namespace App\Providers;

use App\Services\IsiHalaman;
use App\View\Composers\DashboardComposer;
use Illuminate\Support\Facades\View;
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
        View::composer(['layouts.dashboard', 'errors.page-dashboard'], DashboardComposer::class);
        View::composer(['layouts.public', 'public.*', 'components.public.*'], fn ($view) => $view->with('landing', new IsiHalaman));
    }
}
