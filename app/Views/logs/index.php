<?php $pageScript = 'logs'; ?>

<!-- Page Header -->
<div style="padding:28px 28px 0;" class="fade-up">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-title">Logs de Atividades</h1>
            <p class="page-subtitle">Histórico completo de ações registradas no sistema</p>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="display:flex;align-items:center;gap:8px;padding:8px 14px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:10px;">
                <i data-lucide="zap" style="width:14px;height:14px;color:var(--indigo-light);"></i>
                <span style="font-size:13px;color:var(--text-muted);">Hoje:</span>
                <span id="today-actions-count" style="font-size:13px;font-weight:700;color:var(--text-primary);"><?= $todayCount ?></span>
                <span style="font-size:13px;color:var(--text-muted);">ações</span>
            </div>
            <button id="btn-clear-logs" class="btn btn-danger">
                <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                Limpar Logs
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-bar" style="padding:0;margin-bottom:16px;">
        <div class="search-wrap">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" id="search-log" placeholder="Buscar por descrição, IP ou agente..." class="input-field search-input">
        </div>
        <select id="filter-log-type" class="select-field">
            <option value="all">Todos os Tipos</option>
            <option value="navigation">Navegação</option>
            <option value="task">Tarefa</option>
            <option value="contract">Contrato</option>
            <option value="site">Site</option>
            <option value="category">Categoria</option>
            <option value="warning">Warning</option>
        </select>
    </div>
</div>

<!-- Logs Table -->
<div class="data-table-wrap fade-up">
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:150px;">Data / Hora</th>
                <th style="width:110px;">Tipo</th>
                <th>Descrição</th>
                <th style="width:120px;">Endereço IP</th>
                <th style="width:200px;">Agente</th>
            </tr>
        </thead>
        <tbody id="logs-table-body">
            <?php if (empty($logs)): ?>
            <tr id="no-logs-row">
                <td colspan="5">
                    <div class="empty-state" style="padding:48px;">
                        <i data-lucide="inbox" style="width:40px;height:40px;opacity:0.2;"></i>
                        <p>Nenhum log registrado ainda.</p>
                    </div>
                </td>
            </tr>
            <?php else: ?>
            <?php
            $typeConfig = [
                'warning'    => ['label' => 'Warning',    'class' => 'badge-red',    'dot' => '#f43f5e'],
                'task'       => ['label' => 'Tarefa',     'class' => 'badge-green',  'dot' => '#10b981'],
                'navigation' => ['label' => 'Navegação',  'class' => 'badge-blue',   'dot' => '#0ea5e9'],
                'contract'   => ['label' => 'Contrato',   'class' => 'badge-yellow', 'dot' => '#f59e0b'],
                'site'       => ['label' => 'Site',       'class' => 'badge-indigo', 'dot' => '#818cf8'],
                'category'   => ['label' => 'Categoria',  'class' => 'badge-indigo', 'dot' => '#6366f1'],
            ];
            foreach ($logs as $log):
                $tc = $typeConfig[$log['type']] ?? ['label' => $log['type'], 'class' => 'badge-gray', 'dot' => '#4f4f6a'];
            ?>
            <tr class="log-row"
                data-type="<?= htmlspecialchars($log['type']) ?>"
                data-desc="<?= htmlspecialchars($log['description']) ?>"
                data-ip="<?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?>"
                data-ua="<?= htmlspecialchars($log['user_agent'] ?? '') ?>">

                <td>
                    <div style="display:flex;align-items:center;gap:6px;">
                        <div style="width:6px;height:6px;background:<?= $tc['dot'] ?>;border-radius:50%;flex-shrink:0;"></div>
                        <span style="font-size:12px;font-family:monospace;color:var(--text-muted);">
                            <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                        </span>
                    </div>
                </td>
                <td>
                    <span class="badge <?= $tc['class'] ?>" style="font-size:10px;"><?= $tc['label'] ?></span>
                </td>
                <td>
                    <span style="font-size:13px;"><?= htmlspecialchars($log['description']) ?></span>
                </td>
                <td>
                    <code style="font-size:11px;color:var(--text-muted);font-family:monospace;"><?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?></code>
                </td>
                <td>
                    <span style="font-size:11px;color:var(--text-faint);overflow:hidden;text-overflow:ellipsis;display:block;max-width:200px;white-space:nowrap;"
                          title="<?= htmlspecialchars($log['user_agent'] ?? '') ?>">
                        <?= htmlspecialchars($log['user_agent'] ?? 'Desconhecido') ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
