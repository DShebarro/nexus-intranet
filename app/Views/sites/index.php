<?php $pageScript = 'sites'; ?>
<div class="flex h-full">
    <!-- Sidebar de Pastas -->
    <aside class="w-72 bg-slate-950/30 border-r border-slate-800 p-4 overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Pastas</h3>
            <button id="btn-new-category-site" 
                    data-type="site"
                    class="text-slate-400 hover:text-indigo-400 transition-colors">
                <i data-lucide="folder-plus" class="w-4 h-4"></i>
            </button>
        </div>
        
        <nav class="space-y-1">
            <a href="/sites" 
               class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors
                      <?= !$activeCategory ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800' ?>">
                <i data-lucide="folder" class="w-4 h-4"></i>
                <span class="flex-1 text-sm">Todos os Sites</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-700">
                    <?= array_sum(array_column($categories, 'item_count')) ?>
                </span>
            </a>
            
            <?php foreach ($categories as $cat): ?>
            <a href="/sites?category_id=<?= $cat['id'] ?>" 
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
                <?= $activeCategory ? 'Sites - ' . htmlspecialchars($categories[array_search($activeCategory, array_column($categories, 'id'))]['name'] ?? '') : 'Todos os Sites' ?>
            </h2>
            <button id="btn-new-site" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-sm font-semibold">
                + Novo Site
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($sites as $site): ?>
            <div class="bg-slate-900/50 border border-slate-800 rounded-xl p-6 hover:border-indigo-500/50 transition-all">
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
                        <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                        <?php else: ?>
                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                        <?php endif; ?>
                        <button class="btn-delete-site text-rose-400 hover:text-rose-300" data-id="<?= $site['id'] ?>">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
                <p class="text-sm text-slate-400 mb-3"><?= htmlspecialchars($site['description'] ?? 'Sem descrição') ?></p>
                <a href="<?= htmlspecialchars($site['url']) ?>" target="_blank" 
                   class="text-sm text-indigo-400 hover:text-indigo-300 flex items-center space-x-1">
                    <span>Acessar</span>
                    <i data-lucide="external-link" class="w-3 h-3"></i>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div id="modal-container" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-slate-900 rounded-2xl p-6 w-full max-w-md border border-slate-700">
        <div id="modal-body"></div>
    </div>
</div>
