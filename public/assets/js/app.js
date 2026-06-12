// ============================================================
// NEXUS INTRANET — Core JS
// ============================================================

// ---- Toast Notification ----
window.showToast = function(msg, type = 'info') {
    const toast = $('#toast');
    const icons = { success: '✓', error: '✕', info: 'ℹ' };
    const colors = {
        success: 'linear-gradient(135deg,#059669,#10b981)',
        error:   'linear-gradient(135deg,#e11d48,#f43f5e)',
        info:    'linear-gradient(135deg,#2563eb,#3b82f6)',
    };

    $('#toast-message').text(msg);
    toast.css('background', colors[type] || colors.info);
    toast.removeClass('hidden').addClass('flex');

    clearTimeout(window._toastTimer);
    window._toastTimer = setTimeout(() => {
        toast.addClass('hidden').removeClass('flex');
    }, 4000);
};

// ---- REST API Helpers ----
window.api = {
    _headers: function() {
        const csrf = document.querySelector('meta[name="csrf-token"]');
        return csrf ? { 'X-CSRF-Token': csrf.content } : {};
    },
    get:    (url)       => $.getJSON(url),
    post:   (url, data) => $.ajax({ url, method: 'POST',   contentType: 'application/json', headers: api._headers(), data: JSON.stringify(data) }),
    put:    (url, data) => $.ajax({ url, method: 'PUT',    contentType: 'application/json', headers: api._headers(), data: JSON.stringify(data) }),
    patch:  (url, data) => $.ajax({ url, method: 'PATCH',  contentType: 'application/json', headers: api._headers(), data: JSON.stringify(data) }),
    delete: (url)       => $.ajax({ url, method: 'DELETE', headers: api._headers() }),
};

// ---- Modal ----
window.openModal = function(html) {
    $('#modal-body').html(html);
    $('#modal-container').removeClass('hidden').addClass('flex');
    if (typeof lucide !== 'undefined') lucide.createIcons();
};

window.closeModal = function() {
    $('#modal-container').addClass('hidden').removeClass('flex');
};

// Close modal on backdrop click
$(document).on('click', '#modal-container', function(e) {
    if (e.target === this) closeModal();
});

// Close modal on Escape
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

// ---- Shared Modal Styles injector ----
// Ensures modal content uses design system styles
window.modalFormHtml = function(title, fields, submitLabel = 'Salvar') {
    const fieldRows = fields.map(f => {
        if (f.type === 'select') {
            const opts = f.options.map(o => `<option value="${o.value}">${o.label}</option>`).join('');
            return `<div>
                <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">${f.label}</label>
                <select id="${f.id}" style="width:100%;background:var(--bg-elevated);border:1px solid var(--border-strong);border-radius:10px;padding:10px 14px;color:var(--text-primary);font-size:13px;font-family:inherit;outline:none;appearance:none;cursor:pointer;">${opts}</select>
            </div>`;
        }
        if (f.type === 'textarea') {
            return `<div>
                <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">${f.label}</label>
                <textarea id="${f.id}" placeholder="${f.placeholder || ''}" rows="${f.rows || 3}" style="width:100%;background:var(--bg-elevated);border:1px solid var(--border-strong);border-radius:10px;padding:10px 14px;color:var(--text-primary);font-size:13px;font-family:inherit;outline:none;resize:vertical;"></textarea>
            </div>`;
        }
        return `<div>
            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">${f.label}</label>
            <input type="${f.type || 'text'}" id="${f.id}" placeholder="${f.placeholder || ''}" ${f.required ? 'required' : ''} step="${f.step || ''}"
                   style="width:100%;background:var(--bg-elevated);border:1px solid var(--border-strong);border-radius:10px;padding:10px 14px;color:var(--text-primary);font-size:13px;font-family:inherit;outline:none;">
        </div>`;
    }).join('');

    return `
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
            <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);">${title}</h3>
            <button type="button" onclick="closeModal()" style="width:28px;height:28px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);">✕</button>
        </div>
        <form id="modal-form" class="space-y-4">
            ${fieldRows}
            <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:8px;border-top:1px solid var(--border);margin-top:8px;">
                <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">${submitLabel}</button>
            </div>
        </form>`;
};
