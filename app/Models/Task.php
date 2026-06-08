<?php
namespace App\Models;

class Task extends BaseModel
{
    protected string $table = 'tasks';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO tasks (title, description, priority, status, due_date, created_by)
            VALUES (:title, :description, :priority, :status, :due_date, :created_by)
        ");
        $stmt->execute([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'priority'    => $data['priority'] ?? 'media',
            'status'      => $data['status'] ?? 'todo',
            'due_date'    => $data['due_date'] ?? null,
            'created_by'  => $data['created_by'] ?? 1,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $existing = $this->findById($id);
        if (!$existing) return false;

        $stmt = $this->db->prepare("
            UPDATE tasks
            SET title=:title, description=:description,
                priority=:priority, status=:status, due_date=:due_date
            WHERE id=:id
        ");
        return $stmt->execute([
            'id'          => $id,
            'title'       => $data['title'] ?? $existing['title'],
            'description' => $data['description'] ?? $existing['description'],
            'priority'    => $data['priority'] ?? $existing['priority'],
            'status'      => $data['status'] ?? $existing['status'],
            'due_date'    => $data['due_date'] ?? $existing['due_date'],
        ]);
    }

    public function moveStatus(int $id, string $status): bool
    {
        $allowed = ['todo', 'progress', 'review', 'done'];
        if (!in_array($status, $allowed)) return false;

        $stmt = $this->db->prepare("UPDATE tasks SET status=:status WHERE id=:id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function getByStatus(string $status): array
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE status=:s ORDER BY 
            FIELD(priority, 'alta', 'media', 'baixa'), due_date ASC");
        $stmt->execute(['s' => $status]);
        return $stmt->fetchAll();
    }

    public function countActive(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM tasks WHERE status != 'done'");
        return (int) $stmt->fetchColumn();
    }
}