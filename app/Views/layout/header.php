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

    <!-- Status Badge -->
    <div style="display:flex;align-items:center;gap:6px;padding:5px 12px;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.15);border-radius:99px;">
        <div style="width:6px;height:6px;background:#10b981;border-radius:50%;animation:pulse-green 2s infinite;"></div>
        <span style="font-size:11px;font-weight:600;color:#34d399;">Sistema Online</span>
    </div>

    <!-- User -->
    <div class="header-user">
        <div class="header-user-avatar">CS</div>
        <div class="header-user-info">
            <div class="header-user-name">Carlos Silva</div>
            <div class="header-user-role">Diretor de TI</div>
        </div>
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
