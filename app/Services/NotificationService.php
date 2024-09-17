<?php

namespace App\Services;

use App\Interfaces\NotificationServiceInterface;
use App\Interfaces\SmsServiceInterface;
use App\Notifications\CustomMessageNotification;
use App\Notifications\DebtReminderNotification;
use App\Interfaces\DetteRepositoryInterface;
use App\Models\Client;

class NotificationService implements NotificationServiceInterface
{
    protected $smsService;
    protected $detteRepository;

    public function __construct(SmsServiceInterface $smsService, DetteRepositoryInterface $detteRepository)
    {
        $this->smsService = $smsService;
        $this->detteRepository = $detteRepository;
    }

    public function sendDebtReminderToClient(Client $client)
    {
        $totalDue = $this->calculateTotalDue($client);

        if ($totalDue > 0) {
            $notification = new DebtReminderNotification($client, $totalDue);

            $client->notify($notification);

            return ['message' => 'Rappel de dette envoyé avec succès.'];
        }

        return ['message' => 'Aucune dette en cours.'];
    }


    public function sendDebtRemindersToAllClients($clientIds = null)
    {
        if (is_null($clientIds)) {
            $clients = $this->detteRepository->getClientsWithUnpaidDebts();
        } else {
            $clients = Client::whereIn('id', $clientIds)->get();
        }

        foreach ($clients as $client) {
            $this->sendDebtReminderToClient($client);
        }

        return ['message' => 'Rappels de dette envoyés aux clients concernés.'];
    }

    protected function calculateTotalDue(Client $client)
    {
        $totalDue = 0;

        foreach ($client->dettes as $dette) {
            $paidAmount = $dette->paiements->sum('montant');
            $dueAmount = $dette->montant - $paidAmount;
            $totalDue += max(0, $dueAmount);
        }

        return $totalDue;
    }

    public function sendCustomMessageToClients($clientIds, $message)
    {
        $clients = $clientIds ? Client::whereIn('id', $clientIds)->get() : Client::all();

        foreach ($clients as $client) {
            $notification = new CustomMessageNotification($message);
            $client->notify($notification);
        }

        return ['message' => 'Messages personnalisés envoyés aux clients spécifiés.'];
    }

}
