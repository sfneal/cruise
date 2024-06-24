<?php

namespace Sfneal\Cruise\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class CruiseInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cruise:install';

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

        (new Process(['composer', 'config', "scripts.$script", "sh $script_path/$script.sh"]))->run();

        $this->info("Added 'composer $script' command to composer.json");
    }
}
