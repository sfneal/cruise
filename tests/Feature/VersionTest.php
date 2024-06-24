<?php

namespace Sfneal\Cruise\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Sfneal\Cruise\Tests\TestCase;

class VersionTest extends TestCase
{
    #[Test]
    public function can_get_app_version()
    {
        $command = $this->artisan('version --path=' . __DIR__.'/../../docker/services/version.txt');

        $command->assertSuccessful();
        $command->expectsOutput('v0.1.0');
    }

    #[Test]
    public function cant_get_app_version_without_path()
    {
        $command = $this->artisan('version');

        $command->assertFailed();
    }
}
