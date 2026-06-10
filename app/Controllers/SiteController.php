<?php
namespace App\Controllers;

use App\Core\Request;
use App\Models\Site;
use App\Models\Category;

class SiteController extends BaseController
{
    private Site $site;
    private Category $category;

    public function __construct()
    {
        $this->site = new Site();
        $this->category = new Category();
    }

    public function index(Request $req): void
    {
        $categories = $this->category->getWithCounts('site');
        $activeCategory = $req->get('category_id');
        
        $sites = $activeCategory ? 
            $this->site->findAllByCategory((int)$activeCategory) : 
            $this->site->findAll();
        
        $this->render('sites/index', compact('sites', 'categories', 'activeCategory'));
    }

    public function apiList(Request $req): void
    {
        $categoryId = $req->get('category_id');
        $sites = $categoryId ? 
            $this->site->findAllByCategory((int)$categoryId) : 
            $this->site->findAll();
        $this->json($sites);
    }

    public function store(Request $req): void
    {
        $data = $req->json();
        $id = $this->site->create($data);
        $this->log('site', "Site criado: {$data['name']}");
        $this->json(['success' => true, 'id' => $id], 201);
    }

    public function update(Request $req, array $params): void
    {
        $data = $req->json();
        $this->site->update((int) $params['id'], $data);
        $this->log('site', "Site #{$params['id']} atualizado: {$data['name']}");
        $this->json(['success' => true]);
    }

    public function destroy(Request $req, array $params): void
    {
        $this->site->delete((int) $params['id']);
        $this->log('warning', "Site #{$params['id']} excluído");
        $this->json(['success' => true]);
    }
}
