<?php
namespace App\Controllers;

use App\Core\Request;
use App\Models\{Task, Contract, Site, ActivityLog};

class DashboardController extends BaseController
{
    public function __construct(
        private Task $taskModel,
        private Contract $contractModel,
        private Site $siteModel,
        private ActivityLog $logModel
    ) {
        parent::__construct();
    }

    public function index(Request $req): void
    {
        $stats = [
            'active_tasks'     => $this->taskModel->countActive(),
            'active_contracts' => $this->contractModel->countActive(),
            'contracts_value'  => $this->contractModel->getTotalValue(),
            'active_sites'     => $this->siteModel->countByStatus('online'),
            'today_actions'    => $this->logModel->countToday(),
            'pending_tasks'    => $this->taskModel->countActive(),
        ];

        $recentLogs = $this->logModel->recent(5);

        $this->render('dashboard/index', compact('stats', 'recentLogs'));
        $this->log('navigation', 'Dashboard carregado');
    }
}
