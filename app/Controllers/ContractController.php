<?php
namespace App\Controllers;

use App\Core\Request;
use App\Models\Contract;

class ContractController extends BaseController
{
    private Contract $contract;

    public function __construct()
    {
        $this->contract = new Contract();
    }

    public function index(Request $req): void
    {
        $contracts = $this->contract->findAll();
        $totalValue = $this->contract->getTotalValue();
        $expiring = $this->contract->getExpiringThisMonth();

        $this->render('contracts/index', compact('contracts', 'totalValue', 'expiring'));
        $this->log('navigation', 'Página de Contratos carregada');
    }

    public function store(Request $req): void
    {
        $data = $req->json();
        $id = $this->contract->create($data);
        $this->log('info', "Contrato criado: {$data['code']} - {$data['partner']}");
        $this->json(['success' => true, 'id' => $id], 201);
    }

    public function update(Request $req, array $params): void
    {
        $data = $req->json();
        $id = (int) $params['id'];
        $this->contract->update($id, $data);
        $this->log('info', "Contrato #{$id} atualizado: {$data['code']}");
        $this->json(['success' => true]);
    }

    public function destroy(Request $req, array $params): void
    {
        $id = (int) $params['id'];
        $this->contract->delete($id);
        $this->log('warning', "Contrato #{$id} excluído");
        $this->json(['success' => true]);
    }
}
