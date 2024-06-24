<?php

namespace Sfneal\Cruise\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Sfneal\Cruise\Providers\CruiseServiceProvider;

class TestCase extends OrchestraTestCase
{
    protected bool $shouldInstall = true;
    protected bool $shouldUninstall = true;

    /**
     * Get package providers.
     *
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            CruiseServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {

    }

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->shouldInstall) {
            $this->artisan('cruise:install');
        }
    }

    protected function tearDown(): void
    {
        if ($this->shouldUninstall) {
            $this->artisan('cruise:uninstall');
        }

        parent::tearDown();
    }
}
