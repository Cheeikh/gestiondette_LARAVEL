<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\UserServiceInterface;
use App\Services\UserService;
use App\Interfaces\ClientServiceInterface;
use App\Services\ClientService;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\ClientRepository;
use App\Interfaces\ClientRepositoryInterface;
use App\Uploads\UploadInterface;
use App\Uploads\Uploads;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(ClientServiceInterface::class, ClientService::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(UploadInterface::class, Uploads::class);
    }

    public function boot()
    {
        // Autres configurations si nécessaire
    }
}
