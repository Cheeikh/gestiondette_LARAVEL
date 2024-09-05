<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Remplacez Passport::routes() par ceci :
        Passport::tokensCan([
            'access-api' => 'Access API',
        ]);

        Passport::setDefaultScope([
            'access-api',
        ]);
    }
}