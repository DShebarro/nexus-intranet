<?php $pageScript = 'contracts'; ?>

<!-- Page Header -->
<div style="padding:28px 28px 0;" class="fade-up">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-title">Contratos</h1>
            <p class="page-subtitle">Gerenciamento de contratos e acordos corporativos</p>
        </div>
        <div style="display:flex;gap:10px;">
            <button id="btn-new-category-contract" data-type="contract" class="btn btn-secondary">
                <i data-lucide="folder-plus" style="width:14px;height:14px;"></i>
                Nova Pasta
            </button>
            <a href="/api/export/contracts" class="btn btn-secondary" title="Exportar CSV">
                <i data-lucide="download" style="width:14px;height:14px;"></i>
                CSV
            </a>
            <button id="btn-new-contract" class="btn btn-primary">
                <i data-lucide="plus" style="width:14px;height:14px;"></i>
                Novo Contrato
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid stagger" style="padding:0;margin-bottom:20px;grid-template-columns:repeat(3,1fr);">
        <div class="stat-card fade-up">
            <div class="stat-card-icon" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.2);">
                <i data-lucide="dollar-sign" style="width:20px;height:20px;color:#fbbf24;"></i>
            </div>
            <div class="stat-card-label">Valor Total</div>
            <div class="stat-card-value" style="font-size:20px;">R$ <?= number_format($stats['total_value'], 2, ',', '.') ?></div>
            <div class="stat-card-sub">valor alocado</div>
        </div>
        <div class="stat-card fade-up">
            <div class="stat-card-icon" style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.2);">
                <i data-lucide="file-check" style="width:20px;height:20px;color:#34d399;"></i>
            </div>
            <div class="stat-card-label">Contratos Ativos</div>
            <div class="stat-card-value" id="active-count"><?= $stats['active_count'] ?></div>
            <div class="stat-card-sub">em vigor</div>
        </div>
        <div class="stat-card fade-up">
            <div class="stat-card-icon" style="background:rgba(244,63,94,0.12);border:1px solid rgba(244,63,94,0.2);">
                <i data-lucide="clock" style="width:20px;height:20px;color:#fb7185;"></i>
            </div>
            <div class="stat-card-label">Vencem Este Mês</div>
            <div class="stat-card-value"><?= $stats['expiring_count'] ?></div>
            <div class="stat-card-sub">requerem atenção</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-bar" style="padding:0;margin-bottom:16px;">
        <div class="search-wrap">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" id="search-contracts" placeholder="Buscar por código, fornecedor ou objeto..." class="input-field search-input">
        </div>
        <select id="filter-category-contract" class="select-field">
            <option value="">Todas as Pastas</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($activeCategory == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?> (<?= $cat['item_count'] ?>)
            </option>
            <?php endforeach; ?>
        </select>
        <select id="filter-status-contract" class="select-field">
            <option value="">Todos os Status</option>
            <option value="vigente">Vigente</option>
            <option value="em_renovacao">Em Renovação</option>
            <option value="vencido">Vencido</option>
        </select>
    </div>
</div>

<!-- Data Table -->
<div class="data-table-wrap fade-up">
    <table class="data-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Fornecedor / Parceiro</th>
                <th>Objeto</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Pasta</th>
                <th>Vencimento</th>
                <th style="width:80px;text-align:center;">Ações</th>
            </tr>
        </thead>
        <tbody id="contracts-table-body">
            <?php if (empty($contracts)): ?>
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <i data-lucide="file-text" class="empty-state-icon"></i>
                        <p>Nenhum contrato cadastrado ainda.</p>
                        <button onclick="$('#btn-new-contract').click()" class="btn btn-primary" style="margin-top:8px;">
                            <i data-lucide="plus" style="width:13px;height:13px;"></i>
                            Criar primeiro contrato
                        </button>
                    </div>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($contracts as $contract):
                $statusMap = [
                    'vigente'      => ['label' => 'Vigente',       'class' => 'badge-green'],
                    'em_renovacao' => ['label' => 'Em Renovação',  'class' => 'badge-yellow'],
                    'vencido'      => ['label' => 'Vencido',       'class' => 'badge-red'],
                ];
                $s = $statusMap[$contract['status']] ?? ['label' => $contract['status'], 'class' => 'badge-gray'];

                $endTs  = strtotime($contract['end_date']);
                $isLate = $endTs < time() && $contract['status'] !== 'vencido';
            ?>
            <tr class="contract-row"
                data-code="<?= htmlspecialchars($contract['code']) ?>"
                data-partner="<?= htmlspecialchars($contract['partner']) ?>"
                data-object="<?= htmlspecialchars($contract['object']) ?>"
                data-status="<?= $contract['status'] ?>"
                data-category="<?= $contract['category_id'] ?>"
                data-value="<?= $contract['value'] ?>"
                data-end-date="<?= $contract['end_date'] ?>">

                <td>
                    <code style="background:var(--bg-elevated);border:1px solid var(--border);padding:3px 8px;border-radius:6px;font-size:12px;color:var(--indigo-light);font-family:monospace;">
                        <?= htmlspecialchars($contract['code']) ?>
                    </code>
                </td>
                <td>
                    <div style="font-weight:500;"><?= htmlspecialchars($contract['partner']) ?></div>
                </td>
                <td>
                    <div class="td-muted" style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($contract['object']) ?>">
                        <?= htmlspecialchars($contract['object']) ?>
                    </div>
                </td>
                <td>
                    <span style="font-weight:600;color:var(--text-primary);">R$ <?= number_format($contract['value'], 2, ',', '.') ?></span>
                </td>
                <td>
                    <span class="badge <?= $s['class'] ?>"><?= $s['label'] ?></span>
                </td>
                <td>
                    <?php if ($contract['category_name']): ?>
                    <span class="badge badge-indigo"><?= htmlspecialchars($contract['category_name']) ?></span>
                    <?php else: ?>
                    <span style="color:var(--text-faint);font-size:12px;">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <span style="color:<?= $isLate ? '#fb7185' : 'var(--text-muted)' ?>;font-size:13px;font-weight:<?= $isLate ? '600' : '400' ?>;">
                        <?= date('d/m/Y', $endTs) ?>
                    </span>
                </td>
                <td>
                    <div style="display:flex;gap:4px;justify-content:center;">
                        <button class="action-btn btn-edit-contract" data-id="<?= $contract['id'] ?>" title="Editar">
                            <i data-lucide="edit-3" style="width:13px;height:13px;"></i>
                        </button>
                        <button class="action-btn danger btn-delete-contract" data-id="<?= $contract['id'] ?>" title="Excluir">
                            <i data-lucide="trash-2" style="width:13px;height:13px;"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
