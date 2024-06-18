<?php

namespace Sfneal\Cruise\Providers;

use Illuminate\Support\ServiceProvider;
use Sfneal\Cruise\Commands\Bump;
use Sfneal\Cruise\Commands\CruiseInstall;
use Sfneal\Cruise\Commands\MigrateDbInProduction;
use Sfneal\Cruise\Commands\WaitForDb;

class CruiseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any Users services
     */
    public function boot(): void
    {
        // Publish config file
        $this->publishes([
            __DIR__.'/../../config/cruise.php' => config_path('cruise.php'),
        ], ['config', 'cruise-config']);

        // Load commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                // DB commands
                MigrateDbInProduction::class,
                WaitForDb::class,

                // Semver bump commands
                Bump::class,

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
        // Load config file
        $this->mergeConfigFrom(__DIR__.'/../../config/cruise.php', 'cruise');
    }
}
