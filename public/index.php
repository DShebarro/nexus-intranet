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
$router->add('GET', '/chat',      'ChatController@index');
$router->add('GET', '/logs',      'LogController@index');

// Rotas da API - Tasks
$router->add('POST',   '/api/tasks',              'TaskController@store');
$router->add('PUT',    '/api/tasks/{id}',         'TaskController@update');
$router->add('PATCH',  '/api/tasks/{id}/move',    'TaskController@move');
$router->add('DELETE', '/api/tasks/{id}',         'TaskController@destroy');
$router->add('GET',    '/api/tasks',              'TaskController@apiList');

// Rotas da API - Contracts
$router->add('GET',    '/api/contracts',          'ContractController@apiList');
$router->add('POST',   '/api/contracts',          'ContractController@store');
$router->add('PUT',    '/api/contracts/{id}',     'ContractController@update');
$router->add('DELETE', '/api/contracts/{id}',     'ContractController@destroy');

// Rotas da API - Sites
$router->add('GET',    '/api/sites',              'SiteController@apiList');
$router->add('POST',   '/api/sites',              'SiteController@store');
$router->add('PUT',    '/api/sites/{id}',         'SiteController@update');
$router->add('DELETE', '/api/sites/{id}',         'SiteController@destroy');

// Rotas da API - Categories
$router->add('GET',    '/api/categories',         'CategoryController@index');
$router->add('POST',   '/api/categories',         'CategoryController@store');

// Rotas da API - Logs
$router->add('GET',    '/api/logs',               'LogController@apiList');
$router->add('DELETE', '/api/logs',               'LogController@clear');

// Rotas da API - Chat
$router->add('GET',    '/api/chats/{slug}/messages', 'ChatController@getMessages');
$router->add('POST',   '/api/chats/{slug}/messages', 'ChatController@storeMessage');

$router->dispatch($request);
