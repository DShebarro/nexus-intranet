<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Exceptions\NotFoundException;
use App\Models\Task;
use App\Models\Category;

class TaskController extends BaseController
{
    public function __construct(
        private Task $task,
        private Category $category
    ) {
        parent::__construct();
    }

    public function index(Request $req): void
    {
        $categories = $this->category->getWithCounts('task');
        $activeCategory = $req->get('category_id');

        $columns = [
            'todo'     => $this->task->getByStatus('todo', $activeCategory ? (int) $activeCategory : null),
            'progress' => $this->task->getByStatus('progress', $activeCategory ? (int) $activeCategory : null),
            'review'   => $this->task->getByStatus('review', $activeCategory ? (int) $activeCategory : null),
            'done'     => $this->task->getByStatus('done', $activeCategory ? (int) $activeCategory : null),
        ];

        $this->render('tasks/index', compact('columns', 'categories', 'activeCategory'));
    }

    public function store(Request $req): void
    {
        $data = $this->validate($req->json(), [
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'priority'    => 'required|in:baixa,media,alta',
            'status'      => 'nullable|in:todo,progress,review,done',
            'due_date'    => 'nullable|date',
            'category_id' => 'nullable|integer',
        ]);

        $data['created_by'] = Auth::id() ?? 1;
        $id = $this->task->create($data);
        $this->audit('task', "Tarefa criada: {$data['title']}", 'create', 'task', $id, null, $data);
        $this->json(['success' => true, 'id' => $id], 201);
    }

    public function update(Request $req, array $params): void
    {
        $id = (int) $params['id'];
        $old = $this->task->findById($id);
        if (!$old) {
            throw new NotFoundException('Tarefa não encontrada.');
        }

        $data = $this->validate($req->json(), [
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'priority'    => 'required|in:baixa,media,alta',
            'status'      => 'nullable|in:todo,progress,review,done',
            'due_date'    => 'nullable|date',
            'category_id' => 'nullable|integer',
        ]);

        $this->task->update($id, $data);
        $this->audit('task', "Tarefa #{$id} atualizada: {$data['title']}", 'update', 'task', $id, $old, $data);
        $this->json(['success' => true]);
    }

    public function move(Request $req, array $params): void
    {
        $id = (int) $params['id'];
        if (!$this->task->findById($id)) {
            throw new NotFoundException('Tarefa não encontrada.');
        }

        $data = $this->validate($req->json(), [
            'status' => 'required|in:todo,progress,review,done',
        ]);

        $this->task->moveStatus($id, $data['status']);
        $this->log('task', "Tarefa #{$id} movida para {$data['status']}");
        $this->json(['success' => true]);
    }

    public function destroy(Request $req, array $params): void
    {
        $id = (int) $params['id'];
        $old = $this->task->findById($id);
        if (!$old) {
            throw new NotFoundException('Tarefa não encontrada.');
        }

        $this->task->delete($id);
        $this->audit('warning', "Tarefa #{$id} excluída", 'delete', 'task', $id, $old, null);
        $this->json(['success' => true]);
    }

    public function restore(Request $req, array $params): void
    {
        $id = (int) $params['id'];
        if (!$this->task->restore($id)) {
            throw new NotFoundException('Tarefa não encontrada ou já ativa.');
        }
        $this->audit('task', "Tarefa #{$id} restaurada", 'restore', 'task', $id);
        $this->json(['success' => true]);
    }

    public function apiList(Request $req): void
    {
        $categoryId = $req->get('category_id');
        $page = max(1, (int) $req->get('page', 1));

        if ($categoryId) {
            $tasks = $this->task->findAllByCategory((int) $categoryId);
            $this->json(['data' => $tasks, 'page' => 1, 'total' => count($tasks)]);
            return;
        }

        $result = $this->task->paginate($page, 50);
        $this->json($result);
    }
}
