<?php

namespace App\Events;

use App\Models\Client;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class ClientCreated
{
    use Dispatchable, SerializesModels;

    public $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
