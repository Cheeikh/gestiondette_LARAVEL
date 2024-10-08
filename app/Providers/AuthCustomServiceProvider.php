<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\AuthentificationServiceInterface;
use Symfony\Component\Yaml\Yaml;
use Laravel\Passport\Passport;

class AuthCustomServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AuthentificationServiceInterface::class, function ($app) {
            $config = Yaml::parseFile(config_path('auth_services.yaml'));
            $defaultService = $config['auth_services']['default'];
            $serviceClass = $config['auth_services']['services'][$defaultService]['class'];

            return new $serviceClass();
        });
    }

    public function boot()
    {
        // Configuration de Passport
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        // Autres configurations si nécessaires
    }
}
