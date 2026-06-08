<?php
namespace App\Controllers;

use App\Core\Request;
use App\Models\ActivityLog;

class LogController extends BaseController
{
    private ActivityLog $logModel;

    public function __construct()
    {
        $this->logModel = new ActivityLog();
    }

    public function index(Request $req): void
    {
        $logs = $this->logModel->recent(100);
        $todayCount = $this->logModel->countToday();

        $this->render('logs/index', compact('logs', 'todayCount'));
        $this->log('navigation', 'Página de Logs de Atividades carregada');
    }

    public function clear(Request $req): void
    {
        $this->logModel->clearAll();
        $this->log('warning', 'Histórico de logs limpo');
        $this->json(['success' => true]);
    }
}
