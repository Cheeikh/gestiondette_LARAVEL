<?php

namespace App\Providers;

use App\Interfaces\AuthentificationServiceInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ClientServiceInterface;
use App\Interfaces\UploadInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserServiceInterface;
use App\Models\Client;
use App\Observers\ClientObserver;
use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;
use App\Services\ClientService;
use App\Services\UserService;
use App\Uploads\Uploads;
use Illuminate\Support\ServiceProvider;
use App\Observers\UserObserver;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(ClientServiceInterface::class, ClientService::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(UploadInterface::class, Uploads::class);

        $this->app->singleton('userService', function ($app) {
            return new UserService(
                $app->make(UserRepositoryInterface::class),
                $app->make(AuthentificationServiceInterface::class)
            );
        });

        $this->app->singleton('clientService', function ($app) {
            return new ClientService(
                $app->make(ClientRepositoryInterface::class),
                $app->make(UserService::class)
            );
        });
    }

    public function boot()
    {
        User::observe(UserObserver::class);
        Client::observe(ClientObserver::class);
    }
}
