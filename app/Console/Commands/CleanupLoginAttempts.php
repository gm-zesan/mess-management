<?php

namespace App\Console\Commands;

use App\Services\LoginAttemptService;
use Illuminate\Console\Command;

class CleanupLoginAttempts extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'auth:cleanup-login-attempts
                            {--days=90 : Number of days to retain}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Clean up old login attempts from the database';

    /**
     * Execute the console command.
     */
    public function handle(LoginAttemptService $attemptService): int
    {
        $days = (int) $this->option('days');
        $deleted = $attemptService->cleanupOldAttempts();

        $this->info("Deleted {$deleted} login attempts older than {$days} days.");

        return Command::SUCCESS;
    }
}
