<?php
namespace App\Controllers;

use App\Core\{CsvExporter, Request};
use App\Models\{Task, Contract, ActivityLog};

class ExportController extends BaseController
{
    public function __construct(
        private Task $task,
        private Contract $contract,
        private ActivityLog $logModel
    ) {
        parent::__construct();
    }

    public function tasks(Request $req): void
    {
        $rows = $this->task->forExport();
        $csv = CsvExporter::generate(
            ['ID', 'Título', 'Prioridade', 'Status', 'Prazo', 'Categoria', 'Criado em'],
            array_map(fn($t) => [
                $t['id'], $t['title'], $t['priority'], $t['status'],
                $t['due_date'] ?? '', $t['category_name'] ?? '', $t['created_at'],
            ], $rows)
        );
        $this->csv($csv, 'tarefas_' . date('Y-m-d') . '.csv');
    }

    public function contracts(Request $req): void
    {
        $rows = $this->contract->forExport();
        $csv = CsvExporter::generate(
            ['ID', 'Código', 'Parceiro', 'Objeto', 'Valor', 'Status', 'Vencimento', 'Categoria'],
            array_map(fn($c) => [
                $c['id'], $c['code'], $c['partner'], $c['object'],
                number_format((float) $c['value'], 2, ',', '.'),
                $c['status'], $c['end_date'], $c['category_name'] ?? '',
            ], $rows)
        );
        $this->csv($csv, 'contratos_' . date('Y-m-d') . '.csv');
    }

    public function logs(Request $req): void
    {
        $rows = $this->logModel->forExport();
        $csv = CsvExporter::generate(
            ['ID', 'Tipo', 'Ação', 'Entidade', 'Descrição', 'Usuário', 'IP', 'Data'],
            array_map(fn($l) => [
                $l['id'], $l['type'], $l['action'] ?? '', ($l['entity_type'] ?? '') . '#' . ($l['entity_id'] ?? ''),
                $l['description'], $l['user_name'] ?? '', $l['ip_address'] ?? '', $l['created_at'],
            ], $rows)
        );
        $this->csv($csv, 'logs_' . date('Y-m-d') . '.csv');
    }
}
