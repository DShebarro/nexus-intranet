<?php

return new class {
    public function up(PDO $db): void
    {
        $columns = [
            'user_id'     => "INT NULL",
            'entity_type' => "VARCHAR(50) NULL",
            'entity_id'   => "INT NULL",
            'action'      => "VARCHAR(40) NULL",
            'old_values'  => "JSON NULL",
            'new_values'  => "JSON NULL",
        ];

        foreach ($columns as $col => $def) {
            $check = $db->query("SHOW COLUMNS FROM activity_logs LIKE '{$col}'");
            if ($check->rowCount() === 0) {
                $db->exec("ALTER TABLE activity_logs ADD COLUMN {$col} {$def}");
            }
        }

        $idx = $db->query("SHOW INDEX FROM activity_logs WHERE Key_name = 'idx_entity'");
        if ($idx->rowCount() === 0) {
            $db->exec("ALTER TABLE activity_logs ADD INDEX idx_entity (entity_type, entity_id)");
        }
    }

    public function down(PDO $db): void
    {
        foreach (['user_id', 'entity_type', 'entity_id', 'action', 'old_values', 'new_values'] as $col) {
            $check = $db->query("SHOW COLUMNS FROM activity_logs LIKE '{$col}'");
            if ($check->rowCount() > 0) {
                $db->exec("ALTER TABLE activity_logs DROP COLUMN {$col}");
            }
        }
    }
};
