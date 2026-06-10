// ============================================================
// CategoryManager — Gerenciamento de Pastas/Categorias
// ============================================================

window.CategoryManager = {

    createCategory: function(type, name) {
        return api.post('/api/categories', { name, type });
    },

    loadCategories: function(type) {
        return api.get('/api/categories?type=' + type);
    },

    showCreateCategoryModal: function(type, onSuccess) {
        const typeLabels = { task: 'Tarefas', contract: 'Contratos', site: 'Sites' };
        const label = typeLabels[type] || type;

        openModal(`
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                <div>
                    <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);">Nova Pasta</h3>
                    <p style="font-size:12px;color:var(--text-faint);margin-top:3px;">Organizar ${label}</p>
                </div>
                <button type="button" onclick="closeModal()" style="width:28px;height:28px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);font-size:16px;">✕</button>
            </div>
            <form id="form-category" style="display:flex;flex-direction:column;gap:14px;">
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Nome da Pasta *</label>
                    <input type="text" id="cat-name" placeholder="Ex: Desenvolvimento, Parcerias..." required
                           class="input-field" autocomplete="off">
                </div>
                <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:4px;border-top:1px solid var(--border);margin-top:4px;">
                    <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="folder-plus" style="width:13px;height:13px;"></i>
                        Criar Pasta
                    </button>
                </div>
            </form>
        `);

        setTimeout(() => $('#cat-name').focus(), 100);

        $('#form-category').on('submit', function(e) {
            e.preventDefault();
            const name = $('#cat-name').val().trim();
            if (!name) {
                showToast('Por favor, insira um nome para a pasta', 'error');
                return;
            }

            const $btn = $(this).find('[type=submit]');
            $btn.prop('disabled', true).text('Criando...');

            CategoryManager.createCategory(type, name)
                .done(function(res) {
                    showToast(`Pasta "${name}" criada com sucesso!`, 'success');
                    closeModal();
                    if (onSuccess) onSuccess(res.category);
                    setTimeout(() => location.reload(), 800);
                })
                .fail(function() {
                    showToast('Erro ao criar pasta', 'error');
                    $btn.prop('disabled', false).html('<i data-lucide="folder-plus" style="width:13px;height:13px;"></i> Criar Pasta');
                    lucide.createIcons();
                });
        });
    }
};
