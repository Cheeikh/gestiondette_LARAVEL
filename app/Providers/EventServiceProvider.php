<?php

namespace App\Providers;

use App\Events\ClientCreated;
use App\Events\UserCreated;
use App\Events\UserRegistering;
use App\Listeners\HandleUserCreated;
use App\Listeners\HandleUserRegistration;
use App\Listeners\SendClientWelcomeEmail;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserCreated::class => [
            SendWelcomeEmail::class,
            HandleUserCreated::class,
        ],
        UserRegistering::class => [
            HandleUserRegistration::class,
        ],
        ClientCreated::class => [
            SendClientWelcomeEmail::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
