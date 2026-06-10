<?php $pageScript = 'sites'; ?>

<!-- Page Header -->
<div style="padding:28px 28px 0;" class="fade-up">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="page-title">Diretório de Sites</h1>
            <p class="page-subtitle">Links e sistemas corporativos centralizados</p>
        </div>
        <div style="display:flex;gap:10px;">
            <button id="btn-new-category-site" data-type="site" class="btn btn-secondary">
                <i data-lucide="folder-plus" style="width:14px;height:14px;"></i>
                Nova Pasta
            </button>
            <button id="btn-new-site" class="btn btn-primary">
                <i data-lucide="plus" style="width:14px;height:14px;"></i>
                Novo Site
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-bar" style="padding:0;margin-bottom:20px;">
        <div class="search-wrap">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" id="search-sites" placeholder="Buscar por nome ou descrição..." class="input-field search-input">
        </div>
        <select id="filter-category-site" class="select-field">
            <option value="">Todas as Pastas</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($activeCategory == $cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?> (<?= $cat['item_count'] ?>)
            </option>
            <?php endforeach; ?>
        </select>
        <select id="filter-type-site" class="select-field">
            <option value="">Todos os Tipos</option>
            <option value="1">💼 Interno</option>
            <option value="0">🌐 Externo</option>
        </select>
        <select id="filter-status-site" class="select-field">
            <option value="">Todos os Status</option>
            <option value="online">Online</option>
            <option value="offline">Offline</option>
        </select>
    </div>
</div>

<!-- Sites Grid -->
<?php if (empty($sites)): ?>
<div style="padding:0 28px 28px;" class="fade-up">
    <div class="empty-state" style="background:var(--bg-card);border:1px solid var(--border);border-radius:16px;padding:60px;">
        <i data-lucide="globe" style="width:48px;height:48px;opacity:0.2;"></i>
        <p style="font-size:14px;color:var(--text-muted);">Nenhum site cadastrado ainda.</p>
        <button onclick="$('#btn-new-site').click()" class="btn btn-primary" style="margin-top:8px;">
            <i data-lucide="plus" style="width:13px;height:13px;"></i>
            Adicionar primeiro site
        </button>
    </div>
</div>
<?php else: ?>
<div class="sites-grid fade-up" id="sites-grid">
    <?php foreach ($sites as $i => $site):
        $isOnline   = $site['status'] === 'online';
        $isInternal = $site['is_internal'];
    ?>
    <div class="site-card"
         data-name="<?= htmlspecialchars($site['name']) ?>"
         data-description="<?= htmlspecialchars($site['description'] ?? '') ?>"
         data-category="<?= $site['category_id'] ?>"
         data-type="<?= $site['is_internal'] ?>"
         data-status="<?= $site['status'] ?>"
         data-url="<?= htmlspecialchars($site['url']) ?>">

        <!-- Card Header -->
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="site-icon-wrap">
                    <i data-lucide="globe" style="width:20px;height:20px;color:var(--indigo-light);"></i>
                </div>
                <div>
                    <h3 style="font-size:14px;font-weight:700;color:var(--text-primary);line-height:1.2;"><?= htmlspecialchars($site['name']) ?></h3>
                    <?php if ($site['category_name']): ?>
                    <span style="font-size:11px;color:var(--text-muted);"><?= htmlspecialchars($site['category_name']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <div class="status-dot <?= $isOnline ? 'online' : 'offline' ?>" title="<?= $isOnline ? 'Online' : 'Offline' ?>"></div>
                <button class="action-btn btn-edit-site" data-id="<?= $site['id'] ?>" title="Editar">
                    <i data-lucide="edit-3" style="width:13px;height:13px;"></i>
                </button>
                <button class="action-btn danger btn-delete-site" data-id="<?= $site['id'] ?>" title="Excluir">
                    <i data-lucide="trash-2" style="width:13px;height:13px;"></i>
                </button>
            </div>
        </div>

        <!-- Description -->
        <p style="font-size:13px;color:var(--text-muted);line-height:1.5;margin-bottom:16px;min-height:40px;">
            <?= htmlspecialchars($site['description'] ?? 'Sem descrição disponível.') ?>
        </p>

        <!-- Footer -->
        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:14px;border-top:1px solid var(--border);">
            <div style="display:flex;align-items:center;gap:6px;">
                <?php if ($isInternal): ?>
                <span class="badge badge-indigo" style="font-size:10px;">💼 Interno</span>
                <?php else: ?>
                <span class="badge badge-gray" style="font-size:10px;">🌐 Externo</span>
                <?php endif; ?>
                <?php if (!$isOnline): ?>
                <span class="badge badge-red" style="font-size:10px;">Offline</span>
                <?php endif; ?>
            </div>
            <a href="<?= htmlspecialchars($site['url']) ?>" target="_blank"
               style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:600;color:var(--indigo-light);text-decoration:none;padding:5px 10px;border-radius:8px;background:var(--indigo-glow);border:1px solid rgba(99,102,241,0.2);transition:var(--transition);"
               onmouseover="this.style.background='rgba(99,102,241,0.2)'" onmouseout="this.style.background='var(--indigo-glow)'">
                Acessar
                <i data-lucide="external-link" style="width:11px;height:11px;"></i>
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
