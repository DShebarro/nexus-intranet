<?php
namespace App\Controllers;

use App\Core\Request;
use App\Models\{Task, Contract, Site, ActivityLog};

class DashboardController extends BaseController
{
    public function index(Request $req): void
    {
        $taskModel = new Task();
        $contractModel = new Contract();
        $siteModel = new Site();
        $logModel = new ActivityLog();

        $stats = [
            'active_tasks' => $taskModel->countActive(),
            'active_contracts' => count($contractModel->findAll()),
            'contracts_value' => $contractModel->getTotalValue(),
            'active_sites' => count(array_filter($siteModel->findAll(), fn($s) => $s['status'] === 'online')),
            'today_actions' => $logModel->countToday(),
            'pending_tasks' => $taskModel->countActive(),
        ];

        $recentLogs = $logModel->recent(5);

        $this->render('dashboard/index', compact('stats', 'recentLogs'));
        $this->log('navigation', 'Dashboard carregado');
    }
}
