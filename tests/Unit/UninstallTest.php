<?php

namespace Sfneal\Cruise\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Sfneal\Cruise\Tests\TestCase;

class UninstallTest extends TestCase
{
    protected bool $shouldUninstall = false;

    #[Test]
    public function can_remove_the_config()
    {
        $this->assertTrue(File::exists(config_path('cruise.php')));

        Artisan::call('cruise:uninstall');

        $this->assertFalse(File::exists(config_path('cruise.php')));
    }

    #[Test]
    public function can_remove_docker_assets()
    {
        // Get list of docker asset files
        $directory = __DIR__.'/../../docker/services';
        $files = [];
        foreach (scandir($directory) as $file) {
            if ($file !== '.' && $file !== '..') {
                $files[] = $directory.'/'.$file;
            }
        }

        // Confirm files DO exists
        foreach ($files as $file) {
            $this->assertTrue(File::exists(base_path(basename($file))));
        }

        // Run install command
        Artisan::call('cruise:uninstall');

        // Confirm files DON'T already exist
        foreach ($files as $file) {
            $this->assertFalse(File::exists(base_path(basename($file))));
        }

        // Confirm docker assets were removed
        $this->assertFalse(is_dir(base_path('docker')));
    }

    #[Test]
    public function can_remove_composer_commands()
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
        $this->assertNotEmpty($pre_install['scripts']);
        foreach ($expected_scripts as $script_k => $script_v) {
            $this->assertArrayHasKey($script_k, $pre_install['scripts']);
            $this->assertEquals($pre_install['scripts'][$script_k], $script_v);
        }

        $this->artisan('cruise:uninstall');

        // Confirm scripts added after installation
        $post_install = json_decode(file_get_contents(base_path('composer.json')), true);
        $this->assertEmpty($post_install['scripts']);
    }
}
