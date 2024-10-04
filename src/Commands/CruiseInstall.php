<?php

namespace Sfneal\Cruise\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Process\Pipe;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class CruiseInstall extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cruise:install
                            {docker_id : Your docker ID you will be using with your laravel application}
                            {docker_image : Your laravel applications docker image name}
                            {front_end_compiler : Front-end asset bundler}
                            {--ddd : Enable Domain Driven Design application scaffolding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the cruise package and publish files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Artisan::call('vendor:publish', ['--tag' => 'cruise-config']);
        $this->info('Published cruise config files config/cruise.php');

        Artisan::call('vendor:publish', ['--tag' => 'docker']);
        $this->info('Published docker assets to the application root');

        $this->publishDockerAssets();

        $this->renameDockerImages($this->argument('docker_id'), $this->argument('docker_image'));

        if ($this->option('ddd')) {
            $this->enableDDD();
        }

        $this->addComposerCommand('test', 'docker exec -it app vendor/bin/phpunit');
        $this->addComposerScript('start-dev');
        $this->addComposerScript('start-dev-db');
        $this->addComposerScript('start-dev-node');
        $this->addComposerScript('start-test');
        $this->addComposerScript('stop');
        $this->addComposerScript('build');
        $this->info('Published composer scripts for starting/stopping docker services');

        if (! file_exists(base_path('.env.dev')) && file_exists(base_path('.env'))) {
            copy(base_path('.env'), base_path('.env.dev'));
            $this->info('Published missing .env.dev file');
        }
        if (! file_exists(base_path('.env.dev.db')) && file_exists(base_path('.env'))) {
            copy(base_path('.env.dev'), base_path('.env.dev.db'));
            $this->info('Published missing .env.dev.db file');
        }

        return self::SUCCESS;
    }

    private function publishDockerAssets(): void
    {
        if (strtolower($this->argument('front_end_compiler')) == 'webpack') {
            Artisan::call('vendor:publish', ['--tag' => 'docker-webpack']);
        }
        if (strtolower($this->argument('front_end_compiler')) == 'vite') {
            Artisan::call('vendor:publish', ['--tag' => 'docker-vite', '--force' => true]);
        }
        $this->info("Published {$this->argument('front_end_compiler')} Dockerfiles & docker-compose.yml's");
    }

    private function enableDDD(): void
    {
        Artisan::call('vendor:publish', ['--tag' => 'ddd', '--force' => true]);
        $this->info('Published app/App/BaseApplication & bootstrap/app.php');

        // Get Existing directories
        $existing_directories = File::directories(base_path('app'));

        // Create app/App, app/Domain & app/Support
        foreach (['App', 'Domain', 'Support'] as $namespace) {
            $path = base_path("app/$namespace");
            if (! File::isDirectory($path)) {
                File::makeDirectory($path);
            }
        }

        // Move existing directories into app/App
        foreach ($existing_directories as $directory) {
            File::moveDirectory($directory, base_path('app/App').DIRECTORY_SEPARATOR.basename($directory));
        }

        // Add namespacing
        $process = Process::path(base_path())->run('sh vendor/sfneal/cruise/scripts/utils/add-ddd-namespacing.sh');

        if ($process->successful()) {
            $this->info('Added DDD namespacing to composer.json');
        } else {
            $this->fail('Unable to add DDD namespacing to composer.json');
        }
    }

    private function addComposerScript(string $script): void
    {
        $this->addComposerCommand($script, "sh vendor/sfneal/cruise/scripts/runners/$script.sh");
    }

    private function addComposerCommand(string $name, string $command): void
    {
        $process = Process::run([
            'composer', 'config', "scripts.$name", "$command", '--working-dir='.base_path(),
        ]);

        if ($process->successful()) {
            $this->info("Added 'composer $name' command to composer.json");
        }
    }

    private function renameDockerImages(string $docker_id, string $image_name): void
    {
        $og_full_image_name = trim(Process::path(base_path())
            ->run("grep -A 10 'services:' docker-compose.yml | grep -A 1 'app:' | grep 'image:' | awk '{print $2}' | grep -o '^[^:]*'")
            ->output());

        // Linux process
        $process = Process::pipe(function (Pipe $pipe) use ($og_full_image_name, $docker_id, $image_name) {
            [$og_docker_id, $og_image_name] = explode('/', $og_full_image_name);

            $docker_compose_files = [
                'docker-compose.yml',
                'docker-compose-dev.yml',
                'docker-compose-dev-db.yml',
                'docker-compose-dev-node.yml',
                'docker-compose-tests.yml',
            ];

            foreach ($docker_compose_files as $docker_file) {
                // Should work on Linux
                $pipe->path(base_path())->command("sed -i'' 's|$og_docker_id|$docker_id|g' ".$docker_file);
                $pipe->path(base_path())->command(trim("sed -i'' 's|$og_image_name|$image_name|g' ".$docker_file));
            }
        });

        // Mac process - ugly hack
        if (! $process->successful()) {
            $process = Process::pipe(function (Pipe $pipe) use ($og_full_image_name, $docker_id, $image_name) {
                [$og_docker_id, $og_image_name] = explode('/', $og_full_image_name);

                $docker_compose_files = [
                    'docker-compose.yml',
                    'docker-compose-dev.yml',
                    'docker-compose-dev-db.yml',
                    'docker-compose-dev-node.yml',
                    'docker-compose-tests.yml',
                ];

                foreach ($docker_compose_files as $docker_file) {
                    // Should work on Macs
                    $pipe->path(base_path())->command("sed -i '' 's|$og_docker_id|$docker_id|g' ".$docker_file);
                    $pipe->path(base_path())->command(trim("sed -i '' 's|$og_image_name|$image_name|g' ".$docker_file));

                    // I don't care if it works on Windows!
                }
            });
        }

        if ($process->successful()) {
            $this->info("Renamed docker images from {$og_full_image_name} to {$docker_id}/{$image_name}");
            $this->warn('Warning: remember to update the server.hmr.host value in vite.config.js to your app url');
        } else {
            $this->info("Failed to rename docker images from {$og_full_image_name} to {$docker_id}/{$image_name}");
        }
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'docker_id' => function () {
                return text(
                    label: 'Enter your Docker ID:',
                    placeholder: 'E.g. mydockerid',
                    required: true,
                );
            },
            'docker_image' => function () {
                return text(
                    label: 'Enter your Docker image name (recommend using application name):',
                    placeholder: 'E.g. myapplication',
                    required: true,
                );
            },
            'front_end_compiler' => function () {
                return select(
                    label: 'Select Front-end asset compiler',
                    options: ['Webpack', 'Vite'],
                    default: 'Vite',
                );
            },
            'ddd' => function () {
                return confirm(
                    label: 'Do you want to enable Domain Driven Design application scaffolding?',
                    default: false,
                    yes: 'Yes',
                    no: 'No',
                    hint: 'This will separate your application into App, Domain & Support namespaces.'
                );
            },
        ];
    }
}
