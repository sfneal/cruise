<?php

namespace Sfneal\Cruise\Tests;

class ScriptTestCase extends TestCase
{
    protected array $copiedFiles = [];

    protected function copyDirectory(string $source, string $destination): void
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
        $this->copyDirectory(__DIR__.'/../scripts/version', base_path('vendor/sfneal/cruise/scripts/version'));
        chmod(base_path('vendor/sfneal/cruise/scripts/version/semver'), 0755);
    }

    protected function tearDown(): void
    {
        foreach ($this->copiedFiles as $file) {
            unlink($file);
        }

        parent::tearDown();
    }
}
