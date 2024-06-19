<?php

namespace Sfneal\Cruise\Tests;

use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

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
        $this->copyDirectory(__DIR__.'/../docker/services', __DIR__.'/../');
    }

    protected function tearDown(): void
    {
        foreach ($this->copiedFiles as $file) {
            unlink($file);
        }
    }

    #[Test]
    public function can_bump_major_version()
    {
        $this->markTestSkipped();
        $this->artisan('bump:major')->assertSuccessful();
    }
}
