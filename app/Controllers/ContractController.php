<?php
namespace App\Controllers;

use App\Core\Request;
use App\Exceptions\NotFoundException;
use App\Models\Contract;
use App\Models\Category;

class ContractController extends BaseController
{
    public function __construct(
        private Contract $contract,
        private Category $category
    ) {
        parent::__construct();
    }

    public function index(Request $req): void
    {
        $categories = $this->category->getWithCounts('contract');
        $activeCategory = $req->get('category_id');
        $catId = $activeCategory ? (int) $activeCategory : null;

        $stats = [
            'total_value'    => $this->contract->getTotalValue($catId),
            'active_count'   => $this->contract->countActive($catId),
            'expiring_count' => count($this->contract->getExpiringThisMonth($catId)),
        ];

        $contracts = $catId
            ? $this->contract->findAllByCategory($catId)
            : $this->contract->findAll();

        $this->render('contracts/index', compact('contracts', 'stats', 'categories', 'activeCategory'));
    }

    public function apiList(Request $req): void
    {
        $categoryId = $req->get('category_id');
        $page = max(1, (int) $req->get('page', 1));

        if ($categoryId) {
            $contracts = $this->contract->findAllByCategory((int) $categoryId);
            $this->json(['data' => $contracts, 'page' => 1, 'total' => count($contracts)]);
            return;
        }

        $result = $this->contract->paginate($page, 50);
        $this->json($result);
    }

    public function store(Request $req): void
    {
        $data = $this->validate($req->json(), [
            'code'        => 'required|string|max:50',
            'partner'     => 'required|string|max:150',
            'object'      => 'required|string|max:255',
            'value'       => 'required|numeric',
            'status'      => 'required|in:vigente,em_renovacao,vencido',
            'end_date'    => 'required|date',
            'category_id' => 'nullable|integer',
        ]);

        $id = $this->contract->create($data);
        $this->audit('contract', "Contrato criado: {$data['code']}", 'create', 'contract', $id, null, $data);
        $this->json(['success' => true, 'id' => $id], 201);
    }

    public function update(Request $req, array $params): void
    {
        $id = (int) $params['id'];
        $old = $this->contract->findById($id);
        if (!$old) {
            throw new NotFoundException('Contrato não encontrado.');
        }

        $data = $this->validate($req->json(), [
            'code'        => 'required|string|max:50',
            'partner'     => 'required|string|max:150',
            'object'      => 'required|string|max:255',
            'value'       => 'required|numeric',
            'status'      => 'required|in:vigente,em_renovacao,vencido',
            'end_date'    => 'required|date',
            'category_id' => 'nullable|integer',
        ]);

        $this->contract->update($id, $data);
        $this->audit('contract', "Contrato #{$id} atualizado", 'update', 'contract', $id, $old, $data);
        $this->json(['success' => true]);
    }

    public function destroy(Request $req, array $params): void
    {
        $id = (int) $params['id'];
        $old = $this->contract->findById($id);
        if (!$old) {
            throw new NotFoundException('Contrato não encontrado.');
        }

        $this->contract->delete($id);
        $this->audit('warning', "Contrato #{$id} excluído", 'delete', 'contract', $id, $old, null);
        $this->json(['success' => true]);
    }

    public function restore(Request $req, array $params): void
    {
        $id = (int) $params['id'];
        if (!$this->contract->restore($id)) {
            throw new NotFoundException('Contrato não encontrado ou já ativo.');
        }
        $this->audit('contract', "Contrato #{$id} restaurado", 'restore', 'contract', $id);
        $this->json(['success' => true]);
    }
}
