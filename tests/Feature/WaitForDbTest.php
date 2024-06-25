<?php

namespace Sfneal\Cruise\Tests\Feature;

use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Sfneal\Cruise\Commands\MigrateDbInProduction;
use Sfneal\Cruise\Tests\TestCase;

class WaitForDbTest extends TestCase
{
    #[Test]
    public function can_wait_for_db_to_become_available()
    {
        $command = $this->artisan('db:wait');

        $command->assertSuccessful();
        $command->expectsOutputToContain('Waiting for the DB connection to become available.');
        $command->expectsOutputToContain('seconds to connect to the DB.');
    }
}
