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

// Apache en Windows (XAMPP) atiende con varios hilos que comparten el mismo entorno del proceso. Si Laravel
// carga el .env con putenv() (el valor por defecto), dos pedidos simultáneos se pisan: uno cree que APP_KEY
// ya está definida, no la carga y la request arranca como "production"/sqlite -> 500 intermitente que
// desaparece al recargar. Con putenv desactivado las variables quedan solo en $_ENV/$_SERVER (por request).
Illuminate\Support\Env::disablePutenv();

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
