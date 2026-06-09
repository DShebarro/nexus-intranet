<?php
namespace App\Controllers;

use App\Core\Request;
use App\Models\Category;

class CategoryController extends BaseController
{
    private Category $category;
    
    public function __construct()
    {
        $this->category = new Category();
    }
    
    public function index(Request $req): void
    {
        $type = $req->get('type');
        if (!$type) {
            $this->json(['error' => 'Tipo de categoria não especificado'], 400);
            return;
        }
        
        $categories = $this->category->getWithCounts($type);
        $this->json($categories);
    }
    
    public function store(Request $req): void
    {
        $data = $req->json();
        
        if (empty($data['name']) || empty($data['type'])) {
            $this->json(['error' => 'Nome e tipo são obrigatórios'], 400);
            return;
        }
        
        $id = $this->category->create($data);
        $this->log('category', "Categoria criada: {$data['name']} ({$data['type']})");
        
        $newCategory = $this->category->findById($id);
        $this->json(['success' => true, 'category' => $newCategory], 201);
    }
}
