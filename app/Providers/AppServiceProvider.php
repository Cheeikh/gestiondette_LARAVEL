<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\User;

use App\Observers\ClientObserver;
use App\Observers\UserObserver;

use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;
use App\Repositories\ArticleRepository;
use App\Repositories\DetteRepository;
use App\Repositories\PaiementRepository;

use App\Services\ClientService;
use App\Services\UserService;
use App\Services\ArticleService;
use App\Services\PDFService;
use App\Services\QRCodeService;
use App\Services\DetteService;

use Illuminate\Support\ServiceProvider;

use App\Uploads\Uploads;

use App\Interfaces\AuthentificationServiceInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ClientServiceInterface;
use App\Interfaces\UploadInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\UserServiceInterface;
use App\Interfaces\QRCodeServiceInterface;
use App\Interfaces\PDFServiceInterface;
use App\Interfaces\ArticleRepositoryInterface;
use App\Interfaces\ArticleServiceInterface;
use App\Interfaces\PaiementRepositoryInterface;
use App\Interfaces\DetteRepositoryInterface;
use App\Interfaces\DetteServiceInterface;


class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(ClientServiceInterface::class, ClientService::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(UploadInterface::class, Uploads::class);
        $this->app->bind(QRCodeServiceInterface::class, QRCodeService::class);
        $this->app->bind(PDFServiceInterface::class, PDFService::class);
        $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);
        $this->app->bind(ArticleServiceInterface::class, ArticleService::class);
        $this->app->bind(PaiementRepositoryInterface::class, PaiementRepository::class);
        $this->app->bind(DetteRepositoryInterface::class, DetteRepository::class);
        $this->app->bind(DetteServiceInterface::class, DetteService::class);

        $this->app->singleton('userService', function ($app) {
            return new UserService(
                $app->make(UserRepositoryInterface::class),
                $app->make(AuthentificationServiceInterface::class)
            );
        });

        $this->app->singleton('clientService', function ($app) {
            return new ClientService(
                $app->make(ClientRepositoryInterface::class)
            );
        });

        $this->app->singleton('articleService', function ($app) {
            return new ArticleService(
                $app->make(ArticleRepositoryInterface::class),
            );
        });

        $this->app->singleton('detteService', function ($app) {
            return new DetteService(
                $app->make(DetteRepositoryInterface::class),
                $app->make(ArticleRepositoryInterface::class),
                $app->make(PaiementRepositoryInterface::class),
            );
        });
    }

    public function boot()
    {
        User::observe(UserObserver::class);
        Client::observe(ClientObserver::class);
    }
}
