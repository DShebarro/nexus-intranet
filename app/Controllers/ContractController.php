<?php
namespace App\Controllers;

use App\Core\Request;
use App\Models\Contract;
use App\Models\Category;

class ContractController extends BaseController
{
    private Contract $contract;
    private Category $category;

    public function __construct()
    {
        $this->contract = new Contract();
        $this->category = new Category();
    }

    public function index(Request $req): void
    {
        $categories = $this->category->getWithCounts('contract');
        $activeCategory = $req->get('category_id');
        
        $stats = [
            'total_value' => $this->contract->getTotalValue($activeCategory ? (int)$activeCategory : null),
            'active_count' => count($activeCategory ? 
                $this->contract->findAllByCategory((int)$activeCategory) : 
                $this->contract->findAll()),
            'expiring_count' => count($this->contract->getExpiringThisMonth($activeCategory ? (int)$activeCategory : null))
        ];
        
        $contracts = $activeCategory ? 
            $this->contract->findAllByCategory((int)$activeCategory) : 
            $this->contract->findAll();
        
        $this->render('contracts/index', compact('contracts', 'stats', 'categories', 'activeCategory'));
    }

    public function apiList(Request $req): void
    {
        $categoryId = $req->get('category_id');
        $contracts = $categoryId ? 
            $this->contract->findAllByCategory((int)$categoryId) : 
            $this->contract->findAll();
        $this->json($contracts);
    }

    public function store(Request $req): void
    {
        $data = $req->json();
        $id = $this->contract->create($data);
        $this->log('contract', "Contrato criado: {$data['code']} - {$data['partner']}");
        $this->json(['success' => true, 'id' => $id], 201);
    }

    public function update(Request $req, array $params): void
    {
        $data = $req->json();
        $this->contract->update((int) $params['id'], $data);
        $this->log('contract', "Contrato #{$params['id']} atualizado");
        $this->json(['success' => true]);
    }

    public function destroy(Request $req, array $params): void
    {
        $this->contract->delete((int) $params['id']);
        $this->log('warning', "Contrato #{$params['id']} excluído");
        $this->json(['success' => true]);
    }
}
