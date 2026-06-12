<?php
namespace App\Models;

use App\Core\Database;
use PDO;

abstract class BaseModel
{
    protected PDO    $db;
    protected string $table;
    protected bool   $softDeletes = false;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function notDeletedClause(string $alias = ''): string
    {
        if (!$this->softDeletes) {
            return '';
        }
        $col = $alias ? "{$alias}.deleted_at" : 'deleted_at';
        return " AND {$col} IS NULL";
    }

    public function findAll(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1" . $this->notDeletedClause() . " ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function findById(int $id, bool $withTrashed = false): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        if ($this->softDeletes && !$withTrashed) {
            $sql .= " AND deleted_at IS NULL";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function delete(int $id): bool
    {
        if ($this->softDeletes) {
            return $this->softDelete($id);
        }
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function softDelete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL"
        );
        return $stmt->execute(['id' => $id]);
    }

    public function restore(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET deleted_at = NULL WHERE id = :id AND deleted_at IS NOT NULL"
        );
        return $stmt->execute(['id' => $id]);
    }

    public function forceDelete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function count(array $conditions = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE 1=1" . $this->notDeletedClause();
        $params = [];

        foreach ($conditions as $col => $val) {
            $sql .= " AND {$col} = :{$col}";
            $params[$col] = $val;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function paginate(int $page = 1, int $perPage = 20, string $orderBy = 'id DESC'): array
    {
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $where = '1=1' . $this->notDeletedClause();
        $total = (int) $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE {$where}")->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$where} ORDER BY {$orderBy} LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'     => $stmt->fetchAll(),
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => (int) ceil($total / max(1, $perPage)),
        ];
    }
}
