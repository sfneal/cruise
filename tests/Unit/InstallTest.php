<?php

namespace Sfneal\Cruise\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Sfneal\Cruise\Tests\TestCase;

class InstallTest extends TestCase
{
    protected bool $shouldInstall = false;
    protected bool $shouldUninstall = false;

    #[Test]
    function copies_the_configuration()
    {
        // make sure we're starting from a clean state
        if (File::exists(config_path('cruise.php'))) {
            unlink(config_path('cruise.php'));
        }

        $this->assertFalse(File::exists(config_path('cruise.php')));

        Artisan::call('cruise:install');

        $this->assertTrue(File::exists(config_path('cruise.php')));
    }

    #[Test]
    public function copies_docker_assets()
    {
        // Get list of docker asset files
        $directory = __DIR__ . '/../../docker/services';
        $files = [];
        foreach (scandir($directory) as $file) {
            if ($file !== '.' && $file !== '..') {
                $files[] = $directory . '/' . $file;
            }
        }

        // Confirm files DON'T already exist
        foreach ($files as $file) {
            $this->assertFalse(File::exists(base_path(basename($file))));
        }

        // Run install command
        Artisan::call('cruise:install');

        // Confirm files DO exists
        foreach ($files as $file) {
            $this->assertTrue(File::exists(base_path(basename($file))));
        }
    }

    #[Test]
    public function adds_composer_commands()
    {
        $expected_scripts = [
            'start-dev' => 'sh vendor/sfneal/cruise/scripts/runners/start-dev.sh',
            'start-dev-db' => 'sh vendor/sfneal/cruise/scripts/runners/start-dev-db.sh',
            'start-dev-node' => 'sh vendor/sfneal/cruise/scripts/runners/start-dev-node.sh',
            'start-test' => 'sh vendor/sfneal/cruise/scripts/runners/start-test.sh',
            'stop' => 'sh vendor/sfneal/cruise/scripts/runners/stop.sh',
            'build' => 'sh vendor/sfneal/cruise/scripts/runners/build.sh',
        ];

        // Confirm no scripts are added prior to cruise installation
        $pre_install = json_decode(file_get_contents(base_path('composer.json')), true);
        $this->assertEmpty($pre_install['scripts']);

        $this->artisan('cruise:install');

        // Confirm scripts added after installation
        $post_install = json_decode(file_get_contents(base_path('composer.json')), true);
        $this->assertNotEmpty($post_install['scripts']);
        foreach($expected_scripts as $script_k => $script_v) {
            $this->assertArrayHasKey($script_k, $post_install['scripts']);
            $this->assertEquals($post_install['scripts'][$script_k], $script_v);
        }
    }
}
