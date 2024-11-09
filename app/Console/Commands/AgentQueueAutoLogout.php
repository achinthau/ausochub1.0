<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AgentQueueAutoLogout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:agent-queue-autologout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (config('auso.auto_logout_queue')) {
            return Command::SUCCESS;
        }
        print_r('auto_logout_queue false');

        return Command::INVALID;
    }
}
