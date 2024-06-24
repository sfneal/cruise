<?php

namespace Sfneal\Cruise\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Sfneal\Cruise\Commands\Bump;
use Sfneal\Cruise\Tests\TestCase;

class BumpTest extends TestCase
{
    private array $copiedFiles = [];

    private function copyDirectory(string $source, string $destination): void
    {
        if (! is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        $files = scandir($source);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $sourceFile = $source.'/'.$file;
                $destinationFile = $destination.'/'.$file;
                if (is_dir($sourceFile)) {
                    $this->copyDirectory($sourceFile, $destinationFile);
                } else {
                    copy($sourceFile, $destinationFile);
                    $this->copiedFiles[] = $destinationFile;
                }
            }
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Add scripts directory
        $this->copyDirectory(__DIR__.'/../../scripts/version', base_path('vendor/sfneal/cruise/scripts/version'));
        chmod(base_path('vendor/sfneal/cruise/scripts/version/semver'), 0755);
    }

    protected function tearDown(): void
    {
        foreach ($this->copiedFiles as $file) {
            unlink($file);
        }

        parent::tearDown();
    }

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
