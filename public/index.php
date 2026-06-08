<?php
declare(strict_types=1);

spl_autoload_register(function (string $class) {
    $path = __DIR__ . '/../' . str_replace(['App\\', '\\'], ['app/', '/'], $class) . '.php';
    if (file_exists($path)) require $path;
});

$appConfig = require __DIR__ . '/../config/app.php';
date_default_timezone_set($appConfig['timezone']);

$router  = new \App\Core\Router();
$request = new \App\Core\Request();

// =============================================
// ROTAS — Páginas (HTML)
// =============================================
$router->add('GET', '/',          'DashboardController@index');
$router->add('GET', '/dashboard', 'DashboardController@index');

$router->dispatch($request);