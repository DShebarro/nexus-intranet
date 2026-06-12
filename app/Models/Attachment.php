<?php
namespace App\Models;

class Attachment extends BaseModel
{
    protected string $table = 'attachments';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO attachments (entity_type, entity_id, user_id, original_name, stored_name, mime_type, size_bytes)
            VALUES (:entity_type, :entity_id, :user_id, :original_name, :stored_name, :mime_type, :size_bytes)
        ");
        $stmt->execute([
            'entity_type'   => $data['entity_type'],
            'entity_id'     => $data['entity_id'],
            'user_id'       => $data['user_id'] ?? null,
            'original_name' => $data['original_name'],
            'stored_name'   => $data['stored_name'],
            'mime_type'     => $data['mime_type'],
            'size_bytes'    => $data['size_bytes'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function forEntity(string $entityType, int $entityId): array
    {
        $stmt = $this->db->prepare("
            SELECT a.*, u.name as uploader_name
            FROM attachments a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE a.entity_type = :type AND a.entity_id = :id
            ORDER BY a.created_at DESC
        ");
        $stmt->execute(['type' => $entityType, 'id' => $entityId]);
        return $stmt->fetchAll();
    }

    public function findOwned(int $id, ?int $userId): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM attachments WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
