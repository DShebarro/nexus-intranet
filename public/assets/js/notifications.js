// ============================================================
// NEXUS INTRANET — Notificações + Busca Global
// ============================================================

(function () {
    // ---- Notificações ----
    let notifOpen = false;

    function loadNotifications() {
        $.getJSON('/api/notifications').done(function (data) {
            const count = data.unread_count || 0;
            const badge = $('#notif-badge');
            if (count > 0) {
                badge.text(count > 9 ? '9+' : count).removeClass('hidden');
            } else {
                badge.addClass('hidden');
            }
            renderNotifications(data.notifications || []);
        });
    }

    function renderNotifications(items) {
        const list = $('#notif-list');
        if (!items.length) {
            list.html('<div style="padding:24px;text-align:center;color:var(--text-faint);font-size:12px;">Nenhuma notificação</div>');
            return;
        }
        list.html(items.map(function (n) {
            const unread = !n.read_at;
            return `<a href="${n.link || '#'}" data-notif-id="${n.id}" class="notif-item"
                style="display:block;padding:12px 16px;border-bottom:1px solid var(--border);text-decoration:none;${unread ? 'background:rgba(99,102,241,0.06);' : ''}">
                <div style="font-size:12px;font-weight:600;color:var(--text-primary);">${escapeHtml(n.title)}</div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">${escapeHtml(n.message)}</div>
            </a>`;
        }).join(''));
    }

    $('#btn-notifications').on('click', function (e) {
        e.stopPropagation();
        notifOpen = !notifOpen;
        $('#notif-dropdown').toggleClass('hidden', !notifOpen);
        if (notifOpen) loadNotifications();
    });

    $('#btn-mark-all-read').on('click', function () {
        api.post('/api/notifications/read-all', {}).done(loadNotifications);
    });

    $(document).on('click', '.notif-item', function () {
        const id = $(this).data('notif-id');
        api.patch('/api/notifications/' + id + '/read', {});
    });

    $(document).on('click', function () {
        if (notifOpen) {
            notifOpen = false;
            $('#notif-dropdown').addClass('hidden');
        }
        $('#search-results').addClass('hidden');
    });

    $('#notif-dropdown').on('click', function (e) { e.stopPropagation(); });

    loadNotifications();
    setInterval(loadNotifications, 60000);

    // ---- Busca Global ----
    let searchTimer = null;

    $('#global-search').on('input', function () {
        clearTimeout(searchTimer);
        const q = $(this).val().trim();
        if (q.length < 2) {
            $('#search-results').addClass('hidden');
            return;
        }
        searchTimer = setTimeout(function () {
            $.getJSON('/api/search?q=' + encodeURIComponent(q)).done(function (data) {
                renderSearchResults(data.results || []);
            });
        }, 300);
    });

    $('#global-search-wrap').on('click', function (e) { e.stopPropagation(); });

    function renderSearchResults(results) {
        const box = $('#search-results');
        if (!results.length) {
            box.html('<div style="padding:16px;font-size:12px;color:var(--text-faint);">Nenhum resultado</div>').removeClass('hidden');
            return;
        }
        const typeLabels = { task: 'Tarefa', contract: 'Contrato', site: 'Site' };
        const typeLinks = { task: '/tasks', contract: '/contracts', site: '/sites' };
        box.html(results.map(function (r) {
            const label = typeLabels[r.result_type] || r.result_type;
            const link = typeLinks[r.result_type] || '#';
            const title = r.title || r.code || r.name || '—';
            const sub = r.partner || r.url || r.description || '';
            return `<a href="${link}" style="display:block;padding:10px 14px;border-bottom:1px solid var(--border);text-decoration:none;" onmouseover="this.style.background='var(--bg-elevated)'" onmouseout="this.style.background='transparent'">
                <div style="font-size:10px;font-weight:600;color:var(--indigo-light);text-transform:uppercase;">${label}</div>
                <div style="font-size:12px;font-weight:600;color:var(--text-primary);">${escapeHtml(String(title))}</div>
                ${sub ? `<div style="font-size:11px;color:var(--text-faint);">${escapeHtml(String(sub).substring(0,60))}</div>` : ''}
            </a>`;
        }).join('')).removeClass('hidden');
    }

    function escapeHtml(str) {
        return $('<div>').text(str).html();
    }
})();
