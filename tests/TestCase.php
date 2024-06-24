<?php

namespace Sfneal\Cruise\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Sfneal\Cruise\Providers\CruiseServiceProvider;

class TestCase extends OrchestraTestCase
{
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

        $this->artisan('cruise:install');
    }

    protected function tearDown(): void
    {
        $this->artisan('cruise:uninstall');

        parent::tearDown();
    }
}
