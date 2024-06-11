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

        $docker_compose_files = [
            'docker-compose.yml',
            'docker-compose-dev.yml',
            'docker-compose-dev-db.yml',
            'docker-compose-dev-node.yml',
        ];

        // Find & replace app name
        foreach ($docker_compose_files as $file) {
            (new Process([
                'perl',
                '-pi',
                '"s/cruise-boilerplate/' . config('app.name') . '/g"',
                "./{$file}",
            ]))->run();
        }

        return 1;
    }
}
