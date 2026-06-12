<?php
// Executar: php database/migrate.php [--rollback] [--status]
// Migrações versionadas em database/migrations/

require __DIR__ . '/../vendor/autoload.php';

use App\Core\{Env, Migrator};

Env::load(__DIR__ . '/..');

$action = $argv[1] ?? 'run';
$migrator = new Migrator();

echo "🗄️  Nexus Intranet — Migrator\n\n";

match ($action) {
    '--status' => (function () use ($migrator) {
        $status = $migrator->status();
        echo "Executadas (" . count($status['ran']) . "):\n";
        foreach ($status['ran'] as $m) echo "  ✓ {$m}\n";
        echo "\nPendentes (" . count($status['pending']) . "):\n";
        foreach ($status['pending'] as $m) echo "  ○ {$m}\n";
        if (empty($status['pending'])) echo "  (nenhuma)\n";
    })(),
    '--rollback' => (function () use ($migrator) {
        $steps = (int) ($argv[2] ?? 1);
        $rolled = $migrator->rollback($steps);
        echo empty($rolled) ? "Nenhuma migração para reverter.\n" : "\n🎉 " . count($rolled) . " migração(ões) revertida(s).\n";
    })(),
    default => (function () use ($migrator) {
        $ran = $migrator->run();
        echo empty($ran) ? "✅ Nenhuma migração pendente.\n" : "\n🎉 " . count($ran) . " migração(ões) executada(s).\n";
    })(),
};
