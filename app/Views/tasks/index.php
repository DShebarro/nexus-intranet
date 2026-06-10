<?php $pageScript = 'tasks'; ?>
<div class="p-6">
    <!-- Barra de Ações Superior -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Gerenciador de Tarefas</h2>
            <div class="flex space-x-3">
                <button id="btn-new-category-task" 
                        data-type="task"
                        class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-xl text-sm font-semibold transition-colors flex items-center space-x-2">
                    <i data-lucide="folder-plus" class="w-4 h-4"></i>
                    <span>Nova Pasta</span>
                </button>
                <button id="btn-new-task" 
                        class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors flex items-center space-x-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Nova Tarefa</span>
                </button>
            </div>
        </div>
        
        <!-- Barra de Pesquisa e Filtros -->
        <div class="flex items-center space-x-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" 
                       id="search-tasks" 
                       placeholder="Buscar tarefas por título ou descrição..." 
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
            </div>
            <select id="filter-category-task" class="bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500">
                <option value="">Todas as Pastas</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($activeCategory == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?> (<?= $cat['item_count'] ?>)
                </option>
                <?php endforeach; ?>
            </select>
            <select id="filter-priority-task" class="bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500">
                <option value="">Todas Prioridades</option>
                <option value="alta">Alta</option>
                <option value="media">Média</option>
                <option value="baixa">Baixa</option>
            </select>
        </div>
    </div>

    <!-- Quadro Kanban -->
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
        <div class="bg-slate-950/30 border border-slate-800 w-96 rounded-2xl flex flex-col shrink-0">
            <div class="p-4 border-b border-slate-800 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-<?= $col['color'] ?>-500"></span>
                    <h3 class="font-bold text-sm"><?= $col['label'] ?></h3>
                </div>
                <span class="task-count bg-slate-800 text-slate-400 text-xs px-2.5 py-0.5 rounded-full">
                    <?= count($columns[$key]) ?>
                </span>
            </div>
            <div class="flex-1 p-4 space-y-3 kanban-col" data-status="<?= $key ?>">
                <?php foreach ($columns[$key] as $task): ?>
                 <div class="task-card bg-slate-900 border border-slate-800 p-4 rounded-xl" 
                      data-id="<?= $task['id'] ?>"
                      data-title="<?= htmlspecialchars($task['title']) ?>"
                      data-description="<?= htmlspecialchars($task['description'] ?? '') ?>"
                      data-priority="<?= $task['priority'] ?>"
                      data-category="<?= $task['category_id'] ?>"
                      data-date="<?= $task['due_date'] ?>"
                      draggable="true">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-bold text-sm text-slate-200"><?= htmlspecialchars($task['title']) ?></h4>
                        <?php if ($task['category_name']): ?>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300">
                            <?= htmlspecialchars($task['category_name']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-slate-400 mb-3"><?= htmlspecialchars(substr($task['description'] ?? '', 0, 100)) ?></p>
                    <div class="flex justify-between items-center text-xs">
                        <div class="flex items-center space-x-2">
                            <?php
                            $priorityColors = ['alta' => 'text-red-400', 'media' => 'text-yellow-400', 'baixa' => 'text-green-400'];
                            ?>
                            <span class="<?= $priorityColors[$task['priority']] ?>">●</span>
                            <span class="text-slate-400"><?= $task['due_date'] ? date('d/m/Y', strtotime($task['due_date'])) : '—' ?></span>
                        </div>
                        <div class="flex space-x-1">
                            <button class="btn-edit-task text-slate-400 hover:text-white p-1" data-id="<?= $task['id'] ?>">
                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                            </button>
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
