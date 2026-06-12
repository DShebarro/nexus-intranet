<?php
namespace App\Controllers;

use App\Core\Request;
use App\Models\{Task, Contract, Site};

class SearchController extends BaseController
{
    public function __construct(
        private Task $task,
        private Contract $contract,
        private Site $site
    ) {
        parent::__construct();
    }

    public function index(Request $req): void
    {
        $q = trim($req->get('q', ''));
        $type = $req->get('type', 'all');

        if (mb_strlen($q) < 2) {
            $this->json(['error' => 'Digite pelo menos 2 caracteres para buscar.'], 400);
            return;
        }

        $results = [];

        if ($type === 'all' || $type === 'tasks') {
            $results = array_merge($results, $this->task->search($q));
        }
        if ($type === 'all' || $type === 'contracts') {
            $results = array_merge($results, $this->contract->search($q));
        }
        if ($type === 'all' || $type === 'sites') {
            $results = array_merge($results, $this->site->search($q));
        }

        $this->json([
            'query'   => $q,
            'total'   => count($results),
            'results' => $results,
        ]);
    }
}
