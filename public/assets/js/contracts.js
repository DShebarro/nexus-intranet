$(function () {

    // ---- Nova Categoria ----
    $('#btn-new-category-contract').on('click', function() {
        CategoryManager.showCreateCategoryModal('contract');
    });

    // ---- Novo Contrato ----
    $('#btn-new-contract').on('click', function() {
        CategoryManager.loadCategories('contract').done(function(categories) {
            let catOpts = '<option value="">Sem pasta</option>';
            categories.forEach(c => { catOpts += `<option value="${c.id}">${c.name}</option>`; });

            openModal(`
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);">Novo Contrato</h3>
                    <button type="button" onclick="closeModal()" style="width:28px;height:28px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);font-size:16px;">✕</button>
                </div>
                <form id="form-contract" style="display:flex;flex-direction:column;gap:14px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Código *</label>
                            <input type="text" id="c-code" placeholder="Ex: CT-2025-001" required class="input-field">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Status</label>
                            <select id="c-status" class="select-field" style="width:100%;">
                                <option value="vigente">✅ Vigente</option>
                                <option value="em_renovacao">🔄 Em Renovação</option>
                                <option value="vencido">❌ Vencido</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Fornecedor / Parceiro *</label>
                        <input type="text" id="c-partner" placeholder="Nome do fornecedor ou parceiro" required class="input-field">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Objeto do Contrato *</label>
                        <input type="text" id="c-object" placeholder="Descrição do objeto contratual" required class="input-field">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Valor (R$) *</label>
                            <input type="number" id="c-value" placeholder="0,00" step="0.01" required class="input-field">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Pasta</label>
                            <select id="c-category" class="select-field" style="width:100%;">${catOpts}</select>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Data de Vencimento *</label>
                        <input type="date" id="c-end-date" required class="input-field">
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:4px;border-top:1px solid var(--border);margin-top:4px;">
                        <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="plus" style="width:13px;height:13px;"></i>
                            Criar Contrato
                        </button>
                    </div>
                </form>
            `);

            $('#form-contract').on('submit', function(e) {
                e.preventDefault();
                api.post('/api/contracts', {
                    code:        $('#c-code').val(),
                    partner:     $('#c-partner').val(),
                    object:      $('#c-object').val(),
                    value:       parseFloat($('#c-value').val()),
                    status:      $('#c-status').val(),
                    end_date:    $('#c-end-date').val(),
                    category_id: $('#c-category').val() || null
                })
                .done(function() {
                    showToast('Contrato criado com sucesso!', 'success');
                    closeModal();
                    location.reload();
                })
                .fail(function() { showToast('Erro ao criar contrato', 'error'); });
            });
        });
    });

    // ---- Editar Contrato ----
    $(document).on('click', '.btn-edit-contract', function() {
        const id         = $(this).data('id');
        const row        = $(this).closest('.contract-row');
        const code       = row.data('code');
        const partner    = row.data('partner');
        const object     = row.data('object');
        const value      = row.data('value');
        const categoryId = row.data('category');
        const status     = row.data('status');
        const endDate    = row.data('end-date');

        CategoryManager.loadCategories('contract').done(function(categories) {
            let catOpts = '<option value="">Sem pasta</option>';
            categories.forEach(c => {
                catOpts += `<option value="${c.id}" ${c.id == categoryId ? 'selected' : ''}>${c.name}</option>`;
            });

            openModal(`
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);">Editar Contrato</h3>
                    <button type="button" onclick="closeModal()" style="width:28px;height:28px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);font-size:16px;">✕</button>
                </div>
                <form id="form-edit-contract" style="display:flex;flex-direction:column;gap:14px;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Código *</label>
                            <input type="text" id="edit-c-code" required class="input-field">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Status</label>
                            <select id="edit-c-status" class="select-field" style="width:100%;">
                                <option value="vigente">✅ Vigente</option>
                                <option value="em_renovacao">🔄 Em Renovação</option>
                                <option value="vencido">❌ Vencido</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Fornecedor / Parceiro *</label>
                        <input type="text" id="edit-c-partner" required class="input-field">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Objeto do Contrato *</label>
                        <input type="text" id="edit-c-object" required class="input-field">
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Valor (R$) *</label>
                            <input type="number" id="edit-c-value" step="0.01" required class="input-field">
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Pasta</label>
                            <select id="edit-c-category" class="select-field" style="width:100%;">${catOpts}</select>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Data de Vencimento *</label>
                        <input type="date" id="edit-c-end-date" required class="input-field">
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:4px;border-top:1px solid var(--border);margin-top:4px;">
                        <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            `);

            $('#edit-c-code').val(code);
            $('#edit-c-partner').val(partner);
            $('#edit-c-object').val(object);
            $('#edit-c-value').val(value);
            $('#edit-c-status').val(status);
            $('#edit-c-end-date').val(endDate);

            $('#form-edit-contract').on('submit', function(e) {
                e.preventDefault();
                api.put('/api/contracts/' + id, {
                    code:        $('#edit-c-code').val(),
                    partner:     $('#edit-c-partner').val(),
                    object:      $('#edit-c-object').val(),
                    value:       parseFloat($('#edit-c-value').val()),
                    status:      $('#edit-c-status').val(),
                    end_date:    $('#edit-c-end-date').val(),
                    category_id: $('#edit-c-category').val() || null
                })
                .done(function() {
                    showToast('Contrato atualizado!', 'success');
                    closeModal();
                    location.reload();
                })
                .fail(function() { showToast('Erro ao atualizar contrato', 'error'); });
            });
        });
    });

    // ---- Deletar Contrato ----
    $(document).on('click', '.btn-delete-contract', function() {
        const id = $(this).data('id');
        if (!confirm('Excluir este contrato? Esta ação não pode ser desfeita.')) return;
        api.delete(`/api/contracts/${id}`)
           .done(() => { showToast('Contrato removido', 'success'); location.reload(); })
           .fail(() => showToast('Erro ao remover contrato', 'error'));
    });
});

// ---- Filtros ----
function filterContracts() {
    const search     = $('#search-contracts').val().toLowerCase();
    const categoryId = $('#filter-category-contract').val();
    const status     = $('#filter-status-contract').val();
    let count = 0;

    $('.contract-row').each(function() {
        const $r     = $(this);
        const code   = String($r.data('code')).toLowerCase();
        const partner = String($r.data('partner')).toLowerCase();
        const object = String($r.data('object')).toLowerCase();
        const rowSt  = $r.data('status');
        const rowCat = $r.data('category');

        let show = true;
        if (search     && !code.includes(search) && !partner.includes(search) && !object.includes(search)) show = false;
        if (categoryId && rowCat != categoryId) show = false;
        if (status     && rowSt !== status)     show = false;

        show ? $r.show() : $r.hide();
        if (show) count++;
    });

    $('#active-count').text(count);
}

$('#search-contracts').on('keyup', filterContracts);
$('#filter-category-contract').on('change', function() {
    const id = $(this).val();
    window.location.href = id ? `/contracts?category_id=${id}` : '/contracts';
});
$('#filter-status-contract').on('change', filterContracts);
