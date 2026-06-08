<?php
namespace App\Controllers;

use App\Core\Request;
use App\Models\Task;

class TaskController extends BaseController
{
    private Task $task;

    public function __construct()
    {
        $this->task = new Task();
    }

    public function index(Request $req): void
    {
        $columns = [
            'todo' => $this->task->getByStatus('todo'),
            'progress' => $this->task->getByStatus('progress'),
            'review' => $this->task->getByStatus('review'),
            'done' => $this->task->getByStatus('done'),
        ];
        $this->render('tasks/index', compact('columns'));
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
        $id = (int) $params['id'];
        $this->task->update($id, $data);
        $this->log('task', "Tarefa #{$id} atualizada: {$data['title']}");
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
}
