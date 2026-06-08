<?php $pageScript = 'tasks'; ?>
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold">Gerenciador de Tarefas</h2>
        <button id="btn-new-task" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-sm font-semibold">
            + Nova Tarefa
        </button>
    </div>

    <div class="flex space-x-6 overflow-x-auto pb-4">
        <?php
        $cols = [
            'todo' => ['label' => 'A Fazer', 'color' => 'rose'],
            'progress' => ['label' => 'Em Andamento', 'color' => 'amber'],
            'review' => ['label' => 'Revisão', 'color' => 'indigo'],
            'done' => ['label' => 'Concluído', 'color' => 'emerald'],
        ];
        foreach ($cols as $key => $col):
        ?>
        <div class="bg-slate-950/30 border border-slate-800 w-80 rounded-2xl flex flex-col shrink-0">
            <div class="p-4 border-b border-slate-800 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-<?= $col['color'] ?>-500"></span>
                    <h3 class="font-bold text-sm"><?= $col['label'] ?></h3>
                </div>
                <span class="bg-slate-800 text-slate-400 text-xs px-2.5 py-0.5 rounded-full">
                    <?= count($columns[$key]) ?>
                </span>
            </div>
            <div class="flex-1 p-4 space-y-3 kanban-col" data-status="<?= $key ?>">
                <?php foreach ($columns[$key] as $task): ?>
                <div class="task-card bg-slate-900 border border-slate-800 p-4 rounded-xl" data-id="<?= $task['id'] ?>">
                    <h4 class="font-bold text-sm text-slate-200 mb-2"><?= htmlspecialchars($task['title']) ?></h4>
                    <p class="text-xs text-slate-400 mb-3"><?= htmlspecialchars($task['description'] ?? '') ?></p>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400"><?= $task['due_date'] ? date('d/m/Y', strtotime($task['due_date'])) : '—' ?></span>
                        <div class="flex space-x-1">
                            <button class="btn-delete-task text-rose-400 p-1" data-id="<?= $task['id'] ?>">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="modal-container" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-slate-900 rounded-2xl p-6 w-full max-w-md border border-slate-700">
        <div id="modal-body"></div>
    </div>
</div>
