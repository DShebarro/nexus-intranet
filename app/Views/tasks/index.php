<?php $pageScript = 'tasks'; ?>

<!-- Page Header -->
<div style="padding:28px 28px 0;" class="fade-up">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-title">Lista de Tarefas</h1>
            <p class="page-subtitle">
                <?php
                $total = 0; $done = 0;
                foreach ($columns as $k => $tasks) {
                    $total += count($tasks);
                    if ($k === 'done') $done = count($tasks);
                }
                $active = $total - $done;
                ?>
                <span style="color:var(--text-primary);font-weight:600;"><?= $active ?></span> pendentes ·
                <span style="color:#34d399;font-weight:600;"><?= $done ?></span> concluídas ·
                <span style="color:var(--text-faint);"><?= $total ?> no total</span>
            </p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <!-- Progress bar -->
            <?php $pct = $total > 0 ? round(($done / $total) * 100) : 0; ?>
            <div style="display:flex;align-items:center;gap:10px;padding:8px 14px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:10px;min-width:180px;">
                <div style="flex:1;height:6px;background:var(--bg-hover);border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:<?= $pct ?>%;background:linear-gradient(90deg,#6366f1,#10b981);border-radius:99px;transition:width 0.6s ease;"></div>
                </div>
                <span style="font-size:12px;font-weight:600;color:var(--text-muted);flex-shrink:0;"><?= $pct ?>%</span>
            </div>
            <button id="btn-new-category-task" data-type="task" class="btn btn-secondary">
                <i data-lucide="folder-plus" style="width:14px;height:14px;"></i>
                Nova Pasta
            </button>
            <button id="btn-new-task" class="btn btn-primary">
                <i data-lucide="plus" style="width:14px;height:14px;"></i>
                Nova Tarefa
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-bar" style="padding:0;margin-bottom:20px;">
        <div class="search-wrap">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" id="search-tasks" placeholder="Buscar tarefas por título ou descrição..." class="input-field search-input">
        </div>
        <select id="filter-category-task" class="select-field">
            <option value="">Todas as Pastas</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($activeCategory == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?> (<?= $cat['item_count'] ?>)
            </option>
            <?php endforeach; ?>
        </select>
        <select id="filter-priority-task" class="select-field">
            <option value="">Todas Prioridades</option>
            <option value="alta">🔴 Alta</option>
            <option value="media">🟡 Média</option>
            <option value="baixa">🟢 Baixa</option>
        </select>
    </div>
</div>

<!-- Task List -->
<div style="padding:0 28px 28px;" class="fade-up">
    <?php
    $sections = [
        'todo'     => ['label'=>'A Fazer',       'icon'=>'circle',       'color'=>'#f43f5e', 'bg'=>'rgba(244,63,94,0.08)',   'border'=>'rgba(244,63,94,0.15)'],
        'progress' => ['label'=>'Em Andamento',  'icon'=>'loader',       'color'=>'#f59e0b', 'bg'=>'rgba(245,158,11,0.08)',  'border'=>'rgba(245,158,11,0.15)'],
        'review'   => ['label'=>'Em Revisão',    'icon'=>'eye',          'color'=>'#6366f1', 'bg'=>'rgba(99,102,241,0.08)',  'border'=>'rgba(99,102,241,0.15)'],
        'done'     => ['label'=>'Concluído',     'icon'=>'check-circle', 'color'=>'#10b981', 'bg'=>'rgba(16,185,129,0.08)',  'border'=>'rgba(16,185,129,0.15)'],
    ];
    $priorityConfig = [
        'alta'  => ['label'=>'Alta',  'color'=>'#fb7185', 'dot'=>'#f43f5e'],
        'media' => ['label'=>'Média', 'color'=>'#fbbf24', 'dot'=>'#f59e0b'],
        'baixa' => ['label'=>'Baixa', 'color'=>'#34d399', 'dot'=>'#10b981'],
    ];
    foreach ($sections as $key => $sec):
        $tasks = $columns[$key];
        $count = count($tasks);
        $isDone = ($key === 'done');
    ?>
    <!-- Section: <?= $sec['label'] ?> -->
    <div class="task-section" data-section="<?= $key ?>" style="margin-bottom:12px;">

        <!-- Section Header (clickable to collapse) -->
        <button class="task-section-toggle" data-target="section-<?= $key ?>"
                style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 16px;background:<?= $sec['bg'] ?>;border:1px solid <?= $sec['border'] ?>;border-radius:12px;cursor:pointer;text-align:left;transition:var(--transition);margin-bottom:2px;"
                onmouseover="this.style.filter='brightness(1.2)'" onmouseout="this.style.filter=''">
            <i data-lucide="<?= $sec['icon'] ?>" style="width:15px;height:15px;color:<?= $sec['color'] ?>;flex-shrink:0;<?= $key === 'progress' ? 'animation:spin 2s linear infinite;' : '' ?>"></i>
            <span style="font-size:13px;font-weight:700;color:var(--text-primary);flex:1;"><?= $sec['label'] ?></span>
            <span style="font-size:12px;font-weight:600;color:<?= $sec['color'] ?>;background:<?= $sec['bg'] ?>;border:1px solid <?= $sec['border'] ?>;padding:2px 9px;border-radius:99px;"><?= $count ?></span>
            <i data-lucide="chevron-down" class="section-chevron" style="width:14px;height:14px;color:var(--text-faint);transition:transform 0.2s ease;<?= $isDone && $count > 0 ? 'transform:rotate(-90deg)' : '' ?>"></i>
        </button>

        <!-- Task Rows -->
        <div id="section-<?= $key ?>" style="display:<?= ($isDone && $count > 0) ? 'none' : 'block' ?>;">
            <?php if ($count === 0): ?>
            <div style="padding:20px 20px;text-align:center;color:var(--text-faint);font-size:13px;display:flex;align-items:center;justify-content:center;gap:8px;">
                <i data-lucide="inbox" style="width:14px;height:14px;opacity:0.4;"></i>
                <span>Nenhuma tarefa <?= strtolower($sec['label']) ?></span>
            </div>
            <?php else: ?>
            <div style="background:var(--bg-card);border:1px solid var(--border);border-top:none;border-radius:0 0 12px 12px;overflow:hidden;">
                <?php foreach ($tasks as $i => $task):
                    $pCfg   = $priorityConfig[$task['priority']] ?? $priorityConfig['media'];
                    $dueTs  = $task['due_date'] ? strtotime($task['due_date']) : null;
                    $isLate = $dueTs && $dueTs < time() && !$isDone;
                    $isToday = $dueTs && date('Y-m-d', $dueTs) === date('Y-m-d');
                ?>
                <div class="task-row task-card"
                     data-id="<?= $task['id'] ?>"
                     data-title="<?= htmlspecialchars($task['title']) ?>"
                     data-description="<?= htmlspecialchars($task['description'] ?? '') ?>"
                     data-priority="<?= $task['priority'] ?>"
                     data-category="<?= $task['category_id'] ?>"
                     data-status="<?= $key ?>"
                     data-date="<?= $task['due_date'] ?>"
                     style="display:flex;align-items:center;gap:14px;padding:14px 18px;border-bottom:1px solid var(--border);transition:var(--transition);<?= $isDone ? 'opacity:0.6;' : '' ?>"
                     onmouseover="this.style.background='var(--bg-elevated)'" onmouseout="this.style.background='transparent'">

                    <!-- Checkbox -->
                    <button class="btn-toggle-done" data-id="<?= $task['id'] ?>" data-status="<?= $key ?>"
                            title="<?= $isDone ? 'Reabrir tarefa' : 'Marcar como concluída' ?>"
                            style="width:20px;height:20px;border-radius:50%;border:2px solid <?= $isDone ? '#10b981' : 'var(--border-strong)' ?>;background:<?= $isDone ? 'rgba(16,185,129,0.15)' : 'transparent' ?>;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;transition:var(--transition);"
                            onmouseover="this.style.borderColor='<?= $isDone ? '#34d399' : '#6366f1' ?>'" onmouseout="this.style.borderColor='<?= $isDone ? '#10b981' : 'var(--border-strong)' ?>'">
                        <?php if ($isDone): ?>
                        <i data-lucide="check" style="width:11px;height:11px;color:#10b981;"></i>
                        <?php endif; ?>
                    </button>

                    <!-- Priority dot -->
                    <div style="width:7px;height:7px;background:<?= $pCfg['dot'] ?>;border-radius:50%;flex-shrink:0;" title="Prioridade <?= $pCfg['label'] ?>"></div>

                    <!-- Content -->
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                            <span class="task-title" style="font-size:13px;font-weight:600;color:var(--text-primary);<?= $isDone ? 'text-decoration:line-through;color:var(--text-faint);' : '' ?>"><?= htmlspecialchars($task['title']) ?></span>
                            <?php if ($task['category_name']): ?>
                            <span class="badge badge-indigo task-category" style="font-size:10px;"><?= htmlspecialchars($task['category_name']) ?></span>
                            <?php endif; ?>
                            <?php if ($isLate): ?>
                            <span class="badge badge-red" style="font-size:10px;">Atrasada</span>
                            <?php elseif ($isToday && !$isDone): ?>
                            <span class="badge badge-yellow" style="font-size:10px;">Hoje</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($task['description'])): ?>
                        <p style="font-size:12px;color:var(--text-faint);margin-top:3px;line-height:1.4;"><?= htmlspecialchars(mb_strimwidth($task['description'], 0, 100, '...')) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Meta info -->
                    <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;">
                        <!-- Priority badge -->
                        <span style="font-size:11px;font-weight:600;color:<?= $pCfg['color'] ?>;white-space:nowrap;display:none;" class="priority-label task-priority">
                            <?= $pCfg['label'] ?>
                        </span>

                        <!-- Due date -->
                        <?php if ($dueTs): ?>
                        <div style="display:flex;align-items:center;gap:4px;font-size:11px;color:<?= $isLate ? '#fb7185' : ($isToday ? '#fbbf24' : 'var(--text-faint)') ?>;white-space:nowrap;">
                            <i data-lucide="calendar" style="width:11px;height:11px;"></i>
                            <?= date('d/m/Y', $dueTs) ?>
                        </div>
                        <?php endif; ?>

                        <!-- Move to status dropdown -->
                        <div class="move-status-wrap" style="position:relative;display:none;">
                            <select class="btn-move-status select-field" data-id="<?= $task['id'] ?>"
                                    style="font-size:11px;padding:4px 8px;border-radius:7px;min-width:0;padding-right:20px;height:28px;"
                                    title="Mover para...">
                                <option value="todo"     <?= $key==='todo'?'selected':'' ?>>📋 A Fazer</option>
                                <option value="progress" <?= $key==='progress'?'selected':'' ?>>⚡ Andamento</option>
                                <option value="review"   <?= $key==='review'?'selected':'' ?>>🔍 Revisão</option>
                                <option value="done"     <?= $key==='done'?'selected':'' ?>>✅ Concluído</option>
                            </select>
                        </div>

                        <!-- Actions (visible on hover) -->
                        <div class="task-actions" style="display:flex;gap:2px;opacity:0;transition:opacity 0.15s;">
                            <button class="action-btn btn-edit-task" data-id="<?= $task['id'] ?>" title="Editar">
                                <i data-lucide="edit-3" style="width:13px;height:13px;"></i>
                            </button>
                            <button class="action-btn danger btn-delete-task" data-id="<?= $task['id'] ?>" title="Excluir">
                                <i data-lucide="trash-2" style="width:13px;height:13px;"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Empty state global -->
    <?php if ($total === 0): ?>
    <div style="text-align:center;padding:64px 24px;background:var(--bg-card);border:1px solid var(--border);border-radius:16px;">
        <div style="width:64px;height:64px;background:var(--indigo-glow);border:1px solid rgba(99,102,241,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
            <i data-lucide="check-square" style="width:28px;height:28px;color:var(--indigo-light);"></i>
        </div>
        <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:8px;">Nenhuma tarefa ainda!</h3>
        <p style="font-size:13px;color:var(--text-muted);margin-bottom:20px;">Crie sua primeira tarefa para começar a organizar suas atividades.</p>
        <button id="btn-new-task-empty" class="btn btn-primary" onclick="$('#btn-new-task').click()">
            <i data-lucide="plus" style="width:14px;height:14px;"></i>
            Criar primeira tarefa
        </button>
    </div>
    <?php endif; ?>
</div>

<style>
/* Hover reveal task actions */
.task-row:hover .task-actions { opacity: 1 !important; }
.task-row:hover .move-status-wrap { display: block !important; }
.task-row:hover .priority-label { display: inline !important; }

/* Spin animation for "Em andamento" icon */
@keyframes spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}
</style>
