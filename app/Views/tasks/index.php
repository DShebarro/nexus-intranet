<?php $pageScript = 'tasks'; ?>
<div class="flex h-full">
    <!-- Sidebar de Pastas -->
    <aside class="w-72 bg-slate-950/30 border-r border-slate-800 p-4 overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Pastas</h3>
            <button id="btn-new-category-task" 
                    data-type="task"
                    class="text-slate-400 hover:text-indigo-400 transition-colors">
                <i data-lucide="folder-plus" class="w-4 h-4"></i>
            </button>
        </div>
        
        <nav class="space-y-1">
            <a href="/tasks" 
               class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors
                      <?= !$activeCategory ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' ?>">
                <i data-lucide="folder" class="w-4 h-4"></i>
                <span class="flex-1 text-sm">Todas as Tarefas</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-700">
                    <?= array_sum(array_column($categories, 'item_count')) ?>
                </span>
            </a>
            
            <?php foreach ($categories as $cat): ?>
            <a href="/tasks?category_id=<?= $cat['id'] ?>" 
               class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors
                      <?= ($activeCategory == $cat['id']) ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' ?>">
                <i data-lucide="folder" class="w-4 h-4"></i>
                <span class="flex-1 text-sm"><?= htmlspecialchars($cat['name']) ?></span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-700">
                    <?= $cat['item_count'] ?>
                </span>
            </a>
            <?php endforeach; ?>
        </nav>
    </aside>

    <!-- Área Principal -->
    <div class="flex-1 p-6 overflow-x-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold">
                <?= $activeCategory ? 'Tarefas - ' . htmlspecialchars($categories[array_search($activeCategory, array_column($categories, 'id'))]['name'] ?? '') : 'Todas as Tarefas' ?>
            </h2>
            <button id="btn-new-task" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-sm font-semibold">
                + Nova Tarefa
            </button>
        </div>

        <div class="flex space-x-6 pb-4">
            <?php
            $cols = [
                'todo' => ['label' => 'A Fazer', 'color' => 'rose'],
                'progress' => ['label' => 'Em Andamento', 'color' => 'amber'],
                'review' => ['label' => 'Revisão', 'color' => 'indigo'],
                'done' => ['label' => 'Concluído', 'color' => 'emerald'],
            ];
            foreach ($cols as $key => $col):
            ?>
            <div class="bg-slate-950/30 border border-slate-800 w-96 rounded-2xl flex flex-col shrink-0">
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
                    <div class="task-card bg-slate-900 border border-slate-800 p-4 rounded-xl" 
                         data-id="<?= $task['id'] ?>"
                         draggable="true">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-bold text-sm text-slate-200"><?= htmlspecialchars($task['title']) ?></h4>
                            <?php if ($task['category_name']): ?>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300">
                                <?= htmlspecialchars($task['category_name']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-slate-400 mb-3"><?= htmlspecialchars($task['description'] ?? '') ?></p>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-400">
                                <?= $task['due_date'] ? date('d/m/Y', strtotime($task['due_date'])) : '—' ?>
                            </span>
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
</div>

<div id="modal-container" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-slate-900 rounded-2xl p-6 w-full max-w-md border border-slate-700">
        <div id="modal-body"></div>
    </div>
</div>
