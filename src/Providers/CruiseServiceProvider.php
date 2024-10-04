<?php

namespace Sfneal\Cruise\Providers;

use Illuminate\Support\ServiceProvider;
use Sfneal\Cruise\Commands\Bump;
use Sfneal\Cruise\Commands\CruiseInstall;
use Sfneal\Cruise\Commands\CruiseUninstall;
use Sfneal\Cruise\Commands\MigrateDbInProduction;
use Sfneal\Cruise\Commands\Version;
use Sfneal\Cruise\Commands\WaitForDb;

class CruiseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any Users services.
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
                Version::class,

                // Install/uninstall command
                CruiseInstall::class,
                CruiseUninstall::class,
            ]);
        }

        // version & changelog files
        $this->publishes([
            __DIR__.'/../../docker/static' => base_path(''),
        ], 'docker');

        // Supervisor configs
        $this->publishes([
            __DIR__.'/../../docker/supervisor' => base_path('docker/supervisor'),
        ], 'docker');

        // Docker scripts configs
        $this->publishes([
            __DIR__.'/../../docker/scripts' => base_path('docker/scripts'),
        ], 'docker');

        // Webpack - Docker compose & dockerfiles
        $this->publishes([
            __DIR__.'/../../docker/services/webpack' => base_path(''),
        ], 'docker-webpack');

        // Vite - Docker compose & dockerfiles
        $this->publishes([
            __DIR__.'/../../docker/services/vite' => base_path(''),
        ], 'docker-vite');

        // Domain Driven Design - BaseApplication & bootstrap/app.php
        $this->publishes([
            __DIR__.'/../../docker/ddd' => base_path(''),
        ], 'ddd');
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
