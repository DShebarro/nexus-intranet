<?php
namespace App\Models;

class Site extends BaseModel
{
    protected string $table = 'sites';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO sites (name, url, description, is_internal, status)
            VALUES (:name, :url, :description, :is_internal, :status)
        ");
        $stmt->execute([
            'name'        => $data['name'],
            'url'         => $data['url'],
            'description' => $data['description'] ?? null,
            'is_internal' => (int) ($data['is_internal'] ?? 1),
            'status'      => $data['status'] ?? 'online',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE sites
            SET name=:name, url=:url, description=:description,
                is_internal=:is_internal, status=:status
            WHERE id=:id
        ");
        return $stmt->execute([
            'id'          => $id,
            'name'        => $data['name'],
            'url'         => $data['url'],
            'description' => $data['description'] ?? null,
            'is_internal' => (int) ($data['is_internal'] ?? 1),
            'status'      => $data['status'] ?? 'online',
        ]);
    }
}
