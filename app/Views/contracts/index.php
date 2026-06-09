<?php $pageScript = 'contracts'; ?>
<div class="p-6">
    <!-- Barra de Ações Superior -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Gerenciador de Contratos</h2>
            <div class="flex space-x-3">
                <button id="btn-new-category-contract" 
                        data-type="contract"
                        class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-xl text-sm font-semibold transition-colors flex items-center space-x-2">
                    <i data-lucide="folder-plus" class="w-4 h-4"></i>
                    <span>Nova Pasta</span>
                </button>
                <button id="btn-new-contract" 
                        class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors flex items-center space-x-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Novo Contrato</span>
                </button>
            </div>
        </div>
        
        <!-- Barra de Pesquisa e Filtros -->
        <div class="flex items-center space-x-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" 
                       id="search-contracts" 
                       placeholder="Buscar contratos por código, fornecedor ou objeto..." 
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
            </div>
            <select id="filter-category-contract" class="bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500">
                <option value="">Todas as Pastas</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($activeCategory == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?> (<?= $cat['item_count'] ?>)
                </option>
                <?php endforeach; ?>
            </select>
            <select id="filter-status-contract" class="bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500">
                <option value="">Todos Status</option>
                <option value="vigente">Vigente</option>
                <option value="em_renovacao">Em Renovação</option>
                <option value="vencido">Vencido</option>
            </select>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
            <p class="text-slate-400 text-sm">Valor Total Alocado</p>
            <p class="text-2xl font-bold mt-1">R$ <?= number_format($stats['total_value'], 2, ',', '.') ?></p>
        </div>
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
            <p class="text-slate-400 text-sm">Contratos Ativos</p>
            <p class="text-2xl font-bold mt-1" id="active-count"><?= $stats['active_count'] ?></p>
        </div>
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
            <p class="text-slate-400 text-sm">Vencem Este Mês</p>
            <p class="text-2xl font-bold mt-1"><?= $stats['expiring_count'] ?></p>
        </div>
    </div>

    <!-- Tabela de Contratos -->
    <div class="bg-slate-900/50 rounded-xl border border-slate-800 overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-800/50 border-b border-slate-700">
                <tr>
                    <th class="text-left p-4 text-sm font-semibold text-slate-400">Código</th>
                    <th class="text-left p-4 text-sm font-semibold text-slate-400">Fornecedor</th>
                    <th class="text-left p-4 text-sm font-semibold text-slate-400">Objeto</th>
                    <th class="text-left p-4 text-sm font-semibold text-slate-400">Valor</th>
                    <th class="text-left p-4 text-sm font-semibold text-slate-400">Status</th>
                    <th class="text-left p-4 text-sm font-semibold text-slate-400">Pasta</th>
                    <th class="text-left p-4 text-sm font-semibold text-slate-400">Vencimento</th>
                    <th class="text-left p-4 text-sm font-semibold text-slate-400">Ações</th>
                </tr>
            </thead>
            <tbody id="contracts-table-body">
                <?php foreach ($contracts as $contract): ?>
                <tr class="contract-row border-b border-slate-800 hover:bg-slate-800/30 transition-colors"
                    data-code="<?= htmlspecialchars($contract['code']) ?>"
                    data-partner="<?= htmlspecialchars($contract['partner']) ?>"
                    data-object="<?= htmlspecialchars($contract['object']) ?>"
                    data-status="<?= $contract['status'] ?>"
                    data-category="<?= $contract['category_id'] ?>">
                    <td class="p-4 text-sm"><?= htmlspecialchars($contract['code']) ?></td>
                    <td class="p-4 text-sm"><?= htmlspecialchars($contract['partner']) ?></td>
                    <td class="p-4 text-sm"><?= htmlspecialchars($contract['object']) ?></td>
                    <td class="p-4 text-sm">R$ <?= number_format($contract['value'], 2, ',', '.') ?></td>
                    <td class="p-4 text-sm">
                        <?php
                        $statusColors = [
                            'vigente' => 'green',
                            'em_renovacao' => 'yellow',
                            'vencido' => 'red'
                        ];
                        $color = $statusColors[$contract['status']] ?? 'gray';
                        ?>
                        <span class="inline-flex px-2 py-1 rounded-full text-xs bg-<?= $color ?>-500/20 text-<?= $color ?>-300">
                            <?= str_replace('_', ' ', $contract['status']) ?>
                        </span>
                    </td>
                    <td class="p-4 text-sm">
                        <?php if ($contract['category_name']): ?>
                        <span class="inline-flex px-2 py-1 rounded-full text-xs bg-indigo-500/20 text-indigo-300">
                            <?= htmlspecialchars($contract['category_name']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-slate-500">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="p-4 text-sm"><?= date('d/m/Y', strtotime($contract['end_date'])) ?></td>
                    <td class="p-4 text-sm">
                        <button class="btn-edit-contract text-slate-400 hover:text-white mr-2" data-id="<?= $contract['id'] ?>">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </button>
                        <button class="btn-delete-contract text-rose-400 hover:text-rose-300" data-id="<?= $contract['id'] ?>">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modal-container" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-slate-900 rounded-2xl p-6 w-full max-w-md border border-slate-700">
        <div id="modal-body"></div>
    </div>
</div>
