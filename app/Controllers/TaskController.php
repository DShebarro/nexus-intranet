<?php
namespace App\Controllers;

use App\Core\Request;
use App\Models\Task;
use App\Models\Category;

class TaskController extends BaseController
{
    private Task $task;
    private Category $category;

    public function __construct()
    {
        $this->task = new Task();
        $this->category = new Category();
    }

    public function index(Request $req): void
    {
        $categories = $this->category->getWithCounts('task');
        $activeCategory = $req->get('category_id');
        
        $columns = [
            'todo' => $this->task->getByStatus('todo', $activeCategory ? (int)$activeCategory : null),
            'progress' => $this->task->getByStatus('progress', $activeCategory ? (int)$activeCategory : null),
            'review' => $this->task->getByStatus('review', $activeCategory ? (int)$activeCategory : null),
            'done' => $this->task->getByStatus('done', $activeCategory ? (int)$activeCategory : null),
        ];
        
        $this->render('tasks/index', compact('columns', 'categories', 'activeCategory'));
    }

    public function store(Request $req): void
    {
        $data = $req->json();
        $id = $this->task->create($data);
        $this->log('task', "Tarefa criada: {$data['title']}");
        $this->json(['success' => true, 'id' => $id], 201);
    }

    public function update(Request $req, array $params): void
    {
        $data = $req->json();
        $this->task->update((int) $params['id'], $data);
        $this->log('task', "Tarefa #{$params['id']} atualizada: {$data['title']}");
        $this->json(['success' => true]);
    }

    public function move(Request $req, array $params): void
    {
        $data = $req->json();
        $status = $data['status'] ?? '';
        $this->task->moveStatus((int) $params['id'], $status);
        $this->log('task', "Tarefa #{$params['id']} movida para {$status}");
        $this->json(['success' => true]);
    }

    public function destroy(Request $req, array $params): void
    {
        $this->task->delete((int) $params['id']);
        $this->log('warning', "Tarefa #{$params['id']} excluída");
        $this->json(['success' => true]);
    }

    public function apiList(Request $req): void
    {
        $categoryId = $req->get('category_id');
        $tasks = $categoryId ? $this->task->findAllByCategory((int)$categoryId) : $this->task->findAll();
        $this->json($tasks);
    }
}
