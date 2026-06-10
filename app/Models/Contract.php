<?php
namespace App\Models;

class Contract extends BaseModel
{
    protected string $table = 'contracts';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO contracts (code, partner, object, value, status, end_date, category_id)
            VALUES (:code, :partner, :object, :value, :status, :end_date, :category_id)
        ");
        $stmt->execute([
            'code' => $data['code'],
            'partner' => $data['partner'],
            'object' => $data['object'],
            'value' => $data['value'],
            'status' => $data['status'] ?? 'vigente',
            'end_date' => $data['end_date'],
            'category_id' => $data['category_id'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE contracts
            SET code=:code, partner=:partner, object=:object,
                value=:value, status=:status, end_date=:end_date, category_id=:category_id
            WHERE id=:id
        ");
        return $stmt->execute([
            'code' => $data['code'],
            'partner' => $data['partner'],
            'object' => $data['object'],
            'value' => $data['value'],
            'status' => $data['status'],
            'end_date' => $data['end_date'],
            'category_id' => $data['category_id'] ?? null,
            'id' => $id
        ]);
    }

    public function getTotalValue(?int $categoryId = null): float
    {
        $sql = "SELECT SUM(value) FROM contracts";
        if ($categoryId) {
            $sql .= " WHERE category_id = :category_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['category_id' => $categoryId]);
        } else {
            $stmt = $this->db->query($sql);
        }
        return (float) ($stmt->fetchColumn() ?? 0);
    }

    public function getExpiringThisMonth(?int $categoryId = null): array
    {
        $sql = "
            SELECT c.*, cat.name as category_name 
            FROM contracts c
            LEFT JOIN categories cat ON c.category_id = cat.id
            WHERE (end_date BETWEEN CURDATE() AND LAST_DAY(CURDATE())
               OR status IN ('em_renovacao','vencido'))
        ";
        
        if ($categoryId) {
            $sql .= " AND c.category_id = :category_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['category_id' => $categoryId]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }
    
    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT c.*, cat.name as category_name 
            FROM contracts c
            LEFT JOIN categories cat ON c.category_id = cat.id
            ORDER BY c.id DESC
        ");
        return $stmt->fetchAll();
    }

    public function findAllByCategory(?int $categoryId = null): array
    {
        $sql = "
            SELECT c.*, cat.name as category_name 
            FROM contracts c
            LEFT JOIN categories cat ON c.category_id = cat.id
        ";
        
        if ($categoryId) {
            $sql .= " WHERE c.category_id = :category_id";
            $stmt = $this->db->prepare($sql . " ORDER BY c.id DESC");
            $stmt->execute(['category_id' => $categoryId]);
        } else {
            $stmt = $this->db->query($sql . " ORDER BY c.id DESC");
        }
        
        return $stmt->fetchAll();
    }
}
