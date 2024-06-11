<?php

namespace Sfneal\Cruise\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Contracts\Console\PromptsForMissingInput;
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
     *
     *
     * @throws Exception
     */
    public function handle(): int
    {
        Artisan::call('vendor:publish --tag=docker');
        $this->info("Published docker assets to the application root");

        $script_path = 'vendor/sfneal/cruise/scripts/runners';
        (new Process(['composer', 'config', 'scripts.start-dev', "sh $script_path/start-dev.sh"]))->run();
        (new Process(['composer', 'config', 'scripts.start-dev-db', "sh $script_path/start-dev-db.sh"]))->run();
        (new Process(['composer', 'config', 'scripts.start-dev-node', "sh $script_path/start-dev-node.sh"]))->run();
        (new Process(['composer', 'config', 'scripts.start-test', "sh $script_path/start-test.sh"]))->run();
        (new Process(['composer', 'config', 'scripts.stop', "sh $script_path/stop.sh"]))->run();
        (new Process(['composer', 'config', 'scripts.build', "sh $script_path/build.sh"]))->run();
        $this->info("Published composer scripts for starting/stopping docker services");

        copy(base_path('.env'), base_path('.env.dev'));
        copy(base_path('.env'), base_path('.env.dev.db'));

        return 1;
    }
}
