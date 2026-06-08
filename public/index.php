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
$router->add('GET', '/contracts', 'ContractController@index');
$router->add('GET', '/sites',     'SiteController@index');
$router->add('GET', '/logs',      'LogController@index');
$router->add('GET', '/chat',      'ChatController@index');

// Rotas da API
$router->add('POST',   '/api/tasks',         'TaskController@store');
$router->add('PUT',    '/api/tasks/{id}',    'TaskController@update');
$router->add('PATCH',  '/api/tasks/{id}/move', 'TaskController@move');
$router->add('DELETE', '/api/tasks/{id}',    'TaskController@destroy');
$router->add('POST',   '/api/contracts',     'ContractController@store');
$router->add('PUT',    '/api/contracts/{id}', 'ContractController@update');
$router->add('DELETE', '/api/contracts/{id}', 'ContractController@destroy');
$router->add('POST',   '/api/sites',         'SiteController@store');
$router->add('PUT',    '/api/sites/{id}',    'SiteController@update');
$router->add('DELETE', '/api/sites/{id}',    'SiteController@destroy');
$router->add('DELETE', '/api/logs',          'LogController@clear');
$router->add('GET',    '/api/chats/{slug}/messages', 'ChatController@getMessages');
$router->add('POST',   '/api/chats/{slug}/messages', 'ChatController@storeMessage');

$router->dispatch($request);
