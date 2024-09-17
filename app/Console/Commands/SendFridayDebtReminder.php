<?php

namespace App\Console\Commands;

use App\Jobs\SendDebtReminderJob;
use Illuminate\Console\Command;

class SendFridayDebtReminder extends Command
{
    protected $signature = 'debt:send-sms';
    protected $description = 'Envoyer un rappel SMS pour les dettes chaque vendredi à 14h';

    public function handle()
    {
        // Dispatch du job pour envoyer les SMS
        SendDebtReminderJob::dispatch();
        $this->info('Rappel de dettes envoyé avec succès.');
    }
}
