<?php
namespace App\Controllers;

use App\Core\Cache;
use App\Core\Request;
use App\Models\Category;

class CategoryController extends BaseController
{
    public function __construct(private Category $category)
    {
        parent::__construct();
    }

    public function index(Request $req): void
    {
        $type = $req->get('type');
        if (!$type || !in_array($type, ['task', 'contract', 'site'], true)) {
            $this->json(['error' => 'Tipo de categoria inválido.'], 400);
            return;
        }

        $categories = Cache::remember("categories.{$type}", 300, fn() =>
            $this->category->getWithCounts($type)
        );

        $this->json($categories);
    }

    public function store(Request $req): void
    {
        $data = $this->validate($req->json(), [
            'name' => 'required|string|max:100',
            'type' => 'required|in:task,contract,site',
        ]);

        $id = $this->category->create($data);
        Cache::forget("categories.{$data['type']}");
        $this->log('category', "Categoria criada: {$data['name']} ({$data['type']})");

        $newCategory = $this->category->findById($id);
        $this->json(['success' => true, 'category' => $newCategory], 201);
    }
}
