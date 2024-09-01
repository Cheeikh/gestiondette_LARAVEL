<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
{
    $this->registerPolicies();

    // Supprimez ou commentez la ligne Passport::routes();
    // Passport::routes();

    // Si vous voulez personnaliser la durée de vie des tokens, vous pouvez utiliser ces lignes :
    // Passport::tokensExpireIn(now()->addDays(15));
    // Passport::refreshTokensExpireIn(now()->addDays(30));
    // Passport::personalAccessTokensExpireIn(now()->addMonths(6));
}
}
