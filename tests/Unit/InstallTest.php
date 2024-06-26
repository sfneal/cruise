<?php

namespace Sfneal\Cruise\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\Test;
use Sfneal\Cruise\Tests\TestCase;

class InstallTest extends TestCase
{
    protected bool $shouldInstall = false;

    #[Test]
    public function copies_the_configuration()
    {
        // make sure we're starting from a clean state
        if (File::exists(config_path('cruise.php'))) {
            unlink(config_path('cruise.php'));
        }

        $this->assertFalse(File::exists(config_path('cruise.php')));

        Artisan::call('cruise:install', $this->getCruiseInstallArguments());

        $this->assertTrue(File::exists(config_path('cruise.php')));
    }

    #[Test]
    public function copies_docker_assets()
    {
        // Get list of docker asset files
        $directory = __DIR__.'/../../docker/services';
        $files = [];
        foreach (scandir($directory) as $file) {
            if ($file !== '.' && $file !== '..') {
                $files[] = $directory.'/'.$file;
            }
        }

        // Confirm files DON'T already exist
        foreach ($files as $file) {
            $this->assertFalse(File::exists(base_path(basename($file))));
        }

        // Run install command
        Artisan::call('cruise:install', $this->getCruiseInstallArguments());

        // Confirm files DO exists
        foreach ($files as $file) {
            $this->assertTrue(File::exists(base_path(basename($file))));
        }
    }

    #[Test]
    public function renames_docker_images()
    {
        // Run install command
        Artisan::call('cruise:install', $this->getCruiseInstallArguments());

        $docker_files = [
            'docker-compose.yml',
            'docker-compose-dev.yml',
            'docker-compose-dev-db.yml',
            'docker-compose-dev-node.yml',
            'docker-compose-tests.yml',
        ];
        foreach ($docker_files as $file) {
            $image_name = trim(Process::path(base_path())
                ->run("grep -A 10 'services:' {$file} | grep -A 10 'app:' | grep 'image:' | awk '{print $2}' | grep -o '^[^:]*'")
                ->output());

            $this->assertStringContainsString(
                self::TEST_DOCKER_ID.'/'.self::TEST_DOCKER_IMAGE,
                $image_name,
                "New Docker image name not found in {$file}"
            );
        }
    }

    #[Test]
    public function can_prompt_for_docker_id_and_image_name()
    {
        $this->artisan('cruise:install')
            ->expectsQuestion('Enter your Docker ID:', 'fakedockerid')
            ->expectsQuestion('Enter your Docker image name (recommend using application name):', 'myapp')
            ->assertSuccessful();
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

        $this->artisan('cruise:install', $this->getCruiseInstallArguments());

        // Confirm scripts added after installation
        $post_install = json_decode(file_get_contents(base_path('composer.json')), true);
        $this->assertNotEmpty($post_install['scripts']);
        foreach ($expected_scripts as $script_k => $script_v) {
            $this->assertArrayHasKey($script_k, $post_install['scripts']);
            $this->assertEquals($post_install['scripts'][$script_k], $script_v);
        }
    }
}
