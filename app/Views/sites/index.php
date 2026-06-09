<?php $pageScript = 'sites'; ?>
<div class="p-6">
    <!-- Barra de A√ß√µes Superior -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Diret√≥rio de Sites</h2>
            <div class="flex space-x-3">
                <button id="btn-new-category-site" 
                        data-type="site"
                        class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2 rounded-xl text-sm font-semibold transition-colors flex items-center space-x-2">
                    <i data-lucide="folder-plus" class="w-4 h-4"></i>
                    <span>Nova Pasta</span>
                </button>
                <button id="btn-new-site" 
                        class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors flex items-center space-x-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Novo Site</span>
                </button>
            </div>
        </div>
        
        <!-- Barra de Pesquisa e Filtros -->
        <div class="flex items-center space-x-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" 
                       id="search-sites" 
                       placeholder="Buscar sites por nome ou descri√ß√£o..." 
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-10 pr-4 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
            </div>
            <select id="filter-category-site" class="bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500">
                <option value="">Todas as Pastas</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($activeCategory == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?> (<?= $cat['item_count'] ?>)
                </option>
                <?php endforeach; ?>
            </select>
            <select id="filter-type-site" class="bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500">
                <option value="">Todos os Tipos</option>
                <option value="1">Interno</option>
                <option value="0">Externo</option>
            </select>
            <select id="filter-status-site" class="bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:border-indigo-500">
                <option value="">Todos Status</option>
                <option value="online">Online</option>
                <option value="offline">Offline</option>
            </select>
        </div>
    </div>

    <!-- Grid de Sites -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="sites-grid">
        <?php foreach ($sites as $site): ?>
        <div class="site-card bg-slate-900/50 border border-slate-800 rounded-xl p-6 hover:border-indigo-500/50 transition-all"
             data-name="<?= htmlspecialchars($site['name']) ?>"
             data-description="<?= htmlspecialchars($site['description'] ?? '') ?>"
             data-category="<?= $site['category_id'] ?>"
             data-type="<?= $site['is_internal'] ?>"
             data-status="<?= $site['status'] ?>">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-500/20 rounded-lg flex items-center justify-center">
                        <i data-lucide="globe" class="w-5 h-5 text-indigo-400"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-200"><?= htmlspecialchars($site['name']) ?></h3>
                        <?php if ($site['category_name']): ?>
                        <span class="text-xs text-indigo-400"><?= htmlspecialchars($site['category_name']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <?php if ($site['status'] === 'online'): ?>
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse" title="Online"></div>
                    <?php else: ?>
                    <div class="w-2 h-2 rounded-full bg-red-500" title="Offline"></div>
                    <?php endif; ?>
                    <button class="btn-edit-site text-slate-400 hover:text-white" data-id="<?= $site['id'] ?>">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </button>
                    <button class="btn-delete-site text-rose-400 hover:text-rose-300" data-id="<?= $site['id'] ?>">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            <p class="text-sm text-slate-400 mb-4"><?= htmlspecialchars($site['description'] ?? 'Sem descri√ß√£o') ?></p>
            <div class="flex items-center justify-between">
                <span class="text-xs px-2 py-1 rounded-full bg-slate-800 text-slate-400">
                    <?= $site['is_internal'] ? 'Ì¥í Interno' : 'Ìºê Externo' ?>
                </span>
                <a href="<?= htmlspecialchars($site['url']) ?>" target="_blank" 
                   class="text-sm text-indigo-400 hover:text-indigo-300 flex items-center space-x-1">
                    <span>Acessar</span>
                    <i data-lucide="external-link" class="w-3 h-3"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="modal-container" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-slate-900 rounded-2xl p-6 w-full max-w-md border border-slate-700">
        <div id="modal-body"></div>
    </div>
</div>
