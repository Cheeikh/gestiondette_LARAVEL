<?php

namespace App\Jobs;

use App\Models\Dette;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Notifications\DebtDueNotification;
use Illuminate\Support\Facades\Log;

class SendPaymentRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Log::info('Starting to send payment reminders.');

        $dueDettes = Dette::with(['client', 'paiements'])
            ->whereDate('date_echeance', '<', now())
            ->get();

        foreach ($dueDettes as $dette) {
            $totalDue = $this->calculateTotalDue($dette->client);
            if ($totalDue > 0) {
                $notification = new DebtDueNotification($dette, $totalDue);
                $dette->client->notify($notification);
                Log::info("Notification sent for debt ID: {$dette->id} to client ID: {$dette->client->id}");
            } else {
                Log::info("No outstanding amount for debt ID: {$dette->id}");
            }
        }

        Log::info('Completed sending payment reminders.');
    }

    protected function calculateTotalDue($client)
    {
        $totalDue = 0;
        foreach ($client->dettes as $dette) {
            $paidAmount = $dette->paiements->sum('montant');
            $dueAmount = $dette->montant - $paidAmount;
            $totalDue += ($dueAmount > 0) ? $dueAmount : 0;
        }
        return $totalDue;
    }
}
