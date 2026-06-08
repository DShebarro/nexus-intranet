<?php $pageScript = 'contracts'; ?>
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Contratos Corporativos</h2>
        <button id="btn-new-contract" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-lg">
            + Novo Contrato
        </button>
    </div>

    <!-- Cards de Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Total de Contratos</p>
                    <p class="text-3xl font-bold mt-1"><?= count($contracts) ?></p>
                </div>
                <i data-lucide="file-text" class="w-8 h-8 text-indigo-400"></i>
            </div>
        </div>

        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Valor Total Acumulado</p>
                    <p class="text-3xl font-bold mt-1">R$ <?= number_format($totalValue, 2, ',', '.') ?></p>
                </div>
                <i data-lucide="dollar-sign" class="w-8 h-8 text-emerald-400"></i>
            </div>
        </div>

        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Atenção / Expirações</p>
                    <p class="text-3xl font-bold mt-1"><?= count($expiring) ?></p>
                </div>
                <i data-lucide="alert-triangle" class="w-8 h-8 text-amber-400"></i>
            </div>
        </div>
    </div>

    <!-- Tabela de Contratos -->
    <div class="bg-slate-800/30 border border-slate-800 rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-slate-800 bg-slate-900/50">
            <h3 class="font-bold text-sm text-slate-300">Lista de Contratos</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase bg-slate-950/20">
                        <th class="p-4">Código</th>
                        <th class="p-4">Parceiro</th>
                        <th class="p-4">Objeto</th>
                        <th class="p-4">Valor</th>
                        <th class="p-4">Vencimento</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 w-24 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50 text-sm">
                    <?php if (empty($contracts)): ?>
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500">Nenhum contrato cadastrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($contracts as $contract): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors contract-row"
                                data-id="<?= $contract['id'] ?>"
                                data-code="<?= htmlspecialchars($contract['code']) ?>"
                                data-partner="<?= htmlspecialchars($contract['partner']) ?>"
                                data-object="<?= htmlspecialchars($contract['object']) ?>"
                                data-value="<?= htmlspecialchars($contract['value']) ?>"
                                data-date="<?= htmlspecialchars($contract['end_date']) ?>"
                                data-status="<?= htmlspecialchars($contract['status']) ?>">
                                <td class="p-4 font-mono font-semibold text-slate-300"><?= htmlspecialchars($contract['code']) ?></td>
                                <td class="p-4 font-medium"><?= htmlspecialchars($contract['partner']) ?></td>
                                <td class="p-4 text-slate-400 max-w-xs truncate" title="<?= htmlspecialchars($contract['object']) ?>">
                                    <?= htmlspecialchars($contract['object']) ?>
                                </td>
                                <td class="p-4 font-semibold text-slate-200">
                                    R$ <?= number_format((float)$contract['value'], 2, ',', '.') ?>
                                </td>
                                <td class="p-4 text-slate-400">
                                    <?= date('d/m/Y', strtotime($contract['end_date'])) ?>
                                </td>
                                <td class="p-4">
                                    <?php
                                    $statusClasses = [
                                        'vigente' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                                        'em_renovacao' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
                                        'vencido' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
                                    ];
                                    $statusLabels = [
                                        'vigente' => 'Vigente',
                                        'em_renovacao' => 'Em Renovação',
                                        'vencido' => 'Vencido',
                                    ];
                                    $class = $statusClasses[$contract['status']] ?? 'bg-slate-500/10 text-slate-400';
                                    $label = $statusLabels[$contract['status']] ?? $contract['status'];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $class ?>">
                                        <?= $label ?>
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button class="btn-edit-contract text-indigo-400 hover:text-indigo-300 p-1" title="Editar Contrato">
                                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        </button>
                                        <button class="btn-delete-contract text-rose-400 hover:text-rose-300 p-1" title="Excluir Contrato">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
