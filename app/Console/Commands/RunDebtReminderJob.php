<?php

namespace App\Console\Commands;

use App\Interfaces\DetteRepositoryInterface;
use Illuminate\Console\Command;
use App\Jobs\SendDebtReminderJob;

class RunDebtReminderJob extends Command
{
    protected $signature = 'job:send-debt-reminder';

    protected $description = 'Run the SendDebtReminderJob manually';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Resolve the detteRepository service
        $detteRepository = app(DetteRepositoryInterface::class);

        // Get the client IDs with unpaid debts
        $clientsWithDebts = $detteRepository->getClientsWithUnpaidDebts();
        $clientIds = $clientsWithDebts->pluck('id')->toArray();

        // Dispatch the job, passing only the client IDs
        SendDebtReminderJob::dispatch($clientIds);

        $this->info('SendDebtReminderJob has been dispatched.');
    }
}
