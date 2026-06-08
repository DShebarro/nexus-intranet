<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$navItems = [
    ['path' => '/dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
    ['path' => '/tasks', 'icon' => 'check-square', 'label' => 'Tarefas'],
    ['path' => '/contracts', 'icon' => 'file-text', 'label' => 'Contratos'],
    ['path' => '/sites', 'icon' => 'globe', 'label' => 'Sites'],
    ['path' => '/chat', 'icon' => 'message-square', 'label' => 'Chat'],
    ['path' => '/logs', 'icon' => 'history', 'label' => 'Logs'],
];
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Intranet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col">

<header class="bg-slate-950/80 border-b border-slate-800 backdrop-blur-md px-6 py-4 flex items-center justify-between">
    <div class="flex items-center space-x-3">
        <div class="bg-gradient-to-tr from-blue-600 to-indigo-500 p-2.5 rounded-xl">
            <i data-lucide="layers" class="w-6 h-6 text-white"></i>
        </div>
        <h1 class="text-lg font-bold">NEXUS INTRANET</h1>
    </div>
    <div class="text-sm text-slate-400">Carlos Silva — Diretor de TI</div>
</header>

<div class="flex flex-1 overflow-hidden">
    <aside class="w-64 bg-slate-950/40 border-r border-slate-800 p-4 hidden md:flex flex-col">
        <nav class="space-y-1">
        <?php foreach ($navItems as $item): ?>
            <?php $active = str_starts_with($currentPath, $item['path']); ?>
            <a href="<?= $item['path'] ?>"
               class="<?= $active ? 'bg-indigo-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' ?>
                      flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all">
                <i data-lucide="<?= $item['icon'] ?>" class="w-4 h-4"></i>
                <span><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>
        </nav>
    </aside>

    <main class="flex-1 overflow-y-auto">
