<?php
namespace App\Controllers;

use App\Core\Request;
use App\Models\Site;

class SiteController extends BaseController
{
    private Site $site;

    public function __construct()
    {
        $this->site = new Site();
    }

    public function index(Request $req): void
    {
        $sites = $this->site->findAll();
        $this->render('sites/index', compact('sites'));
        $this->log('navigation', 'Página de Sites/Links Úteis carregada');
    }

    public function store(Request $req): void
    {
        $data = $req->json();
        $id = $this->site->create($data);
        $this->log('info', "Site cadastrado: {$data['name']}");
        $this->json(['success' => true, 'id' => $id], 201);
    }

    public function update(Request $req, array $params): void
    {
        $data = $req->json();
        $id = (int) $params['id'];
        $this->site->update($id, $data);
        $this->log('info', "Site #{$id} atualizado: {$data['name']}");
        $this->json(['success' => true]);
    }

    public function destroy(Request $req, array $params): void
    {
        $id = (int) $params['id'];
        $this->site->delete($id);
        $this->log('warning', "Site #{$id} excluído");
        $this->json(['success' => true]);
    }
}
