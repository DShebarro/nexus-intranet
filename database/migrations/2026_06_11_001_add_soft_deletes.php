<?php

use App\Core\Migrator;

return new class {
    public function up(PDO $db): void
    {
        $tables = ['tasks', 'contracts', 'sites'];
        foreach ($tables as $table) {
            $check = $db->query("SHOW COLUMNS FROM {$table} LIKE 'deleted_at'");
            if ($check->rowCount() === 0) {
                $db->exec("ALTER TABLE {$table} ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL");
                $db->exec("ALTER TABLE {$table} ADD INDEX idx_deleted_at (deleted_at)");
            }
        }
    }

    public function down(PDO $db): void
    {
        foreach (['tasks', 'contracts', 'sites'] as $table) {
            $check = $db->query("SHOW COLUMNS FROM {$table} LIKE 'deleted_at'");
            if ($check->rowCount() > 0) {
                $db->exec("ALTER TABLE {$table} DROP COLUMN deleted_at");
            }
        }
    }
};
