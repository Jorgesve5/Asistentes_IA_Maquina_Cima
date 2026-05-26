<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

try {
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    // FORCE DEBUG TRUE TO CATCH LIVEWIRE ERRORS
    config(['app.debug' => true]);
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    file_put_contents(__DIR__.'/../storage/logs/fatal_error.txt', "Global Exception in index.php: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    throw $e;
}
