<?php
namespace App\Models;

class Notification extends BaseModel
{
    protected string $table = 'notifications';

    public function createForUser(int $userId, string $type, string $title, string $message, ?string $link = null): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, type, title, message, link)
            VALUES (:user_id, :type, :title, :message, :link)
        ");
        $stmt->execute([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'message' => $message,
            'link'    => $link,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function forUser(int $userId, int $limit = 20): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM notifications
            WHERE user_id = :user_id
            ORDER BY created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue('user_id', $userId, \PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function unreadCount(int $userId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND read_at IS NULL
        ");
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function markRead(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE notifications SET read_at = NOW()
            WHERE id = :id AND user_id = :user_id AND read_at IS NULL
        ");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    public function markAllRead(int $userId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE notifications SET read_at = NOW()
            WHERE user_id = :user_id AND read_at IS NULL
        ");
        return $stmt->execute(['user_id' => $userId]);
    }

    public function existsRecent(int $userId, string $type, string $title, int $hours = 24): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM notifications
            WHERE user_id = :user_id AND type = :type AND title = :title
              AND created_at > DATE_SUB(NOW(), INTERVAL :hours HOUR)
        ");
        $stmt->execute(['user_id' => $userId, 'type' => $type, 'title' => $title, 'hours' => $hours]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
