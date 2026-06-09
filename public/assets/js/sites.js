$(function () {
    // Inicializar botão de nova categoria
    $('#btn-new-category-site').on('click', function() {
        const type = $(this).data('type');
        CategoryManager.showCreateCategoryModal(type);
    });
    
    // Criar novo site
    $('#btn-new-site').on('click', function () {
        CategoryManager.loadCategories('site').done(function(categories) {
            let categoryOptions = '<option value="">Sem pasta</option>';
            categories.forEach(function(cat) {
                categoryOptions += `<option value="${cat.id}">${cat.name}</option>`;
            });
            
            openModal(`
                <h3 class="font-bold text-lg text-white mb-4">Novo Site</h3>
                <form id="form-site" class="space-y-4">
                    <input type="text" id="s-name" placeholder="Nome do Site" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                    <input type="url" id="s-url" placeholder="URL do Site" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                    <textarea id="s-desc" placeholder="Descrição" rows="2"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white"></textarea>
                    <select id="s-category"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                        ${categoryOptions}
                    </select>
                    <div class="flex space-x-4">
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="internal" value="1" checked>
                            <span class="text-sm">Interno</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="internal" value="0">
                            <span class="text-sm">Externo</span>
                        </label>
                    </div>
                    <select id="s-status"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                    </select>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-700 rounded-xl text-sm">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 rounded-xl text-white text-sm">Criar</button>
                    </div>
                </form>
            `);
            
            $('#form-site').on('submit', function (e) {
                e.preventDefault();
                api.post('/api/sites', {
                    name: $('#s-name').val(),
                    url: $('#s-url').val(),
                    description: $('#s-desc').val(),
                    is_internal: parseInt($('input[name="internal"]:checked').val()),
                    status: $('#s-status').val(),
                    category_id: $('#s-category').val() || null
                })
                .done(function () {
                    showToast('Site criado!', 'success');
                    closeModal();
                    location.reload();
                })
                .fail(function () {
                    showToast('Erro ao criar site', 'error');
                });
            });
        });
    });
    
    // Deletar site
    $(document).on('click', '.btn-delete-site', function () {
        const id = $(this).data('id');
        if (!confirm('Excluir este site?')) return;
        api.delete(`/api/sites/${id}`)
           .done(() => { showToast('Site removido', 'success'); location.reload(); })
           .fail(() => showToast('Erro ao remover', 'error'));
    });
});
