<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\TwilioService;
use App\Services\InfoBipService;
use App\Interfaces\SmsServiceInterface;
use Symfony\Component\Yaml\Yaml;

class SmsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(SmsServiceInterface::class, function () {

            $config = Yaml::parseFile(base_path('config/sms.yaml'));

            $defaultProvider = $config['sms']['default'];
            $providers = $config['sms']['providers'];

            switch ($defaultProvider) {
                case 'twilio':
                    return new TwilioService($providers['twilio']);
                case 'infobip':
                    return new InfoBipService($providers['infobip']);
                default:
                    throw new \Exception('Unsupported SMS provider');
            }
        });
    }
}
