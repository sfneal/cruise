<?php

namespace Sfneal\Cruise\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Process\Pipe;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

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
                            {docker_image : Your laravel applications docker image name}';

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

        $this->renameDockerImages($this->argument('docker_id'), $this->argument('docker_image'));

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

    private function addComposerScript(string $script): void
    {
        $script_path = 'vendor/sfneal/cruise/scripts/runners';

        $process = Process::run([
            'composer', 'config', "scripts.$script", "sh $script_path/$script.sh", '--working-dir='.base_path(),
        ]);

        if ($process->successful()) {
            $this->info("Added 'composer $script' command to composer.json");
        }
    }

    private function renameDockerImages(string $docker_id, string $image_name): void
    {
        $og_full_image_name = trim(Process::path(base_path())
            ->run("grep -A 10 'services:' docker-compose.yml | grep -A 1 'app:' | grep 'image:' | awk '{print $2}' | grep -o '^[^:]*'")
            ->output());

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
                $pipe->command("sed -i '' 's|$og_docker_id|$docker_id|g' ".base_path($docker_file));
                $pipe->command(trim("sed -i '' 's|$og_image_name|$image_name|g' ".base_path($docker_file)));
            }
        });

        if ($process->successful()) {
            $this->info("Renamed docker images from {$og_full_image_name} to {$docker_id}/{$image_name}");
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
        ];
    }
}
