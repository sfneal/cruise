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
            'vite.config.js',
            '.dockerignore',
        ];
        $this->info('Removing docker related files...');
        foreach ($docker_files as $file) {
            $file_path = base_path($file);
            if (File::exists($file_path)) {
                File::delete($file_path);
                $this->info("Removed {$file} from application root");
            }
        }
        self::deleteFileTree(base_path('docker'));
        $this->info("Removed 'docker' directory from application root");

        // Remove compose scripts
        $this->removeComposerScript('test');
        $this->removeComposerScript('start-dev');
        $this->removeComposerScript('start-dev-db');
        $this->removeComposerScript('start-dev-node');
        $this->removeComposerScript('start-test');
        $this->removeComposerScript('stop');
        $this->removeComposerScript('build');
        $this->info('Removed composer scripts for starting/stopping docker services');

        return self::SUCCESS;
    }

    private function removeComposerScript(string $script): void
    {
        (new Process(['composer', 'config', '--unset', "scripts.$script", '--working-dir='.base_path()]))->run();

        $this->info("Removed 'composer $script' command to composer.json");
    }

    private static function deleteFileTree(string $directory): void
    {
        foreach (array_diff(scandir($directory), ['.', '..']) as $file) {
            is_dir("$directory/$file")
                ? self::deleteFileTree("$directory/$file")
                : unlink("$directory/$file");
        }

        rmdir($directory);
    }
}
