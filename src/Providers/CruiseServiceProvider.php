<?php

namespace Sfneal\Cruise\Providers;

use Illuminate\Support\ServiceProvider;
use Sfneal\Cruise\Commands\CruiseInstall;
use Sfneal\Cruise\Commands\Database\MigrateDbInProduction;
use Sfneal\Cruise\Commands\Database\WaitForDb;
use Sfneal\Cruise\Commands\Semver\Bump;
use Sfneal\Cruise\Commands\Semver\BumpMajor;
use Sfneal\Cruise\Commands\Semver\BumpMinor;
use Sfneal\Cruise\Commands\Semver\BumpPatch;

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
                BumpPatch::class,

                // Install command
                CruiseInstall::class,
            ]);
        }

        // Docker compose & dockerfiles
        $this->publishes([
            __DIR__.'/../../docker/services' => base_path(''),
        ], 'docker');

        // Supervisor configs
        $this->publishes([
            __DIR__.'/../../docker/supervisor' => base_path('docker/supervisor'),
        ], 'docker');

        // Docker scripts configs
        $this->publishes([
            __DIR__.'/../../docker/scripts' => base_path('docker/scripts'),
        ], 'docker');
    }

    /**
     * Register any Users services.
     */
    public function register(): void
    {

    }
}
