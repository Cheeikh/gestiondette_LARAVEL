<?php

namespace App\Services;

use App\Interfaces\SmsServiceInterface;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService implements SmsServiceInterface
{
    protected $twilio;
    protected $from;

    public function __construct(array $config)
    {
        $sid = $config['sid'];
        $token = $config['token'];
        $this->from = $config['from'];

        $this->twilio = new Client($sid, $token);
    }

    public function sendSms($to, $message)
    {
        try {
            $this->twilio->messages->create($to, [
                'from' => $this->from,
                'body' => $message,
            ]);
            Log::info("SMS sent successfully to: {$to}");
        } catch (\Exception $e) {
            Log::error("Failed to send SMS via Twilio to {$to}: {$e->getMessage()}");
            throw $e;
        }
    }
}
