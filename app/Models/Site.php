<?php
namespace App\Models;

class Site extends BaseModel
{
    protected string $table = 'sites';
    protected bool $softDeletes = true;
    
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO sites (name, url, description, is_internal, status, category_id)
            VALUES (:name, :url, :description, :is_internal, :status, :category_id)
        ");
        $stmt->execute([
            'name' => $data['name'],
            'url' => $data['url'],
            'description' => $data['description'] ?? null,
            'is_internal' => $data['is_internal'] ?? 1,
            'status' => $data['status'] ?? 'online',
            'category_id' => $data['category_id'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }
    
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE sites
            SET name=:name, url=:url, description=:description,
                is_internal=:is_internal, status=:status, category_id=:category_id
            WHERE id=:id
        ");
        return $stmt->execute([
            'name' => $data['name'],
            'url' => $data['url'],
            'description' => $data['description'] ?? null,
            'is_internal' => $data['is_internal'] ?? 1,
            'status' => $data['status'] ?? 'online',
            'category_id' => $data['category_id'] ?? null,
            'id' => $id
        ]);
    }
    
    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT s.*, c.name as category_name 
            FROM sites s
            LEFT JOIN categories c ON s.category_id = c.id
            WHERE s.deleted_at IS NULL
            ORDER BY s.name
        ");
        return $stmt->fetchAll();
    }

    public function findAllByCategory(?int $categoryId = null): array
    {
        $sql = "
            SELECT s.*, c.name as category_name 
            FROM sites s
            LEFT JOIN categories c ON s.category_id = c.id
            WHERE s.deleted_at IS NULL
        ";
        
        if ($categoryId) {
            $sql .= " AND s.category_id = :category_id";
            $stmt = $this->db->prepare($sql . " ORDER BY s.name");
            $stmt->execute(['category_id' => $categoryId]);
        } else {
            $stmt = $this->db->query($sql . " ORDER BY s.name");
        }
        
        return $stmt->fetchAll();
    }

    public function countByStatus(string $status, ?int $categoryId = null): int
    {
        $sql = "SELECT COUNT(*) FROM sites WHERE status = :status AND deleted_at IS NULL";
        $params = ['status' => $status];

        if ($categoryId) {
            $sql .= " AND category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function search(string $query, int $limit = 20): array
    {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as category_name, 'site' as result_type
            FROM sites s
            LEFT JOIN categories c ON s.category_id = c.id
            WHERE s.deleted_at IS NULL
              AND (s.name LIKE :q OR s.url LIKE :q OR s.description LIKE :q)
            ORDER BY s.name LIMIT :limit
        ");
        $stmt->bindValue('q', "%{$query}%");
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
