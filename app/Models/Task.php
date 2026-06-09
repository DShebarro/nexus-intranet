<?php
namespace App\Models;

class Task extends BaseModel
{
    protected string $table = 'tasks';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO tasks (title, description, priority, status, due_date, created_by, category_id)
            VALUES (:title, :description, :priority, :status, :due_date, :created_by, :category_id)
        ");
        $stmt->execute([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'priority'    => $data['priority'] ?? 'media',
            'status'      => $data['status'] ?? 'todo',
            'due_date'    => $data['due_date'] ?? null,
            'created_by'  => $data['created_by'] ?? 1,
            'category_id' => $data['category_id'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE tasks
            SET title=:title, description=:description,
                priority=:priority, status=:status, 
                due_date=:due_date, category_id=:category_id
            WHERE id=:id
        ");
        return $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'priority' => $data['priority'] ?? 'media',
            'status' => $data['status'] ?? 'todo',
            'due_date' => $data['due_date'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'id' => $id
        ]);
    }

    public function moveStatus(int $id, string $status): bool
    {
        $allowed = ['todo', 'progress', 'review', 'done'];
        if (!in_array($status, $allowed)) return false;

        $stmt = $this->db->prepare("UPDATE tasks SET status=:status WHERE id=:id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function getByStatus(string $status, ?int $categoryId = null): array
    {
        $sql = "
            SELECT t.*, c.name as category_name 
            FROM tasks t
            LEFT JOIN categories c ON t.category_id = c.id
            WHERE t.status=:s
        ";
        
        if ($categoryId) {
            $sql .= " AND t.category_id = :category_id";
            $stmt = $this->db->prepare($sql . " ORDER BY FIELD(priority, 'alta', 'media', 'baixa'), due_date ASC");
            $stmt->execute(['s' => $status, 'category_id' => $categoryId]);
        } else {
            $stmt = $this->db->prepare($sql . " ORDER BY FIELD(priority, 'alta', 'media', 'baixa'), due_date ASC");
            $stmt->execute(['s' => $status]);
        }
        
        return $stmt->fetchAll();
    }

    public function countActive(?int $categoryId = null): int
    {
        $sql = "SELECT COUNT(*) FROM tasks WHERE status != 'done'";
        if ($categoryId) {
            $sql .= " AND category_id = :category_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['category_id' => $categoryId]);
        } else {
            $stmt = $this->db->query($sql);
        }
        return (int) $stmt->fetchColumn();
    }
    
    public function findAllByCategory(?int $categoryId = null): array
    {
        $sql = "
            SELECT t.*, c.name as category_name 
            FROM tasks t
            LEFT JOIN categories c ON t.category_id = c.id
        ";
        
        if ($categoryId) {
            $sql .= " WHERE t.category_id = :category_id";
            $stmt = $this->db->prepare($sql . " ORDER BY t.id DESC");
            $stmt->execute(['category_id' => $categoryId]);
        } else {
            $stmt = $this->db->query($sql . " ORDER BY t.id DESC");
        }
        
        return $stmt->fetchAll();
    }
}
