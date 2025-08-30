<?php

namespace Shankar\VisitTracker;

use Illuminate\Support\ServiceProvider;
use Shankar\VisitTracker\Services\VisitAnalytics;

class VisitTrackerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/visit-tracker.php', 'visit-tracker');

        $this->app->singleton('visit-tracker', function () {
            return new VisitAnalytics();
        });
    }

    public function boot(): void
    {
        // Config publish
        $this->publishes([
            __DIR__.'/../config/visit-tracker.php' => config_path('visit-tracker.php'),
        ], 'visit-tracker-config');

        // Migration publish
        if (! class_exists('CreateVisitsTable')) {
            $timestamp = date('Y_m_d_His', time());
            $this->publishes([
                __DIR__.'/../database/migrations/create_visits_table.php' => database_path('migrations/'.$timestamp.'_create_visits_table.php'),
            ], 'visit-tracker-migrations');
        }

        // Auto-load package migrations (optional)
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}
