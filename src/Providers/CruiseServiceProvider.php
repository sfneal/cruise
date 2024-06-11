<?php

namespace Sfneal\Cruise\Providers;

use Illuminate\Support\ServiceProvider;
use Sfneal\Cruise\Database\MigrateDbInProduction;
use Sfneal\Cruise\Database\WaitForDb;
use Sfneal\Cruise\Semver\Bump;
use Sfneal\Cruise\Semver\BumpMajor;
use Sfneal\Cruise\Semver\BumpMinor;
use Sfneal\Cruise\Semver\BumpPatch;

class CruiseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any Users services
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                // DB commands
                MigrateDbInProduction::class,
                WaitForDb::class,

                // Semver bump commands
                Bump::class,
                BumpMajor::class,
                BumpMinor::class,
                BumpPatch::class
            ]);
        }

        $this->publishes([
            __DIR__.'/../../docker/services' => base_path(''),
        ], 'docker');
    }

    /**
     * Register any Users services.
     */
    public function register(): void
    {

    }
}
