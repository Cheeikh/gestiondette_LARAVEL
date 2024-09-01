<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Vérifiez si l'application est en mode maintenance...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Enregistrer l'autoloader de Composer...
require __DIR__.'/../vendor/autoload.php';

// Démarrer Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// Créer une instance du noyau HTTP et gérer la requête
$kernel = $app->make(Kernel::class);

$response = tap($kernel->handle(
    $request = Request::capture()
))->send();

$kernel->terminate($request, $response);