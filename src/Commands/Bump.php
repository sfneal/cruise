<?php

namespace Sfneal\Cruise\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Process;
use function Laravel\Prompts\select;

class Bump extends Command implements PromptsForMissingInput
{
    const TYPES = ['major', 'minor', 'patch'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bump
                            {type : major, minor or patch version bump}
                            {--commit : commit the updated version files to git}
                            {--no-commit : disabling commiting the updated version files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bump the application to the next major, minor or patch version';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Run bump command
        $bumpProcess = Process::path(base_path())->run([
            'bash', $this->getScriptPath('bump.sh'),
            '--' . $this->argument('type')
        ]);

        /// Display output in the console
        $message = $bumpProcess->output();
        $this->info($message);

        // Exit process if bump failed or the 'commit' option is NOT enabled
        if ($bumpProcess->failed() || $this->isCommitDisabled()) {
            return $bumpProcess->exitCode();
        }

        // Run the commit process
        $commitProcess = Process::path(base_path())->run([
            'bash', $this->getScriptPath('commit.sh'),
            $message
        ]);

        if ($commitProcess->failed()) {
            return $commitProcess->exitCode();
        }

        return $bumpProcess->successful() && $commitProcess->successful() ? 1 : 0;
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'type' => fn () => select(
                label: 'Which semver segment would you like to bump?',
                options: self::TYPES,
                default: 'patch',
                hint: 'E.g. major, minor or patch',
            ),
        ];
    }

    private function getScriptPath(string $script): string
    {
        return base_path("vendor/sfneal/cruise/scripts/version/$script");
    }

    private function isCommitEnabled(): bool
    {
        return $this->option('commit') || ! $this->option('no-commit');
    }

    private function isCommitDisabled(): bool
    {
        return ! $this->option('commit') || $this->option('no-commit');
    }
}
