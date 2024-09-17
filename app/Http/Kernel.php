<?php

namespace App\Http;

use App\Http\Middleware\FormatJsonResponse;
use App\Interfaces\ArchiveServiceInterface;
use App\Interfaces\DetteRepositoryInterface;
use App\Interfaces\SmsServiceInterface;
use App\Jobs\ArchivePaidDebtsJob;
use App\Jobs\SendDebtReminderJob;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Console\Scheduling\Schedule;

class Kernel extends HttpKernel
{
    /**
     * Les middlewares globaux pour l'application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * Les groupes de middleware pour l'application.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\FormatJsonResponse::class,  // Formatage des réponses JSON pour les API
        ],
    ];

    /**
     * Les middlewares de route individuels pour l'application.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
    ];

    /**
     * Planification des tâches.
     *
     * @param Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function() {
            ArchivePaidDebtsJob::dispatch(app()->make(ArchiveServiceInterface::class));
        })->dailyAt('00:00');

        $schedule->call(function() {
            SendDebtReminderJob::dispatch(
                app()->make(SmsServiceInterface::class),
                app()->make(DetteRepositoryInterface::class));
        })->fridays()->at('14:00');
    }

}
