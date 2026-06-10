$(function () {
    let activeChatSlug = null;
    let pollInterval = null;

    $('.chat-selector').on('click', function () {
        const slug = $(this).data('slug');
        const title = $(this).data('title');

        // Highlight selected chat
        $('.chat-selector').removeClass('bg-slate-800/80 border-slate-700').addClass('border-transparent');
        $(this).addClass('bg-slate-800/80 border-slate-700').removeClass('border-transparent');

        // Setup Chat Frame
        $('#empty-chat-state').addClass('hidden');
        $('#chat-header').removeClass('hidden');
        $('#chat-title').text(title);
        $('#chat-messages-container').removeClass('hidden');
        $('#chat-input-form').removeClass('hidden');

        activeChatSlug = slug;
        loadMessages(slug, true);

        // Setup Polling (every 3 seconds)
        if (pollInterval) clearInterval(pollInterval);
        pollInterval = setInterval(function () {
            if (activeChatSlug === slug) {
                loadMessages(slug, false);
            }
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
                <div class="flex flex-col items-center justify-center h-full text-slate-500 text-sm">
                    <p>Nenhuma mensagem por aqui ainda.</p>
                    <p class="text-xs mt-1">Envie uma mensagem abaixo para iniciar a conversa!</p>
                </div>
            `);
            return;
        }

        messages.forEach(function (msg) {
            const isUser = msg.sender_type === 'user';
            const alignClass = isUser ? 'justify-end' : 'justify-start';
            const bubbleBg = isUser ? 'bg-indigo-600 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-slate-800 text-slate-100 rounded-r-2xl rounded-tl-2xl border border-slate-700/50';
            const timeStr = formatTime(msg.sent_at);

            const html = `
                <div class="flex ${alignClass} space-x-2">
                    <div class="max-w-[70%]">
                        <div class="text-[11px] text-slate-500 mb-1 px-1 flex items-center space-x-1 ${isUser ? 'justify-end' : 'justify-start'}">
                            <span class="font-semibold text-slate-400">${escapeHtml(msg.sender_name)}</span>
                            <span>•</span>
                            <span>${timeStr}</span>
                        </div>
                        <div class="px-4 py-3 text-sm shadow-md leading-relaxed ${bubbleBg}">
                            ${escapeHtml(msg.content)}
                        </div>
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
