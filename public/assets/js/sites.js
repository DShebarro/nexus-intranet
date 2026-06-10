$(function () {

    // ---- Nova Categoria ----
    $('#btn-new-category-site').on('click', function() {
        CategoryManager.showCreateCategoryModal('site');
    });

    // ---- Novo Site ----
    $('#btn-new-site').on('click', function() {
        CategoryManager.loadCategories('site').done(function(categories) {
            let catOpts = '<option value="">Sem pasta</option>';
            categories.forEach(c => { catOpts += `<option value="${c.id}">${c.name}</option>`; });

            openModal(`
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);">Novo Site</h3>
                    <button type="button" onclick="closeModal()" style="width:28px;height:28px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);font-size:16px;">✕</button>
                </div>
                <form id="form-site" style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Nome do Site *</label>
                        <input type="text" id="s-name" placeholder="Ex: Portal RH" required class="input-field">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">URL *</label>
                        <input type="url" id="s-url" placeholder="https://..." required class="input-field">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Descrição</label>
                        <textarea id="s-desc" placeholder="Breve descrição do site..." rows="2" class="input-field" style="resize:vertical;"></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Pasta</label>
                            <select id="s-category" class="select-field" style="width:100%;">${catOpts}</select>
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Status</label>
                            <select id="s-status" class="select-field" style="width:100%;">
                                <option value="online">🟢 Online</option>
                                <option value="offline">🔴 Offline</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-size:12px;font-weight:500;color:var(--text-muted);">Tipo de Acesso</label>
                        <div style="display:flex;gap:16px;">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                <input type="radio" name="internal" value="1" checked style="accent-color:var(--indigo);">
                                <span style="font-size:13px;color:var(--text-primary);">💼 Interno</span>
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                <input type="radio" name="internal" value="0" style="accent-color:var(--indigo);">
                                <span style="font-size:13px;color:var(--text-primary);">🌐 Externo</span>
                            </label>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:4px;border-top:1px solid var(--border);margin-top:4px;">
                        <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="plus" style="width:13px;height:13px;"></i>
                            Criar Site
                        </button>
                    </div>
                </form>
            `);

            $('#form-site').on('submit', function(e) {
                e.preventDefault();
                api.post('/api/sites', {
                    name:        $('#s-name').val(),
                    url:         $('#s-url').val(),
                    description: $('#s-desc').val(),
                    is_internal: parseInt($('input[name="internal"]:checked').val()),
                    status:      $('#s-status').val(),
                    category_id: $('#s-category').val() || null
                })
                .done(function() {
                    showToast('Site criado com sucesso!', 'success');
                    closeModal();
                    location.reload();
                })
                .fail(function() { showToast('Erro ao criar site', 'error'); });
            });
        });
    });

    // ---- Editar Site ----
    $(document).on('click', '.btn-edit-site', function() {
        const id         = $(this).data('id');
        const card       = $(this).closest('.site-card');
        const name       = card.data('name');
        const url        = card.data('url');
        const desc       = card.data('description');
        const categoryId = card.data('category');
        const isInternal = card.data('type');
        const status     = card.data('status');

        CategoryManager.loadCategories('site').done(function(categories) {
            let catOpts = '<option value="">Sem pasta</option>';
            categories.forEach(c => {
                catOpts += `<option value="${c.id}" ${c.id == categoryId ? 'selected' : ''}>${c.name}</option>`;
            });

            openModal(`
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);">Editar Site</h3>
                    <button type="button" onclick="closeModal()" style="width:28px;height:28px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);font-size:16px;">✕</button>
                </div>
                <form id="form-edit-site" style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Nome do Site *</label>
                        <input type="text" id="edit-s-name" required class="input-field">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">URL *</label>
                        <input type="url" id="edit-s-url" required class="input-field">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Descrição</label>
                        <textarea id="edit-s-desc" rows="2" class="input-field" style="resize:vertical;"></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Pasta</label>
                            <select id="edit-s-category" class="select-field" style="width:100%;">${catOpts}</select>
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Status</label>
                            <select id="edit-s-status" class="select-field" style="width:100%;">
                                <option value="online">🟢 Online</option>
                                <option value="offline">🔴 Offline</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:8px;font-size:12px;font-weight:500;color:var(--text-muted);">Tipo de Acesso</label>
                        <div style="display:flex;gap:16px;">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                <input type="radio" name="edit-internal" value="1" style="accent-color:var(--indigo);">
                                <span style="font-size:13px;color:var(--text-primary);">💼 Interno</span>
                            </label>
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                <input type="radio" name="edit-internal" value="0" style="accent-color:var(--indigo);">
                                <span style="font-size:13px;color:var(--text-primary);">🌐 Externo</span>
                            </label>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:4px;border-top:1px solid var(--border);margin-top:4px;">
                        <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            `);

            $('#edit-s-name').val(name);
            $('#edit-s-url').val(url);
            $('#edit-s-desc').val(desc);
            $(`input[name="edit-internal"][value="${isInternal}"]`).prop('checked', true);
            $('#edit-s-status').val(status);

            $('#form-edit-site').on('submit', function(e) {
                e.preventDefault();
                api.put('/api/sites/' + id, {
                    name:        $('#edit-s-name').val(),
                    url:         $('#edit-s-url').val(),
                    description: $('#edit-s-desc').val(),
                    is_internal: parseInt($('input[name="edit-internal"]:checked').val()),
                    status:      $('#edit-s-status').val(),
                    category_id: $('#edit-s-category').val() || null
                })
                .done(function() {
                    showToast('Site atualizado!', 'success');
                    closeModal();
                    location.reload();
                })
                .fail(function() { showToast('Erro ao atualizar site', 'error'); });
            });
        });
    });

    // ---- Deletar Site ----
    $(document).on('click', '.btn-delete-site', function() {
        const id = $(this).data('id');
        if (!confirm('Excluir este site? Esta ação não pode ser desfeita.')) return;
        api.delete(`/api/sites/${id}`)
           .done(() => { showToast('Site removido', 'success'); location.reload(); })
           .fail(() => showToast('Erro ao remover site', 'error'));
    });
});

// ---- Filtros ----
function filterSites() {
    const search     = $('#search-sites').val().toLowerCase();
    const categoryId = $('#filter-category-site').val();
    const type       = $('#filter-type-site').val();
    const status     = $('#filter-status-site').val();

    $('.site-card').each(function() {
        const $c    = $(this);
        const name  = String($c.data('name')).toLowerCase();
        const desc  = String($c.data('description')).toLowerCase();
        const cat   = $c.data('category');
        const tp    = String($c.data('type'));
        const st    = $c.data('status');

        let show = true;
        if (search     && !name.includes(search) && !desc.includes(search)) show = false;
        if (categoryId && cat != categoryId) show = false;
        if (type !== '' && tp !== type)      show = false;
        if (status !== '' && st !== status)  show = false;

        show ? $c.show() : $c.hide();
    });
}

$('#search-sites').on('keyup', filterSites);
$('#filter-category-site').on('change', function() {
    const id = $(this).val();
    window.location.href = id ? `/sites?category_id=${id}` : '/sites';
});
$('#filter-type-site').on('change', filterSites);
$('#filter-status-site').on('change', filterSites);
