<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ArchivePaidDebtsJob;
use App\Interfaces\ArchiveServiceInterface;
use App\Interfaces\DetteRepositoryInterface;

class TriggerDebtArchivingCommand extends Command
{
    protected $signature = 'debts:archive-paid';
    protected $description = 'Triggers the job that archives all fully paid debts.';

    private $archiveService;
    private $detteRepository;

    public function __construct(ArchiveServiceInterface $archiveService, DetteRepositoryInterface $detteRepository)
    {
        parent::__construct();
        $this->archiveService = $archiveService;
        $this->detteRepository = $detteRepository;
    }

    public function handle()
    {
        $dettesIds = $this->detteRepository->getPaidDebtsIds()->toArray(); // Convert to array
        ArchivePaidDebtsJob::dispatch($dettesIds); // Dispatch with IDs
        $this->info('Archiving job has been dispatched.');
    }
}
