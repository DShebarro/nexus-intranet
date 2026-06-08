<?php $pageScript = 'dashboard'; ?>
<div class="p-6">
    <h2 class="text-2xl font-bold mb-6">Dashboard</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Tarefas Ativas</p>
                    <p class="text-3xl font-bold mt-1"><?= $stats['active_tasks'] ?></p>
                </div>
                <i data-lucide="check-square" class="w-8 h-8 text-indigo-400"></i>
            </div>
        </div>
        
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Contratos Ativos</p>
                    <p class="text-3xl font-bold mt-1"><?= $stats['active_contracts'] ?></p>
                </div>
                <i data-lucide="file-text" class="w-8 h-8 text-emerald-400"></i>
            </div>
        </div>
        
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Valor Total</p>
                    <p class="text-3xl font-bold mt-1">R$ <?= number_format($stats['contracts_value'], 2, ',', '.') ?></p>
                </div>
                <i data-lucide="dollar-sign" class="w-8 h-8 text-amber-400"></i>
            </div>
        </div>
        
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-400 text-sm">Ações Hoje</p>
                    <p class="text-3xl font-bold mt-1"><?= $stats['today_actions'] ?></p>
                </div>
                <i data-lucide="activity" class="w-8 h-8 text-blue-400"></i>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
            <h3 class="font-bold mb-4">Atividades Recentes</h3>
            <div class="space-y-3">
                <?php foreach ($recentLogs as $log): ?>
                <div class="flex items-start space-x-3 text-sm">
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 mt-2"></div>
                    <div>
                        <p class="text-slate-300"><?= htmlspecialchars($log['description']) ?></p>
                        <p class="text-slate-500 text-xs"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
