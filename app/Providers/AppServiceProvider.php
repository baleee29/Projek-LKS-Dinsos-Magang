<?php

namespace App\Providers;

use App\Support\Demo\DemoLandingContents;
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
        // TODO(Tahap 2): ganti dengan App\Services\LandingContents dari sumber.
        View::composer(['layouts.public', 'public.*', 'components.public.*'], fn ($view) => $view->with('landing', new DemoLandingContents));
    }
}
