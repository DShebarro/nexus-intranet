<?php
namespace App\Models;

class ActivityLog extends BaseModel
{
    protected string $table = 'activity_logs';

    public function log(string $type, string $description): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO activity_logs (type, description, ip_address, user_agent)
            VALUES (:type, :description, :ip, :ua)
        ");
        $stmt->execute([
            'type'        => $type,
            'description' => $description,
            'ip'          => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'ua'          => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function recent(int $limit = 50): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT :limit
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
}
