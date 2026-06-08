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

// Rotas temporárias para teste
$router->add('GET', '/', function() {
    echo "Nexus Intranet - Funcionando! 🚀";
});

$router->dispatch($request);