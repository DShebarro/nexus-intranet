<?php $pageScript = 'logs'; ?>
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold">Logs de Atividades</h2>
            <p class="text-slate-400 text-sm mt-1">Registros de ações e acessos no sistema.</p>
        </div>
        <div class="flex items-center space-x-3">
            <button id="btn-clear-logs" class="bg-rose-600/10 hover:bg-rose-600 border border-rose-500/20 hover:border-rose-500 text-rose-400 hover:text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all">
                Limpar Logs
            </button>
            <div class="bg-slate-800/50 border border-slate-700 px-4 py-2 rounded-xl text-sm font-semibold">
                <span id="today-actions-count" class="text-indigo-400"><?= $todayCount ?></span> ações hoje
            </div>
        </div>
    </div>

    <!-- Filtros e Busca -->
    <div class="flex flex-wrap gap-4 mb-6 bg-slate-900/50 p-4 rounded-2xl border border-slate-850">
        <div class="flex-1 min-w-[200px]">
            <input type="text" id="search-log" placeholder="Buscar por descrição, IP ou dispositivo..." 
                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
        </div>
        <div class="w-48">
            <select id="filter-log-type" 
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors">
                <option value="all">Todos os Tipos</option>
                <option value="info">Info</option>
                <option value="warning">Warning</option>
                <option value="task">Task</option>
                <option value="navigation">Navigation</option>
            </select>
        </div>
    </div>

    <!-- Tabela de Logs -->
    <div class="bg-slate-800/30 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="p-4 border-b border-slate-800 bg-slate-900/50">
            <h3 class="font-bold text-sm text-slate-300">Histórico Recente</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 text-xs font-semibold uppercase bg-slate-950/20">
                        <th class="p-4 w-40">Data / Hora</th>
                        <th class="p-4 w-32">Tipo</th>
                        <th class="p-4">Descrição</th>
                        <th class="p-4 w-36">IP</th>
                        <th class="p-4 w-64">Dispositivo / Agente</th>
                    </tr>
                </thead>
                <tbody id="logs-table-body" class="divide-y divide-slate-800/50 text-sm">
                    <?php if (empty($logs)): ?>
                        <tr id="no-logs-row">
                            <td colspan="5" class="p-8 text-center text-slate-500">Nenhum log registrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="hover:bg-slate-800/20 transition-colors log-row"
                                data-type="<?= htmlspecialchars($log['type']) ?>"
                                data-desc="<?= htmlspecialchars($log['description']) ?>"
                                data-ip="<?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?>"
                                data-ua="<?= htmlspecialchars($log['user_agent'] ?? '') ?>">
                                <td class="p-4 text-slate-400 font-mono">
                                    <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                                </td>
                                <td class="p-4">
                                    <?php
                                    $typeClasses = [
                                        'warning' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
                                        'task' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
                                        'navigation' => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
                                    ];
                                    $class = $typeClasses[$log['type']] ?? 'bg-slate-700/50 text-slate-400 border border-slate-700';
                                    ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold uppercase tracking-wider <?= $class ?>">
                                        <?= htmlspecialchars($log['type']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-slate-200">
                                    <?= htmlspecialchars($log['description']) ?>
                                </td>
                                <td class="p-4 font-mono text-xs text-slate-400">
                                    <?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?>
                                </td>
                                <td class="p-4 text-xs text-slate-500 truncate max-w-xs" title="<?= htmlspecialchars($log['user_agent'] ?? '') ?>">
                                    <?= htmlspecialchars($log['user_agent'] ?? 'Desconhecido') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
