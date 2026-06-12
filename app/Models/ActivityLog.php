<?php
namespace App\Models;

use App\Core\Auth;

class ActivityLog extends BaseModel
{
    protected string $table = 'activity_logs';

    public function log(string $type, string $description): int
    {
        return $this->audit($type, $description);
    }

    public function audit(
        string $type,
        string $description,
        ?string $action = null,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): int {
        $stmt = $this->db->prepare("
            INSERT INTO activity_logs
                (type, description, ip_address, user_agent, user_id, entity_type, entity_id, action, old_values, new_values)
            VALUES
                (:type, :description, :ip, :ua, :user_id, :entity_type, :entity_id, :action, :old_values, :new_values)
        ");
        $stmt->execute([
            'type'        => $type,
            'description' => $description,
            'ip'          => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'ua'          => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            'user_id'     => Auth::id(),
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'action'      => $action,
            'old_values'  => $oldValues ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
            'new_values'  => $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function recent(int $limit = 50): array
    {
        $stmt = $this->db->prepare("
            SELECT al.*, u.name as user_name
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.id
            ORDER BY al.created_at DESC LIMIT :limit
        ");
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countToday(): int
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) FROM activity_logs WHERE DATE(created_at) = CURDATE()
        ");
        return (int) $stmt->fetchColumn();
    }

    public function clearAll(): bool
    {
        return $this->db->exec("DELETE FROM activity_logs") !== false;
    }

    public function forExport(int $limit = 5000): array
    {
        $stmt = $this->db->prepare("
            SELECT al.*, u.name as user_name, u.email as user_email
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.id
            ORDER BY al.created_at DESC LIMIT :limit
        ");
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
