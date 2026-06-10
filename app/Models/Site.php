<?php
namespace App\Models;

class Site extends BaseModel
{
    protected string $table = 'sites';
    
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
        ";
        
        if ($categoryId) {
            $sql .= " WHERE s.category_id = :category_id";
            $stmt = $this->db->prepare($sql . " ORDER BY s.name");
            $stmt->execute(['category_id' => $categoryId]);
        } else {
            $stmt = $this->db->query($sql . " ORDER BY s.name");
        }
        
        return $stmt->fetchAll();
    }
}
