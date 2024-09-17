<?php

namespace App\Services;

use App\Interfaces\SmsServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class InfoBipService implements SmsServiceInterface
{
    protected $client;
    protected $apiKey;
    protected $from;
    protected $baseUrl;

    public function __construct(array $config)
    {
        $this->apiKey = $config['api_key'];
        $this->from = $config['from'];
        $this->baseUrl = $config['base_url'];

        $this->client = new Client();
    }

    public function sendSms($to, $message)
    {
        try {
            $body = [
                'messages' => [
                    [
                        'destinations' => [
                            ['to' => $to]
                        ],
                        'from' => $this->from,
                        'text' => $message
                    ]
                ]
            ];

            $response = $this->client->post($this->baseUrl, [
                'headers' => [
                    'Authorization' => "App {$this->apiKey}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $body,
            ]);

            if ($response->getStatusCode() === 200) {
                Log::info("SMS sent successfully via InfoBip to: {$to}");
            } else {
                Log::warning("Received non-200 response status from InfoBip while sending SMS to {$to}");
            }
        } catch (RequestException $e) {
            Log::error("Failed to send SMS via InfoBip to {$to}: {$e->getMessage()}");
            throw $e;
        }
    }
}
