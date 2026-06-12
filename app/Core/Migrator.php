<?php
namespace App\Core;

use PDO;

class Migrator
{
    private PDO $db;
    private string $migrationsPath;

    public function __construct(?PDO $db = null, ?string $migrationsPath = null)
    {
        $this->db = $db ?? Database::getInstance();
        $this->migrationsPath = $migrationsPath ?? __DIR__ . '/../../database/migrations';
    }

    public function run(): array
    {
        $this->ensureMigrationsTable();
        $ran = [];

        foreach ($this->getPending() as $file) {
            $migration = $this->loadMigration($file);
            $name = basename($file, '.php');

            echo "▶ Executando: {$name}\n";
            $migration->up($this->db);

            $stmt = $this->db->prepare("INSERT INTO migrations (migration) VALUES (:name)");
            $stmt->execute(['name' => $name]);
            $ran[] = $name;
            echo "  ✓ Concluída\n";
        }

        return $ran;
    }

    public function rollback(int $steps = 1): array
    {
        $this->ensureMigrationsTable();
        $rolled = [];

        $stmt = $this->db->query("SELECT migration FROM migrations ORDER BY id DESC LIMIT {$steps}");
        $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($migrations as $name) {
            $file = "{$this->migrationsPath}/{$name}.php";
            if (!file_exists($file)) {
                echo "  ⚠ Arquivo não encontrado: {$name}\n";
                continue;
            }

            echo "◀ Revertendo: {$name}\n";
            $this->loadMigration($file)->down($this->db);

            $del = $this->db->prepare("DELETE FROM migrations WHERE migration = :name");
            $del->execute(['name' => $name]);
            $rolled[] = $name;
            echo "  ✓ Revertida\n";
        }

        return $rolled;
    }

    public function status(): array
    {
        $this->ensureMigrationsTable();
        $ran = $this->getRan();
        $all = $this->getAllFiles();

        return [
            'ran'     => $ran,
            'pending' => array_values(array_diff($all, $ran)),
        ];
    }

    private function ensureMigrationsTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                executed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB
        ");
    }

    private function getRan(): array
    {
        $stmt = $this->db->query("SELECT migration FROM migrations ORDER BY id");
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }

    private function getAllFiles(): array
    {
        if (!is_dir($this->migrationsPath)) {
            return [];
        }

        $files = glob("{$this->migrationsPath}/*.php") ?: [];
        return array_map(fn($f) => basename($f, '.php'), $files);
    }

    private function getPending(): array
    {
        $ran = $this->getRan();
        $pending = [];

        foreach ($this->getAllFiles() as $name) {
            if (!in_array($name, $ran, true)) {
                $pending[] = "{$this->migrationsPath}/{$name}.php";
            }
        }

        sort($pending);
        return $pending;
    }

    private function loadMigration(string $file): object
    {
        $migration = require $file;
        if (!is_object($migration) || !method_exists($migration, 'up')) {
            throw new \RuntimeException("Migração inválida: {$file}");
        }
        return $migration;
    }
}
