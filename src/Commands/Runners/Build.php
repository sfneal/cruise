<?php

namespace Sfneal\Cruise\Commands\Runners;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Process\Process;

class Build extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cruise:build {env=dev}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build the docker containers';

    /**
     * Execute the console command.
     *
     *
     * @throws Exception
     */
    public function handle(): int
    {

        return (new Process(['bash', '../../../scripts/runners/build.sh', $this->option('env')]))->run();
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'env' => ['Which docker environment would you like to build?', 'E.g. dev, dev-db, dev-node or tests'],
        ];
    }
}
