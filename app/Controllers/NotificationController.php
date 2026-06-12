<?php
namespace App\Controllers;

use App\Core\{Auth, NotificationService, Request};
use App\Models\Notification;

class NotificationController extends BaseController
{
    public function __construct(
        private Notification $notification,
        private NotificationService $notificationService
    ) {
        parent::__construct();
    }

    public function index(Request $req): void
    {
        $userId = Auth::id();
        if (!$userId) {
            $this->json(['error' => 'Não autenticado.'], 401);
            return;
        }

        $this->notificationService->checkAndGenerate($userId);

        $this->json([
            'notifications' => $this->notification->forUser($userId, 30),
            'unread_count'  => $this->notification->unreadCount($userId),
        ]);
    }

    public function markRead(Request $req, array $params): void
    {
        $userId = Auth::id();
        $this->notification->markRead((int) $params['id'], $userId);
        $this->json(['success' => true, 'unread_count' => $this->notification->unreadCount($userId)]);
    }

    public function markAllRead(Request $req): void
    {
        $userId = Auth::id();
        $this->notification->markAllRead($userId);
        $this->json(['success' => true, 'unread_count' => 0]);
    }

    public function stream(Request $req): void
    {
        $userId = Auth::id();
        if (!$userId) {
            http_response_code(401);
            return;
        }

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        $this->notificationService->checkAndGenerate($userId);
        $count = $this->notification->unreadCount($userId);
        $data = json_encode(['unread_count' => $count, 'time' => date('c')]);

        echo "event: notification\ndata: {$data}\n\n";
        ob_flush();
        flush();
    }
}
