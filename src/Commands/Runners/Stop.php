<?php

namespace Sfneal\Cruise\Commands\Runners;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Stop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cruise:stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop the docker container services';

    /**
     * Execute the console command.
     *
     *
     * @throws Exception
     */
    public function handle(): int
    {
        return (new Process(['bash', '../../../scripts/runners/stop.sh']))->run();
    }
}
