<?php

namespace Sfneal\Cruise\Tests\Feature;

use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Sfneal\Cruise\Commands\MigrateDbInProduction;
use Sfneal\Cruise\Tests\TestCase;

class MigrateDbInProductionTest extends TestCase
{
    #[Test]
    public function cant_migrate_db_when_not_in_production()
    {
        $command = $this->artisan('migrate:prod');

        $command->assertFailed();
        $command->expectsOutput(MigrateDbInProduction::FAILURE_MESSAGE);
    }

    #[Test]
    public function can_migrate_db_when_in_production()
    {
        Config::set('app.env', 'production');

        $command = $this->artisan('migrate:prod');

        $command->assertSuccessful();
        $command->expectsOutput(MigrateDbInProduction::SUCCESS_MESSAGE);
    }
}
