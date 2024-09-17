<?php

namespace App\Http\Controllers;

use App\Interfaces\NotificationServiceInterface;
use App\Models\Client;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function sendDebtReminder($clientId)
    {
        $client = Client::findOrFail($clientId);
        $response = $this->notificationService->sendDebtReminderToClient($client);

        return response()->json($response);
    }

    public function sendDebtReminderToAllClients(Request $request)
    {
        $request->validate([
            'clientIds' => 'nullable|array',
            'clientIds.*' => 'integer|exists:clients,id',
        ]);

        $clientIds = $request->input('clientIds');
        $response = $this->notificationService->sendDebtRemindersToAllClients($clientIds);
        return response()->json($response);
    }

    public function sendMessageToClients(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'clientIds' => 'nullable|array',
            'clientIds.*' => 'integer|exists:clients,id',
        ]);

        $message = $request->input('message');
        $clientIds = $request->input('clientIds'); // This can be an array of client IDs or null to send to all clients

        $response = $this->notificationService->sendCustomMessageToClients($clientIds, $message);
        return response()->json($response);
    }
}
