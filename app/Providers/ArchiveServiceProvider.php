<?php

namespace App\Providers;

use App\Interfaces\DetteRepositoryInterface;
use App\Services\DetteService;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Yaml\Yaml;
use App\Interfaces\ArchiveServiceInterface;

class ArchiveServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ArchiveServiceInterface::class, function ($app) {

            $config = Yaml::parseFile(config_path('archive_services.yaml'));

            $defaultService = $config['archive_services']['default'];

            $serviceClass = $config['archive_services']['services'][$defaultService]['class'];

            $detteRepository = $app->make(DetteRepositoryInterface::class);
            $detteService = $app->make(DetteService::class);

            return new $serviceClass($detteRepository, $detteService);
        });
    }
}
