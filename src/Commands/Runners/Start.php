<?php

namespace Sfneal\Cruise\Commands\Runners;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Process\Process;

class Start extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cruise:start {env=dev}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the docker container services';

    /**
     * Execute the console command.
     *
     *
     * @throws Exception
     */
    public function handle(): int
    {
        return (new Process(['bash', '../../../scripts/runners/start-' . $this->option('env') . '.sh']))->run();
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'env' => ['Which docker environment would you like to start?', 'E.g. dev, dev-db, dev-node or tests'],
        ];
    }
}
