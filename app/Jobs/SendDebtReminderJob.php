<?php

namespace App\Jobs;

use App\Interfaces\SmsServiceInterface;
use App\Interfaces\DetteRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDebtReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $clientIds;

    public function __construct(array $clientIds)
    {
        $this->clientIds = $clientIds; // Pass only the necessary data
    }

    public function handle()
    {
        // Resolve the services within the job
        $smsService = app(SmsServiceInterface::class);
        $detteRepository = app(DetteRepositoryInterface::class);

        // Fetch clients by the IDs that were passed
        $clientsWithDebts = $detteRepository->getClientsByIds($this->clientIds);
        Log::info($clientsWithDebts);

        foreach ($clientsWithDebts as $client) {
            foreach ($client->dettes as $dette) {
                // Calculer la dette réelle (montant de la dette moins les paiements)
                $totalDebt = $dette->montant;
                $totalPayments = $dette->paiements->sum('montant'); // Somme des paiements
                $realDebt = $totalDebt - $totalPayments; // Dette réelle après paiement

                if ($realDebt > 0) { // Si une dette reste à payer
                    $message = "Dear {$client->nom}, your remaining unpaid debt is: $realDebt. Please pay your debt as soon as possible.";

                    // Send the SMS via Twilio or InfoBip
                    $smsService->sendSms($client->telephone, $message);
                }
            }
        }
    }
}
