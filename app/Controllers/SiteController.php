<?php
namespace App\Controllers;

use App\Core\Request;
use App\Exceptions\NotFoundException;
use App\Models\Site;
use App\Models\Category;

class SiteController extends BaseController
{
    public function __construct(
        private Site $site,
        private Category $category
    ) {
        parent::__construct();
    }

    public function index(Request $req): void
    {
        $categories = $this->category->getWithCounts('site');
        $activeCategory = $req->get('category_id');

        $sites = $activeCategory
            ? $this->site->findAllByCategory((int) $activeCategory)
            : $this->site->findAll();

        $this->render('sites/index', compact('sites', 'categories', 'activeCategory'));
    }

    public function apiList(Request $req): void
    {
        $categoryId = $req->get('category_id');

        $sites = $categoryId
            ? $this->site->findAllByCategory((int) $categoryId)
            : $this->site->findAll();

        $this->json($sites);
    }

    public function store(Request $req): void
    {
        $data = $this->validate($req->json(), [
            'name'         => 'required|string|max:100',
            'url'          => 'required|url|max:255',
            'description'  => 'nullable|string|max:255',
            'is_internal'  => 'nullable|integer',
            'status'       => 'required|in:online,offline',
            'category_id'  => 'nullable|integer',
        ]);

        $id = $this->site->create($data);
        $this->log('site', "Site criado: {$data['name']}");
        $this->json(['success' => true, 'id' => $id], 201);
    }

    public function update(Request $req, array $params): void
    {
        $id = (int) $params['id'];
        if (!$this->site->findById($id)) {
            throw new NotFoundException('Site não encontrado.');
        }

        $data = $this->validate($req->json(), [
            'name'         => 'required|string|max:100',
            'url'          => 'required|url|max:255',
            'description'  => 'nullable|string|max:255',
            'is_internal'  => 'nullable|integer',
            'status'       => 'required|in:online,offline',
            'category_id'  => 'nullable|integer',
        ]);

        $this->site->update($id, $data);
        $this->log('site', "Site #{$id} atualizado: {$data['name']}");
        $this->json(['success' => true]);
    }

    public function destroy(Request $req, array $params): void
    {
        $id = (int) $params['id'];
        if (!$this->site->findById($id)) {
            throw new NotFoundException('Site não encontrado.');
        }

        $this->site->delete($id);
        $this->log('warning', "Site #{$id} excluído");
        $this->json(['success' => true]);
    }
}
