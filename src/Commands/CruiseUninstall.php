<?php

namespace Sfneal\Cruise\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class CruiseUninstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cruise:uninstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uninstall the cruise package and remove publish files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Remove config
        if (File::exists(config_path('cruise.php'))) {
            File::delete(config_path('cruise.php'));
            $this->info('Removed cruise config files');
        }

        // Remove docker files
        $docker_files = [
            'changelog.txt',
            'docker-compose.yml',
            'docker-compose-dev.yml',
            'docker-compose-dev-db.yml',
            'docker-compose-dev-node.yml',
            'docker-compose-tests.yml',
            'Dockerfile',
            'Dockerfile.dev',
            'Dockerfile.dev.node',
            'version.txt',
        ];
        $this->info('Removing docker related files...');
        foreach ($docker_files as $file) {
            $file_path = base_path($file);
            if (File::exists($file_path)) {
                File::delete($file_path);
                $this->info("Removed {$file} from application root");
            }
        }

        // Remove compose scripts
        $this->removeComposerScript('start-dev');
        $this->removeComposerScript('start-dev-db');
        $this->removeComposerScript('start-dev-node');
        $this->removeComposerScript('start-test');
        $this->removeComposerScript('stop');
        $this->removeComposerScript('build');
        $this->info('Removed composer scripts for starting/stopping docker services');

        // Remove copied .env files
        if (file_exists(base_path('.env.dev'))) {
            unlink(file_exists(base_path('.env.dev')));
            $this->info('Removed .env.dev file');
        }
        if (file_exists(base_path('.env.dev.db'))) {
            unlink(file_exists(base_path('.env.dev.db')));
            $this->info('Removed .env.dev.db file');
        }

        return self::SUCCESS;
    }

    private function removeComposerScript(string $script): void
    {
        $script_path = 'vendor/sfneal/cruise/scripts/runners';

        (new Process(['composer', 'config', '--unset', "scripts.$script", "sh $script_path/$script.sh"]))->run();

        $this->info("Removed 'composer $script' command to composer.json");
    }
}
