<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Response;
use App\Models\ActivityLog;

abstract class BaseController
{
    protected Response $response;
    private ?ActivityLog $activityLog = null;

    public function __construct()
    {
        $this->response = new Response();
    }

    protected function render(string $view, array $data = []): void
    {
        $data['currentUser'] = Auth::user();
        extract($data);
        require __DIR__ . "/../Views/layout/header.php";
        require __DIR__ . "/../Views/{$view}.php";
        require __DIR__ . "/../Views/layout/footer.php";
    }

    protected function renderStandalone(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . "/../Views/{$view}.php";
    }

    protected function json(mixed $data, int $status = 200): void
    {
        $this->response->json($data, $status);
    }

    protected function redirect(string $url): void
    {
        $this->response->redirect($url);
    }

    protected function csv(string $content, string $filename): void
    {
        $this->response->csv($content, $filename);
    }

    protected function log(string $type, string $desc): void
    {
        $this->activityLog()->log($type, $desc);
    }

    protected function audit(
        string $type,
        string $description,
        string $action,
        string $entityType,
        int $entityId,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        $this->activityLog()->audit(
            $type, $description, $action, $entityType, $entityId, $oldValues, $newValues
        );
    }

    protected function validate(array $data, array $rules): array
    {
        $errors = \App\Core\Validator::validate($data, $rules);
        if ($errors) {
            throw new \App\Exceptions\ValidationException($errors);
        }
        return $data;
    }

    private function activityLog(): ActivityLog
    {
        return $this->activityLog ??= new ActivityLog();
    }
}
