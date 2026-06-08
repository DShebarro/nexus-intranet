<?php
namespace App\Models;

class Contract extends BaseModel
{
    protected string $table = 'contracts';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO contracts (code, partner, object, value, status, end_date)
            VALUES (:code, :partner, :object, :value, :status, :end_date)
        ");
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE contracts
            SET code=:code, partner=:partner, object=:object,
                value=:value, status=:status, end_date=:end_date
            WHERE id=:id
        ");
        return $stmt->execute([...$data, 'id' => $id]);
    }

    public function getTotalValue(): float
    {
        $stmt = $this->db->query("SELECT SUM(value) FROM contracts");
        return (float) ($stmt->fetchColumn() ?? 0);
    }

    public function getExpiringThisMonth(): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM contracts
            WHERE end_date BETWEEN CURDATE() AND LAST_DAY(CURDATE())
               OR status IN ('em_renovacao','vencido')
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
