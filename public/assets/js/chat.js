$(function () {
    let activeChatSlug = null;
    let pollInterval = null;

    $('.chat-selector').on('click', function () {
        const slug  = $(this).data('slug');
        const title = $(this).data('title');

        // Highlight selected chat
        $('.chat-selector').css({
            'background': 'transparent',
            'border-color': 'transparent',
            'color': 'var(--text-muted)'
        });
        $(this).css({
            'background': 'var(--bg-elevated)',
            'border-color': 'var(--border-strong)',
            'color': 'var(--text-primary)'
        });

        // Show Chat Frame
        $('#empty-chat-state').hide();
        $('#chat-header').css('display', 'flex').removeClass('hidden');
        $('#chat-title').text(title);
        $('#chat-messages-container').css('display','flex').removeClass('hidden').css('flex-direction','column');
        $('#chat-input-form').css('display','block').removeClass('hidden');

        activeChatSlug = slug;
        loadMessages(slug, true);

        // Polling every 3s
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(function () {
            if (activeChatSlug === slug) loadMessages(slug, false);
        }, 3000);
    });

    $('#chat-input-form').on('submit', function (e) {
        e.preventDefault();
        if (!activeChatSlug) return;

        const input = $('#chat-message-input');
        const content = input.val().trim();
        if (content === '') return;

        input.val('');

        api.post(`/api/chats/${activeChatSlug}/messages`, { content: content })
            .done(function () {
                loadMessages(activeChatSlug, true);
            })
            .fail(function () {
                showToast('Erro ao enviar mensagem', 'error');
            });
    });

    function loadMessages(slug, shouldScroll) {
        api.get(`/api/chats/${slug}/messages`)
            .done(function (messages) {
                renderMessages(messages, shouldScroll);
            })
            .fail(function () {
                console.error('Failed to load messages');
            });
    }

    function renderMessages(messages, shouldScroll) {
        const container = $('#chat-messages-container');
        const oldScrollHeight = container[0].scrollHeight;
        const oldScrollTop = container.scrollTop();
        const wasAtBottom = (container.height() + oldScrollTop >= oldScrollHeight - 50);

        container.empty();

        if (messages.length === 0) {
            container.html(`
                <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:10px;color:var(--text-faint);">
                    <i data-lucide="message-circle" style="width:36px;height:36px;opacity:0.2;"></i>
                    <p style="font-size:13px;">Nenhuma mensagem ainda. Diga olá!</p>
                </div>
            `);
            if (typeof lucide !== 'undefined') lucide.createIcons();
            return;
        }

        messages.forEach(function (msg) {
            const isUser  = msg.sender_type === 'user';
            const timeStr = formatTime(msg.sent_at);

            const bubbleStyle = isUser
                ? 'background:linear-gradient(135deg,#6366f1,#7c3aed);color:white;border-radius:16px 16px 4px 16px;box-shadow:0 4px 12px rgba(99,102,241,0.25);padding:12px 16px;'
                : 'background:var(--bg-elevated);color:var(--text-primary);border:1px solid var(--border);border-radius:16px 16px 16px 4px;padding:12px 16px;';

            const html = `
                <div style="display:flex;flex-direction:column;align-items:${isUser ? 'flex-end' : 'flex-start'};gap:4px;">
                    <div style="font-size:11px;color:var(--text-faint);padding:0 4px;">
                        <span style="font-weight:600;color:var(--text-muted);">${escapeHtml(msg.sender_name)}</span>
                        &nbsp;·&nbsp; ${timeStr}
                    </div>
                    <div class="chat-bubble" style="${bubbleStyle}font-size:13px;line-height:1.6;max-width:70%;word-break:break-word;">
                        ${escapeHtml(msg.content)}
                    </div>
                </div>
            `;
            container.append(html);
        });

        if (shouldScroll || wasAtBottom) {
            container.scrollTop(container[0].scrollHeight);
        }
    }


    function formatTime(dateTimeStr) {
        // Formatos aceitos: YYYY-MM-DD HH:MM:SS
        try {
            const parts = dateTimeStr.split(' ');
            if (parts.length === 2) {
                const timeParts = parts[1].split(':');
                return timeParts[0] + ':' + timeParts[1];
            }
            return dateTimeStr;
        } catch (e) {
            return dateTimeStr;
        }
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
