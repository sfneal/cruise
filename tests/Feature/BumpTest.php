<?php

namespace Sfneal\Cruise\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Sfneal\Cruise\Commands\Bump;
use Sfneal\Cruise\Tests\ScriptTestCase;
use Sfneal\Cruise\Tests\TestCase;

class BumpTest extends ScriptTestCase
{
    #[Test]
    public function can_bump_major_app_version()
    {
        $command = $this
            ->withoutMockingConsoleOutput()
            ->artisan('bump major');

        $output = Artisan::output();

        $this->assertEquals(0, $command);
        $this->assertStringContainsString('major', $output);
        $this->assertStringContainsString('1.0.0', $output);
        $this->assertStringContainsString('0.1.0 --> 1.0.0', $output);
        $this->assertEquals('BUMP major version (0.1.0 --> 1.0.0)', trim($output));
    }

    #[Test]
    public function can_bump_minor_app_version()
    {
        $command = $this
            ->withoutMockingConsoleOutput()
            ->artisan('bump minor');

        $output = Artisan::output();

        $this->assertEquals(0, $command);
        $this->assertStringContainsString('minor', $output);
        $this->assertStringContainsString('0.2.0', $output);
        $this->assertStringContainsString('0.1.0 --> 0.2.0', $output);
        $this->assertEquals('BUMP minor version (0.1.0 --> 0.2.0)', trim($output));
    }

    #[Test]
    public function can_bump_patch_app_version()
    {
        $command = $this
            ->withoutMockingConsoleOutput()
            ->artisan('bump patch');

        $output = Artisan::output();

        $this->assertEquals(0, $command);
        $this->assertStringContainsString('patch', $output);
        $this->assertStringContainsString('0.1.1', $output);
        $this->assertStringContainsString('0.1.0 --> 0.1.1', $output);
        $this->assertEquals('BUMP patch version (0.1.0 --> 0.1.1)', trim($output));
    }

    #[Test]
    public function can_bump_version_with_prompts()
    {
        foreach (Bump::TYPES as $type) {
            $this->artisan('bump')
                ->expectsChoice('Which semver segment would you like to bump?', $type, Bump::TYPES)
                ->assertSuccessful();
        }
    }
}
