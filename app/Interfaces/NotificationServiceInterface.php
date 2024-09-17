<?php

namespace App\Interfaces;

use App\Models\Client;

interface NotificationServiceInterface
{
    public function sendDebtReminderToClient(Client $client);
    public function sendCustomMessageToClients($clientIds, $message);
}
