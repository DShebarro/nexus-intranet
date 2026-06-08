<?php
namespace App\Controllers;

use App\Models\ActivityLog;

abstract class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . "/../Views/layout/header.php";
        require __DIR__ . "/../Views/{$view}.php";
        require __DIR__ . "/../Views/layout/footer.php";
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }

    protected function log(string $type, string $desc): void
    {
        (new ActivityLog())->log($type, $desc);
    }
}
