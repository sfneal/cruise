<?php

namespace Sfneal\Cruise\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateDbInProduction extends Command
{
    public const SUCCESS_MESSAGE = "Running database migrations because the app env is 'production'.";
    public const FAILURE_MESSAGE = "Skipped running database migrations because the app env is NOT 'production'.";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:prod';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the database migrations in production';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (config('app.env') == 'production') {
            $this->info("Running database migrations because the app env is 'production'.");
            Artisan::call('migrate --force');

            return self::SUCCESS;
        }

        $this->info("Skipped running database migrations because the app env is NOT 'production'.");

        return self::FAILURE;
    }
}
