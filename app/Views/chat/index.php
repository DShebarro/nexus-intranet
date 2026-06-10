<?php $pageScript = 'chat'; ?>

<div style="display:flex;height:calc(100vh - 60px);overflow:hidden;">

    <!-- Channels Sidebar -->
    <aside style="width:260px;flex-shrink:0;background:var(--bg-surface);border-right:1px solid var(--border);display:flex;flex-direction:column;">
        <!-- Header -->
        <div style="padding:20px;border-bottom:1px solid var(--border);">
            <h2 style="font-size:14px;font-weight:700;color:var(--text-primary);">Canais & Conversas</h2>
            <p style="font-size:11px;color:var(--text-faint);margin-top:3px;">Selecione para começar</p>
        </div>

        <!-- Channel List -->
        <nav style="flex:1;overflow-y:auto;padding:10px;">
            <?php foreach ($chats as $chat):
                $isAI = $chat['type'] === 'ai';
            ?>
            <button class="chat-selector w-full"
                    data-slug="<?= htmlspecialchars($chat['slug']) ?>"
                    data-title="<?= htmlspecialchars($chat['title']) ?>"
                    style="display:flex;align-items:center;gap:10px;width:100%;padding:10px 12px;border-radius:10px;text-align:left;background:transparent;border:1px solid transparent;cursor:pointer;transition:var(--transition);margin-bottom:4px;">
                <div style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:<?= $isAI ? 'rgba(99,102,241,0.15)' : 'rgba(255,255,255,0.05)' ?>;border:1px solid <?= $isAI ? 'rgba(99,102,241,0.25)' : 'var(--border)' ?>;">
                    <i data-lucide="<?= $isAI ? 'bot' : 'hash' ?>" style="width:16px;height:16px;color:<?= $isAI ? '#818cf8' : 'var(--text-muted)' ?>;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:var(--text-primary);line-height:1.2;"><?= htmlspecialchars($chat['title']) ?></div>
                    <div style="font-size:11px;color:var(--text-faint);margin-top:1px;"><?= $isAI ? 'Assistente Inteligente' : 'Canal Corporativo' ?></div>
                </div>
                <?php if ($isAI): ?>
                <div style="width:6px;height:6px;background:#10b981;border-radius:50;flex-shrink:0;animation:pulse-green 2s infinite;"></div>
                <?php endif; ?>
            </button>
            <?php endforeach; ?>
        </nav>

        <!-- Bottom Info -->
        <div style="padding:12px;border-top:1px solid var(--border);">
            <div style="display:flex;align-items:center;gap:8px;padding:10px;background:var(--indigo-glow);border:1px solid rgba(99,102,241,0.2);border-radius:10px;">
                <i data-lucide="shield-check" style="width:14px;height:14px;color:var(--indigo-light);flex-shrink:0;"></i>
                <span style="font-size:11px;color:var(--text-muted);line-height:1.3;">Comunicação segura e criptografada</span>
            </div>
        </div>
    </aside>

    <!-- Chat Main Area -->
    <main style="flex:1;display:flex;flex-direction:column;background:var(--bg-base);overflow:hidden;">

        <!-- Chat Header -->
        <div id="chat-header" class="hidden" style="padding:16px 24px;border-bottom:1px solid var(--border);background:var(--bg-surface);display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div id="chat-header-icon" style="width:36px;height:36px;border-radius:10px;background:var(--indigo-glow);border:1px solid rgba(99,102,241,0.2);display:flex;align-items:center;justify-content:center;">
                    <i data-lucide="message-square" style="width:16px;height:16px;color:var(--indigo-light);"></i>
                </div>
                <div>
                    <h3 id="chat-title" style="font-size:14px;font-weight:700;color:var(--text-primary);">Canal</h3>
                    <div style="font-size:11px;color:var(--emerald);display:flex;align-items:center;gap:4px;">
                        <div style="width:5px;height:5px;background:var(--emerald);border-radius:50%;animation:pulse-green 2s infinite;"></div>
                        Online
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div id="empty-chat-state" style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px;text-align:center;">
            <div style="width:72px;height:72px;background:var(--indigo-glow);border:1px solid rgba(99,102,241,0.2);border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                <i data-lucide="message-square" style="width:32px;height:32px;color:var(--indigo-light);"></i>
            </div>
            <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:8px;">Bem-vindo ao Chat Nexus</h3>
            <p style="font-size:13px;color:var(--text-muted);max-width:300px;line-height:1.6;">Selecione um canal ou o assistente Nexus AI no painel lateral para começar a conversar.</p>
        </div>

        <!-- Messages Container -->
        <div id="chat-messages-container" class="hidden" style="flex:1;overflow-y:auto;padding:20px;display:flex;flex-direction:column;gap:12px;"></div>

        <!-- Input Form -->
        <form id="chat-input-form" class="hidden" style="padding:16px 20px;border-top:1px solid var(--border);background:var(--bg-surface);">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="flex:1;display:flex;align-items:center;gap:10px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:12px;padding:4px 4px 4px 16px;transition:var(--transition);" onfocusin="this.style.borderColor='rgba(99,102,241,0.5)';this.style.boxShadow='0 0 0 3px rgba(99,102,241,0.1)'" onfocusout="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
                    <input type="text" id="chat-message-input" placeholder="Escreva sua mensagem..." autocomplete="off" required
                           style="flex:1;background:transparent;border:none;outline:none;font-size:13px;color:var(--text-primary);font-family:inherit;" />
                    <button type="submit" style="width:36px;height:36px;background:var(--indigo);border:none;border-radius:9px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:var(--transition);flex-shrink:0;" onmouseover="this.style.background='var(--indigo-light)'" onmouseout="this.style.background='var(--indigo)'">
                        <i data-lucide="send" style="width:15px;height:15px;color:white;"></i>
                    </button>
                </div>
            </div>
        </form>
    </main>
</div>
