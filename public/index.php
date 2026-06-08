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

// Rotas das páginas
$router->add('GET', '/',          'DashboardController@index');
$router->add('GET', '/dashboard', 'DashboardController@index');
$router->add('GET', '/tasks',     'TaskController@index');

// Rotas da API
$router->add('POST',   '/api/tasks',         'TaskController@store');
$router->add('PATCH',  '/api/tasks/{id}/move', 'TaskController@move');
$router->add('DELETE', '/api/tasks/{id}',    'TaskController@destroy');

$router->dispatch($request);
