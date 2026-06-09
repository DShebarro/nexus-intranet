<?php $pageScript = 'contracts'; ?>
<div class="flex h-full">
    <!-- Sidebar de Pastas -->
    <aside class="w-72 bg-slate-950/30 border-r border-slate-800 p-4 overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Pastas</h3>
            <button id="btn-new-category-contract" 
                    data-type="contract"
                    class="text-slate-400 hover:text-indigo-400 transition-colors">
                <i data-lucide="folder-plus" class="w-4 h-4"></i>
            </button>
        </div>
        
        <nav class="space-y-1">
            <a href="/contracts" 
               class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors
                      <?= !$activeCategory ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' ?>">
                <i data-lucide="folder" class="w-4 h-4"></i>
                <span class="flex-1 text-sm">Todos os Contratos</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-700">
                    <?= array_sum(array_column($categories, 'item_count')) ?>
                </span>
            </a>
            
            <?php foreach ($categories as $cat): ?>
            <a href="/contracts?category_id=<?= $cat['id'] ?>" 
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
    <div class="flex-1 p-6 overflow-auto">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold">
                <?= $activeCategory ? 'Contratos - ' . htmlspecialchars($categories[array_search($activeCategory, array_column($categories, 'id'))]['name'] ?? '') : 'Todos os Contratos' ?>
            </h2>
            <button id="btn-new-contract" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-sm font-semibold">
                + Novo Contrato
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
                <p class="text-slate-400 text-sm">Valor Total Alocado</p>
                <p class="text-2xl font-bold mt-1">R$ <?= number_format($stats['total_value'], 2, ',', '.') ?></p>
            </div>
            <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
                <p class="text-slate-400 text-sm">Contratos Ativos</p>
                <p class="text-2xl font-bold mt-1"><?= $stats['active_count'] ?></p>
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
                        <th class="text-left p-4 text-sm font-semibold text-slate-400">Categoria</th>
                        <th class="text-left p-4 text-sm font-semibold text-slate-400">Vencimento</th>
                        <th class="text-left p-4 text-sm font-semibold text-slate-400">Ações</th>
                    </tr>
                </thead>
                <tbody id="contracts-table-body">
                    <?php foreach ($contracts as $contract): ?>
                    <tr class="border-b border-slate-800 hover:bg-slate-800/30 transition-colors">
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
</div>

<div id="modal-container" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-slate-900 rounded-2xl p-6 w-full max-w-md border border-slate-700">
        <div id="modal-body"></div>
    </div>
</div>
