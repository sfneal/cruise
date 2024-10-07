<?php

namespace Sfneal\Cruise\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Sfneal\Cruise\Tests\TestCase;

class InstallTest extends TestCase
{
    protected bool $shouldInstall = false;

    public static function frontEndCompilersProvider(): array
    {
        return [
            'Webpack' => ['Webpack'],
            'Vite' => ['Vite'],
        ];
    }

    #[Test]
    #[DataProvider('frontEndCompilersProvider')]
    public function can_copy_the_configuration(string $front_end_compiler)
    {
        // make sure we're starting from a clean state
        if (File::exists(config_path('cruise.php'))) {
            unlink(config_path('cruise.php'));
        }

        $this->assertFalse(File::exists(config_path('cruise.php')));

        Artisan::call('cruise:install', $this->getCruiseInstallArguments($front_end_compiler));

        $this->assertTrue(File::exists(config_path('cruise.php')));
    }

    #[Test]
    #[DataProvider('frontEndCompilersProvider')]
    public function can_copy_docker_assets(string $front_end_compiler)
    {
        // Get list of docker asset files
        $directory = __DIR__.'/../../docker/services/'.strtolower($front_end_compiler);
        $files = [];
        foreach (scandir($directory) as $file) {
            if ($file !== '.' && $file !== '..') {
                $files[] = $directory.'/'.$file;
            }
        }

        // Confirm files DON'T already exist
        foreach ($files as $file) {
            $file_path = base_path(basename($file));
            $this->assertFalse(File::exists($file_path), "The file '{$file_path}' already exists");
        }

        // Run install command
        Artisan::call('cruise:install', $this->getCruiseInstallArguments($front_end_compiler));

        // Confirm files DO exists
        foreach ($files as $file) {
            $file_path = base_path(basename($file));
            $this->assertTrue(File::exists($file_path), "The file '{$file_path}' does not exists");
        }
    }

    #[Test]
    #[DataProvider('frontEndCompilersProvider')]
    public function can_copy_static_assets(string $front_end_compiler)
    {
        // Get list of docker asset files
        $directory = __DIR__.'/../../docker/static';
        $files = [];
        foreach (scandir($directory) as $file) {
            if ($file !== '.' && $file !== '..') {
                $files[] = $directory.'/'.$file;
            }
        }

        // Confirm files DON'T already exist
        foreach ($files as $file) {
            $file_path = base_path(basename($file));
            $this->assertFalse(File::exists($file_path), "The file '{$file_path}' already exists");
        }

        // Run install command
        Artisan::call('cruise:install', $this->getCruiseInstallArguments($front_end_compiler));

        // Confirm files DO exists
        foreach ($files as $file) {
            $file_path = base_path(basename($file));
            $this->assertTrue(File::exists($file_path), "The file '{$file_path}' does not exists");
        }
    }

    #[Test]
    #[DataProvider('frontEndCompilersProvider')]
    public function can_rename_docker_images(string $front_end_compiler)
    {
        // Run install command
        Artisan::call('cruise:install', $this->getCruiseInstallArguments($front_end_compiler));

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
    public function can_prompt_for_docker_id_image_name_and_frontend_compiler()
    {
        $this->artisan('cruise:install')
            ->expectsQuestion('Enter your Docker ID:', 'fakedockerid')
            ->expectsQuestion('Enter your Docker image name (recommend using application name):', 'myapp')
            ->expectsChoice('Select Front-end asset compiler', 'Webpack', ['Webpack', 'Vite'])
            ->assertSuccessful();
    }

    #[Test]
    #[DataProvider('frontEndCompilersProvider')]
    public function can_add_composer_commands(string $front_end_compiler)
    {
        $expected_scripts = [
            'test' => 'docker exec -it app vendor/bin/phpunit',
            'start-dev' => 'sh vendor/sfneal/cruise/scripts/runners/start-dev.sh',
            'start-dev-db' => 'sh vendor/sfneal/cruise/scripts/runners/start-dev-db.sh',
            'start-dev-node' => 'sh vendor/sfneal/cruise/scripts/runners/start-dev-node.sh',
            'start-test' => 'sh vendor/sfneal/cruise/scripts/runners/start-test.sh',
            'stop' => 'sh vendor/sfneal/cruise/scripts/runners/stop.sh',
            'build' => 'sh vendor/sfneal/cruise/scripts/runners/build.sh',
        ];

        // Confirm no scripts are added prior to cruise installation
        $pre_install = json_decode(file_get_contents(base_path('composer.json')), true);
        $this->logicalOr(
            empty($pre_install['scripts']),
            ! array_key_exists('scripts', $pre_install)
        );

        $this->artisan('cruise:install', $this->getCruiseInstallArguments($front_end_compiler));

        // Confirm scripts added after installation
        $post_install = json_decode(file_get_contents(base_path('composer.json')), true);
        $this->assertNotEmpty($post_install['scripts']);
        foreach ($expected_scripts as $script_k => $script_v) {
            $this->assertArrayHasKey($script_k, $post_install['scripts']);
            $this->assertEquals($post_install['scripts'][$script_k], $script_v);
        }
    }
}
