<?php
namespace App\Controllers;

use App\Core\Request;
use App\Models\ActivityLog;

class LogController extends BaseController
{
    public function __construct(private ActivityLog $logModel)
    {
        parent::__construct();
    }

    public function index(Request $req): void
    {
        $page = max(1, (int) $req->get('page', 1));
        $result = $this->logModel->paginate($page, 50, 'created_at DESC');
        $todayCount = $this->logModel->countToday();

        $this->render('logs/index', [
            'logs'       => $result['data'],
            'todayCount' => $todayCount,
            'pagination' => $result,
        ]);
        $this->log('navigation', 'Página de Logs de Atividades carregada');
    }

    public function apiList(Request $req): void
    {
        $page = max(1, (int) $req->get('page', 1));
        $result = $this->logModel->paginate($page, 50, 'created_at DESC');
        $this->json($result);
    }

    public function clear(Request $req): void
    {
        $this->logModel->clearAll();
        $this->log('warning', 'Histórico de logs limpo');
        $this->json(['success' => true]);
    }
}
