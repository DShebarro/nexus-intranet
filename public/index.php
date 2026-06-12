<?php
declare(strict_types=1);

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require $autoload;
} else {
    require __DIR__ . '/../app/helpers.php';
    spl_autoload_register(function (string $class) {
        $path = __DIR__ . '/../' . str_replace(['App\\', '\\'], ['app/', '/'], $class) . '.php';
        if (file_exists($path)) require $path;
    });
}

use App\Core\{Env, Router, Request, Container, ExceptionHandler, Auth, Csrf, FileUploadService, NotificationService};
use App\Models\{Task, Contract, Site, Category, ActivityLog, User, Notification, Attachment};

Env::load(__DIR__ . '/..');

$appConfig = require __DIR__ . '/../config/app.php';
date_default_timezone_set($appConfig['timezone']);

ExceptionHandler::register($appConfig['debug']);

Auth::startSession();
Csrf::startSession();

$container = Container::getInstance();
$container->singleton(\PDO::class, fn() => \App\Core\Database::getInstance());
$container->singleton(User::class, fn() => new User());
$container->singleton(Task::class, fn() => new Task());
$container->singleton(Contract::class, fn() => new Contract());
$container->singleton(Site::class, fn() => new Site());
$container->singleton(Category::class, fn() => new Category());
$container->singleton(ActivityLog::class, fn() => new ActivityLog());
$container->singleton(Notification::class, fn() => new Notification());
$container->singleton(Attachment::class, fn() => new Attachment());
$container->singleton(FileUploadService::class, fn() => new FileUploadService());
$container->singleton(NotificationService::class, fn() => new NotificationService(
    $container->make(Notification::class),
    $container->make(User::class)
));

$router = new Router();
$router->setContainer($container);
$request = new Request();

// Auth (público)
$router->group(['middleware' => 'guest'], function (Router $router) {
    $router->add('GET',  '/login', 'AuthController@showLogin');
    $router->add('POST', '/login', 'AuthController@login', ['csrf']);
});

$router->add('POST', '/logout', 'AuthController@logout', ['auth', 'csrf']);

// Rotas protegidas
$router->group(['middleware' => ['auth']], function (Router $router) {

    // Páginas
    $router->add('GET', '/',          'DashboardController@index');
    $router->add('GET', '/dashboard', 'DashboardController@index');
    $router->add('GET', '/tasks',     'TaskController@index');
    $router->add('GET', '/contracts', 'ContractController@index');
    $router->add('GET', '/sites',     'SiteController@index');
    $router->add('GET', '/chat',      'ChatController@index');
    $router->add('GET', '/logs',      'LogController@index');

    // API — Busca global
    $router->add('GET', '/api/search', 'SearchController@index');

    // API — Notificações
    $router->add('GET',   '/api/notifications',              'NotificationController@index');
    $router->add('GET',   '/api/notifications/stream',       'NotificationController@stream');
    $router->add('PATCH', '/api/notifications/{id}/read',    'NotificationController@markRead', ['csrf']);
    $router->add('POST',  '/api/notifications/read-all',    'NotificationController@markAllRead', ['csrf']);

    // API — Anexos
    $router->add('GET',    '/api/attachments',           'AttachmentController@index');
    $router->add('POST',   '/api/attachments',           'AttachmentController@store', ['csrf']);
    $router->add('GET',    '/api/attachments/{id}/download', 'AttachmentController@download');
    $router->add('DELETE', '/api/attachments/{id}',      'AttachmentController@destroy', ['csrf']);

    // API — Exportação CSV
    $router->add('GET', '/api/export/tasks',     'ExportController@tasks');
    $router->add('GET', '/api/export/contracts', 'ExportController@contracts');

    // API — Tasks
    $router->add('GET',    '/api/tasks',              'TaskController@apiList');
    $router->add('POST',   '/api/tasks',              'TaskController@store',       ['csrf']);
    $router->add('PUT',    '/api/tasks/{id}',         'TaskController@update',      ['csrf']);
    $router->add('PATCH',  '/api/tasks/{id}/move',    'TaskController@move',        ['csrf']);
    $router->add('POST',   '/api/tasks/{id}/restore', 'TaskController@restore',     ['csrf']);
    $router->add('DELETE', '/api/tasks/{id}',         'TaskController@destroy',     ['csrf']);

    // API — Contracts
    $router->add('GET',    '/api/contracts',              'ContractController@apiList');
    $router->add('POST',   '/api/contracts',              'ContractController@store',   ['csrf']);
    $router->add('PUT',    '/api/contracts/{id}',         'ContractController@update',  ['csrf']);
    $router->add('POST',   '/api/contracts/{id}/restore', 'ContractController@restore', ['csrf']);
    $router->add('DELETE', '/api/contracts/{id}',         'ContractController@destroy', ['csrf']);

    // API — Sites
    $router->add('GET',    '/api/sites',              'SiteController@apiList');
    $router->add('POST',   '/api/sites',              'SiteController@store',       ['csrf']);
    $router->add('PUT',    '/api/sites/{id}',         'SiteController@update',      ['csrf']);
    $router->add('DELETE', '/api/sites/{id}',         'SiteController@destroy',     ['csrf']);

    // API — Categories
    $router->add('GET',    '/api/categories',         'CategoryController@index');
    $router->add('POST',   '/api/categories',         'CategoryController@store',   ['csrf']);

    // API — Chat
    $router->add('GET',    '/api/chats/{slug}/messages', 'ChatController@getMessages');
    $router->add('POST',   '/api/chats/{slug}/messages', 'ChatController@storeMessage', ['csrf']);

    // API — Auth profile
    $router->add('GET', '/api/profile', 'AuthController@profile');

    // API — Logs (admin only)
    $router->group(['middleware' => ['admin']], function (Router $router) {
        $router->add('GET',    '/api/logs',          'LogController@apiList');
        $router->add('DELETE', '/api/logs',          'LogController@clear', ['csrf']);
        $router->add('GET',    '/api/export/logs',   'ExportController@logs');
    });
});

$router->dispatch($request);
