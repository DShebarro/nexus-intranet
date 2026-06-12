<?php
namespace App\Core;

use App\Models\{Notification, User};
use App\Core\Database;

class NotificationService
{
    public function __construct(
        private Notification $notification,
        private User $user
    ) {}

    public function checkAndGenerate(int $userId): void
    {
        $this->checkTasksDue($userId);
        $this->checkContractsExpiring($userId);
    }

    private function checkTasksDue(int $userId): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT id, title, due_date FROM tasks
            WHERE deleted_at IS NULL AND status != 'done'
              AND due_date IS NOT NULL
              AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)
            ORDER BY due_date ASC LIMIT 10
        ");
        $stmt->execute();
        $tasks = $stmt->fetchAll();

        foreach ($tasks as $task) {
            $title = "Tarefa vencendo: {$task['title']}";
            if ($this->notification->existsRecent($userId, 'task_due', $title)) {
                continue;
            }
            $this->notification->createForUser(
                $userId,
                'task_due',
                $title,
                "Prazo: " . date('d/m/Y', strtotime($task['due_date'])),
                '/tasks'
            );
        }
    }

    private function checkContractsExpiring(int $userId): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT id, code, partner, end_date FROM contracts
            WHERE deleted_at IS NULL
              AND end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
            ORDER BY end_date ASC LIMIT 10
        ");
        $stmt->execute();
        $contracts = $stmt->fetchAll();

        foreach ($contracts as $c) {
            $title = "Contrato vencendo: {$c['code']}";
            if ($this->notification->existsRecent($userId, 'contract_expiring', $title)) {
                continue;
            }
            $this->notification->createForUser(
                $userId,
                'contract_expiring',
                $title,
                "{$c['partner']} — vence em " . date('d/m/Y', strtotime($c['end_date'])),
                '/contracts'
            );
        }
    }

    public function notifyAllUsers(string $type, string $title, string $message, ?string $link = null): void
    {
        $users = $this->user->findAll();
        foreach ($users as $user) {
            $this->notification->createForUser((int) $user['id'], $type, $title, $message, $link);
        }
    }
}
