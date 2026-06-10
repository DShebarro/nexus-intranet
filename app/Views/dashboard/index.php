<?php $pageScript = 'dashboard'; ?>

<div class="fade-up" style="padding:28px;">

    <!-- Page Header -->
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Visão geral do sistema — <?= date('l, d \d\e F \d\e Y') ?></p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <a href="/tasks" class="btn btn-secondary">
                <i data-lucide="check-square" style="width:14px;height:14px;"></i>
                Ver Tarefas
            </a>
            <a href="/contracts" class="btn btn-primary">
                <i data-lucide="plus" style="width:14px;height:14px;"></i>
                Novo Contrato
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid stagger" style="padding:0;margin-bottom:24px;">
        <!-- Tarefas Ativas -->
        <div class="stat-card fade-up" style="--accent-start:rgba(99,102,241,0.08);">
            <div class="stat-card-icon" style="background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.2);">
                <i data-lucide="check-square" style="width:20px;height:20px;color:#818cf8;"></i>
            </div>
            <div class="stat-card-label">Tarefas Ativas</div>
            <div class="stat-card-value"><?= $stats['active_tasks'] ?></div>
            <div class="stat-card-sub">não concluídas</div>
        </div>

        <!-- Contratos -->
        <div class="stat-card fade-up" style="--accent-start:rgba(16,185,129,0.08);">
            <div class="stat-card-icon" style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.2);">
                <i data-lucide="file-text" style="width:20px;height:20px;color:#34d399;"></i>
            </div>
            <div class="stat-card-label">Contratos Ativos</div>
            <div class="stat-card-value"><?= $stats['active_contracts'] ?></div>
            <div class="stat-card-sub">vigentes</div>
        </div>

        <!-- Valor Total -->
        <div class="stat-card fade-up" style="--accent-start:rgba(245,158,11,0.08);">
            <div class="stat-card-icon" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.2);">
                <i data-lucide="dollar-sign" style="width:20px;height:20px;color:#fbbf24;"></i>
            </div>
            <div class="stat-card-label">Valor em Contratos</div>
            <div class="stat-card-value" style="font-size:18px;">R$ <?= number_format($stats['contracts_value'], 0, ',', '.') ?></div>
            <div class="stat-card-sub">valor total alocado</div>
        </div>

        <!-- Ações Hoje -->
        <div class="stat-card fade-up" style="--accent-start:rgba(14,165,233,0.08);">
            <div class="stat-card-icon" style="background:rgba(14,165,233,0.12);border:1px solid rgba(14,165,233,0.2);">
                <i data-lucide="activity" style="width:20px;height:20px;color:#38bdf8;"></i>
            </div>
            <div class="stat-card-label">Ações Hoje</div>
            <div class="stat-card-value"><?= $stats['today_actions'] ?></div>
            <div class="stat-card-sub">registradas nos logs</div>
        </div>
    </div>

    <!-- Bottom Grid -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;flex-wrap:wrap;">

        <!-- Atividades Recentes -->
        <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:16px;overflow:hidden;" class="fade-up">
            <div style="padding:18px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:14px;font-weight:600;color:var(--text-primary);">Atividades Recentes</div>
                    <div style="font-size:12px;color:var(--text-faint);margin-top:2px;">Últimas ações no sistema</div>
                </div>
                <a href="/logs" style="font-size:12px;color:var(--indigo-light);text-decoration:none;font-weight:500;">Ver todos →</a>
            </div>
            <div style="padding:8px 0;">
                <?php if (empty($recentLogs)): ?>
                <div class="empty-state" style="padding:32px;">
                    <i data-lucide="inbox" class="empty-state-icon"></i>
                    <p>Nenhuma atividade registrada ainda.</p>
                </div>
                <?php else: ?>
                <?php foreach ($recentLogs as $i => $log): ?>
                <?php
                $typeColors = [
                    'warning'    => '#fb7185',
                    'task'       => '#34d399',
                    'navigation' => '#38bdf8',
                    'contract'   => '#fbbf24',
                    'site'       => '#a78bfa',
                    'category'   => '#818cf8',
                ];
                $dotColor = $typeColors[$log['type']] ?? '#4f4f6a';
                ?>
                <div style="display:flex;align-items:flex-start;gap:12px;padding:12px 20px;border-bottom:1px solid var(--border);transition:var(--transition);" onmouseover="this.style.background='var(--bg-elevated)'" onmouseout="this.style.background='transparent'">
                    <div style="width:7px;height:7px;background:<?= $dotColor ?>;border-radius:50%;margin-top:5px;flex-shrink:0;"></div>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:13px;color:var(--text-primary);line-height:1.4;"><?= htmlspecialchars($log['description']) ?></p>
                        <p style="font-size:11px;color:var(--text-faint);margin-top:2px;"><?= date('d/m H:i', strtotime($log['created_at'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Acesso Rápido -->
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div style="background:var(--bg-card);border:1px solid var(--border);border-radius:16px;overflow:hidden;" class="fade-up">
                <div style="padding:18px 20px;border-bottom:1px solid var(--border);">
                    <div style="font-size:14px;font-weight:600;color:var(--text-primary);">Acesso Rápido</div>
                    <div style="font-size:12px;color:var(--text-faint);margin-top:2px;">Principais seções do sistema</div>
                </div>
                <div style="padding:12px;">
                    <?php
                    $quickLinks = [
                        ['href'=>'/tasks',     'icon'=>'check-square',  'label'=>'Gerenciador de Tarefas', 'sub'=>'Quadro Kanban', 'color'=>'#818cf8', 'bg'=>'rgba(99,102,241,0.1)'],
                        ['href'=>'/contracts', 'icon'=>'file-text',     'label'=>'Contratos',              'sub'=>'CRUD completo', 'color'=>'#34d399', 'bg'=>'rgba(16,185,129,0.1)'],
                        ['href'=>'/sites',     'icon'=>'globe',         'label'=>'Diretório de Sites',     'sub'=>'Links corporativos', 'color'=>'#38bdf8', 'bg'=>'rgba(14,165,233,0.1)'],
                        ['href'=>'/chat',      'icon'=>'message-square','label'=>'Chat Corporativo',       'sub'=>'Nexus AI + Canais', 'color'=>'#a78bfa','bg'=>'rgba(167,139,250,0.1)'],
                    ];
                    foreach ($quickLinks as $ql):
                    ?>
                    <a href="<?= $ql['href'] ?>" style="display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;text-decoration:none;transition:var(--transition);" onmouseover="this.style.background='var(--bg-elevated)'" onmouseout="this.style.background='transparent'">
                        <div style="width:36px;height:36px;background:<?= $ql['bg'] ?>;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i data-lucide="<?= $ql['icon'] ?>" style="width:16px;height:16px;color:<?= $ql['color'] ?>;"></i>
                        </div>
                        <div>
                            <div style="font-size:13px;font-weight:600;color:var(--text-primary);"><?= $ql['label'] ?></div>
                            <div style="font-size:11px;color:var(--text-faint);"><?= $ql['sub'] ?></div>
                        </div>
                        <i data-lucide="chevron-right" style="width:14px;height:14px;color:var(--text-faint);margin-left:auto;"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</div>
