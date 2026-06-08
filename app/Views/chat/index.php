<?php $pageScript = 'chat'; ?>
<div class="flex h-[calc(100vh-73px)] overflow-hidden">
    <!-- Sidebar de Canais/Conversas -->
    <aside class="w-80 bg-slate-950/20 border-r border-slate-800 flex flex-col">
        <div class="p-4 border-b border-slate-800">
            <h2 class="text-lg font-bold text-slate-100">Canais e Conversas</h2>
            <p class="text-xs text-slate-500 mt-1">Selecione um canal para interagir</p>
        </div>
        <nav class="flex-1 overflow-y-auto p-3 space-y-1">
            <?php foreach ($chats as $chat): ?>
                <?php
                $isAI = $chat['type'] === 'ai';
                $icon = $isAI ? 'bot' : 'hash';
                $iconColor = $isAI ? 'text-indigo-400' : 'text-slate-400';
                ?>
                <button class="w-full flex items-center space-x-3 px-3 py-3 rounded-xl text-left text-sm font-medium transition-all group chat-selector border border-transparent hover:bg-slate-800/50" 
                        data-slug="<?= htmlspecialchars($chat['slug']) ?>" 
                        data-title="<?= htmlspecialchars($chat['title']) ?>">
                    <div class="p-2 bg-slate-800 rounded-lg group-hover:bg-slate-700 transition-all">
                        <i data-lucide="<?= $icon ?>" class="w-4 h-4 <?= $iconColor ?>"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-slate-200 group-hover:text-white font-semibold truncate"><?= htmlspecialchars($chat['title']) ?></p>
                        <p class="text-xs text-slate-500 truncate"><?= $isAI ? 'Assistente Inteligente' : 'Canal Corporativo' ?></p>
                    </div>
                </button>
            <?php endforeach; ?>
        </nav>
    </aside>

    <!-- Área de Mensagens -->
    <main class="flex-1 flex flex-col bg-slate-900/40">
        <!-- Cabeçalho do Chat Ativo -->
        <div id="chat-header" class="px-6 py-4 border-b border-slate-800 bg-slate-950/10 flex items-center justify-between hidden">
            <div class="flex items-center space-x-3">
                <h3 id="chat-title" class="font-bold text-slate-200">Selecione um Chat</h3>
            </div>
        </div>

        <!-- Tela Vazia (Quando nenhum chat selecionado) -->
        <div id="empty-chat-state" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
            <div class="bg-indigo-600/10 p-6 rounded-full border border-indigo-500/20 mb-4 animate-bounce">
                <i data-lucide="message-square" class="w-12 h-12 text-indigo-400"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-200">Bem-vindo ao Chat da Nexus!</h3>
            <p class="text-sm text-slate-500 max-w-sm mt-1">Selecione uma conversa ou o assistente virtual Nexus AI no menu lateral para começar a conversar.</p>
        </div>

        <!-- Container de Mensagens -->
        <div id="chat-messages-container" class="flex-1 overflow-y-auto p-6 space-y-4 hidden">
            <!-- As mensagens serão inseridas aqui dinamicamente -->
        </div>

        <!-- Barra de Envio de Mensagem -->
        <form id="chat-input-form" class="p-4 border-t border-slate-800 bg-slate-950/20 hidden">
            <div class="flex items-center space-x-3">
                <input type="text" id="chat-message-input" placeholder="Digite sua mensagem..." autocomplete="off" required
                       class="flex-1 bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
                <button type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-500 text-white p-3 rounded-xl transition-all shadow-lg flex items-center justify-center">
                    <i data-lucide="send" class="w-5 h-5"></i>
                </button>
            </div>
        </form>
    </main>
</div>
