<?php

namespace Sfneal\Cruise\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Sfneal\Cruise\Tests\TestCase;

class VersionTest extends TestCase
{
    #[Test]
    public function can_get_app_version()
    {
        $command = $this->artisan('version');

        $command->assertSuccessful();
        $command->expectsOutput('v0.1.0');
    }
}
