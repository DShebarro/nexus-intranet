<?php
namespace App\Models;

class Category extends BaseModel
{
    protected string $table = 'categories';
    
    public function getByType(string $type): array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE type = :type ORDER BY name");
        $stmt->execute(['type' => $type]);
        return $stmt->fetchAll();
    }
    
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO categories (name, type) 
            VALUES (:name, :type)
        ");
        $stmt->execute([
            'name' => $data['name'],
            'type' => $data['type']
        ]);
        return (int) $this->db->lastInsertId();
    }
    
    public function getWithCounts(string $type): array
    {
        $tableMap = [
            'task' => 'tasks',
            'contract' => 'contracts',
            'site' => 'sites'
        ];
        
        $table = $tableMap[$type];
        $stmt = $this->db->prepare("
            SELECT c.*, COUNT({$table}.id) as item_count 
            FROM categories c
            LEFT JOIN {$table} ON c.id = {$table}.category_id
            WHERE c.type = :type
            GROUP BY c.id
            ORDER BY c.name
        ");
        $stmt->execute(['type' => $type]);
        return $stmt->fetchAll();
    }
}
