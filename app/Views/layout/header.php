<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$navItems = [
    ['path' => '/dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
    ['path' => '/tasks',     'icon' => 'check-square',     'label' => 'Tarefas'],
    ['path' => '/contracts', 'icon' => 'file-text',        'label' => 'Contratos'],
    ['path' => '/sites',     'icon' => 'globe',            'label' => 'Sites'],
    ['path' => '/chat',      'icon' => 'message-square',   'label' => 'Chat'],
    ['path' => '/logs',      'icon' => 'activity',         'label' => 'Logs'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= \App\Core\Csrf::meta() ?>
    <title>Nexus Intranet</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
          }
        }
      }
    </script>
</head>
<body>
<div id="app-wrapper">

<!-- Top Header -->
<header id="top-header">
    <a href="/dashboard" class="header-logo">
        <div class="header-logo-icon">
            <i data-lucide="layers" style="width:18px;height:18px;color:white;"></i>
        </div>
        <span class="header-logo-text">NEXUS INTRANET</span>
    </a>

    <div class="header-spacer"></div>

    <!-- Busca Global -->
    <div id="global-search-wrap" style="position:relative;max-width:280px;width:100%;margin-right:12px;">
        <input type="text" id="global-search" placeholder="Buscar tarefas, contratos, sites..."
               style="width:100%;background:var(--bg-elevated);border:1px solid var(--border);border-radius:10px;padding:7px 12px 7px 34px;color:var(--text-primary);font-size:12px;outline:none;box-sizing:border-box;">
        <i data-lucide="search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--text-faint);"></i>
        <div id="search-results" class="hidden" style="position:absolute;top:calc(100% + 6px);left:0;right:0;background:var(--bg-card);border:1px solid var(--border-strong);border-radius:12px;max-height:320px;overflow-y:auto;z-index:100;box-shadow:var(--shadow-lg);"></div>
    </div>

    <!-- Notificações -->
    <div id="notifications-wrap" style="position:relative;margin-right:12px;">
        <button id="btn-notifications" type="button" title="Notificações"
                style="position:relative;width:36px;height:36px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--text-muted);">
            <i data-lucide="bell" style="width:16px;height:16px;"></i>
            <span id="notif-badge" class="hidden" style="position:absolute;top:-4px;right:-4px;min-width:16px;height:16px;background:#f43f5e;color:white;font-size:10px;font-weight:700;border-radius:99px;display:flex;align-items:center;justify-content:center;padding:0 4px;">0</span>
        </button>
        <div id="notif-dropdown" class="hidden" style="position:absolute;top:calc(100% + 8px);right:0;width:320px;background:var(--bg-card);border:1px solid var(--border-strong);border-radius:14px;z-index:100;box-shadow:var(--shadow-lg);overflow:hidden;">
            <div style="padding:14px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:13px;font-weight:600;color:var(--text-primary);">Notificações</span>
                <button id="btn-mark-all-read" type="button" style="font-size:11px;color:var(--indigo-light);background:none;border:none;cursor:pointer;">Marcar todas</button>
            </div>
            <div id="notif-list" style="max-height:300px;overflow-y:auto;"></div>
        </div>
    </div>

    <!-- Status Badge -->
    <div style="display:flex;align-items:center;gap:6px;padding:5px 12px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.15);border-radius:99px;">
        <div style="width:6px;height:6px;background:#10b981;border-radius:50%;animation:pulse-green 2s infinite;"></div>
        <span style="font-size:11px;font-weight:600;color:#34d399;">Sistema Online</span>
    </div>

    <!-- User -->
    <?php $user = $currentUser ?? null; ?>
    <div class="header-user" style="display:flex;align-items:center;gap:12px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div class="header-user-avatar"><?= e($user['avatar'] ?? 'US') ?></div>
            <div class="header-user-info">
                <div class="header-user-name"><?= e($user['name'] ?? 'Usuário') ?></div>
                <div class="header-user-role"><?= e($user['role'] ?? 'usuario') ?></div>
            </div>
        </div>
        <form method="POST" action="/logout" style="margin:0;">
            <?= \App\Core\Csrf::field() ?>
            <button type="submit" title="Sair" style="background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;padding:6px 10px;cursor:pointer;color:var(--text-muted);font-size:12px;">
                Sair
            </button>
        </form>
    </div>
</header>

<div id="body-layout">
<!-- Sidebar -->
<aside id="sidebar">
    <div class="nav-section-label">Menu Principal</div>
    <?php foreach ($navItems as $item): ?>
        <?php
        if ($item['path'] === '/dashboard') {
            $active = ($currentPath === '/' || $currentPath === '/dashboard' || str_starts_with($currentPath, '/dashboard/'));
        } else {
            $active = ($currentPath === $item['path'] || str_starts_with($currentPath, $item['path'] . '/'));
        }
        ?>
        <a href="<?= $item['path'] ?>" class="nav-item <?= $active ? 'active' : '' ?>">
            <i data-lucide="<?= $item['icon'] ?>" class="nav-icon"></i>
            <?= $item['label'] ?>
        </a>
    <?php endforeach; ?>

    <div style="flex:1;"></div>
    <div class="nav-section-label" style="margin-top:8px;">Sistema</div>
    <div style="padding:10px 12px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:10px;margin:0 0 4px;">
        <div style="font-size:11px;color:var(--text-faint);margin-bottom:4px;">Versão</div>
        <div style="font-size:12px;font-weight:600;color:var(--text-muted);">v2.1.0 — Estável</div>
    </div>
</aside>

<!-- Main Content -->
<main id="main-content">
