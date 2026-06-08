<?php $pageScript = 'sites'; ?>
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Links Rápidos e Sistemas</h2>
        <button id="btn-new-site" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-sm font-semibold shadow-lg">
            + Novo Site
        </button>
    </div>

    <!-- Grid de Sites -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($sites)): ?>
            <div class="col-span-full bg-slate-800/20 border border-slate-800 p-8 rounded-2xl text-center text-slate-500">
                Nenhum sistema cadastrado.
            </div>
        <?php else: ?>
            <?php foreach ($sites as $site): ?>
                <div class="site-card bg-slate-850 border border-slate-800 hover:border-slate-700 p-6 rounded-2xl flex flex-col justify-between hover:translate-y-[-2px] transition-all duration-300 shadow-xl group"
                     data-id="<?= $site['id'] ?>"
                     data-name="<?= htmlspecialchars($site['name']) ?>"
                     data-url="<?= htmlspecialchars($site['url']) ?>"
                     data-description="<?= htmlspecialchars($site['description'] ?? '') ?>"
                     data-internal="<?= (int) $site['is_internal'] ?>"
                     data-status="<?= htmlspecialchars($site['status']) ?>">
                    <div>
                        <!-- Cabeçalho do Card: Status e Tipo -->
                        <div class="flex items-center justify-between mb-4">
                            <!-- Status Online/Offline -->
                            <div class="flex items-center space-x-2">
                                <?php if ($site['status'] === 'online'): ?>
                                    <span class="relative flex h-2.5 w-2.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                    </span>
                                    <span class="text-xs text-emerald-400 font-medium">Online</span>
                                <?php else: ?>
                                    <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
                                    <span class="text-xs text-rose-400 font-medium">Offline</span>
                                <?php endif; ?>
                            </div>

                            <!-- Tag Interno/Externo e Ações -->
                            <div class="flex items-center space-x-2">
                                <?php if ($site['is_internal']): ?>
                                    <span class="bg-blue-500/10 text-blue-400 border border-blue-500/20 text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-md">
                                        Interno
                                    </span>
                                <?php else: ?>
                                    <span class="bg-slate-700/50 text-slate-400 border border-slate-700/80 text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-md">
                                        Externo
                                    </span>
                                <?php endif; ?>
                                
                                <button class="btn-edit-site text-indigo-400 hover:text-indigo-300 p-0.5 transition-colors" title="Editar Link">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                </button>
                                <button class="btn-delete-site text-rose-400 hover:text-rose-300 p-0.5 transition-colors" title="Excluir Link">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Nome e URL -->
                        <h3 class="text-lg font-bold text-slate-100 group-hover:text-indigo-400 transition-colors mb-2">
                            <?= htmlspecialchars($site['name']) ?>
                        </h3>
                        <p class="text-xs text-slate-500 font-mono mb-3 truncate" title="<?= htmlspecialchars($site['url']) ?>">
                            <?= htmlspecialchars($site['url']) ?>
                        </p>

                        <!-- Descrição -->
                        <p class="text-sm text-slate-400 mb-6 line-clamp-2" title="<?= htmlspecialchars($site['description'] ?? '') ?>">
                            <?= htmlspecialchars($site['description'] ?? 'Sem descrição disponível.') ?>
                        </p>
                    </div>

                    <!-- Botão de Acesso -->
                    <a href="<?= htmlspecialchars($site['url']) ?>" target="_blank" rel="noopener noreferrer" 
                       class="w-full bg-slate-800 hover:bg-indigo-600 text-white text-center py-2.5 rounded-xl text-sm font-semibold transition-all flex items-center justify-center space-x-2">
                        <span>Acessar</span>
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
