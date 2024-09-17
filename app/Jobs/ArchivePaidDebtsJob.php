<?php

namespace App\Jobs;

use App\Interfaces\ArchiveServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ArchivePaidDebtsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dettesIds; // Only passing IDs, a simple data type

    public function __construct(array $dettesIds)
    {
        $this->dettesIds = $dettesIds; // Accept only IDs to fetch in handle
    }

    public function handle(ArchiveServiceInterface $archiveService)
    {
        // Fetch debts within handle, do not try to serialize full debt models
        $archiveService->archivePaidDebts($this->dettesIds);
    }
}
