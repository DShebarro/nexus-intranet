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
    
    // Editar site
    $(document).on('click', '.btn-edit-site', function () {
        const id = $(this).data('id');
        const card = $(this).closest('.site-card');
        const name = card.data('name');
        const url = card.data('url');
        const desc = card.data('description');
        const categoryId = card.data('category');
        const isInternal = card.data('type');
        const status = card.data('status');
        
        CategoryManager.loadCategories('site').done(function(categories) {
            let categoryOptions = '<option value="">Sem pasta</option>';
            categories.forEach(function(cat) {
                const selected = (cat.id == categoryId) ? 'selected' : '';
                categoryOptions += `<option value="${cat.id}" ${selected}>${cat.name}</option>`;
            });
            
            openModal(`
                <h3 class="font-bold text-lg text-white mb-4">Editar Site</h3>
                <form id="form-edit-site" class="space-y-4">
                    <input type="text" id="edit-s-name" placeholder="Nome do Site" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                    <input type="url" id="edit-s-url" placeholder="URL do Site" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                    <textarea id="edit-s-desc" placeholder="Descrição" rows="2"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white"></textarea>
                    <select id="edit-s-category"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                        ${categoryOptions}
                    </select>
                    <div class="flex space-x-4">
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="edit-internal" value="1">
                            <span class="text-sm">Interno</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="edit-internal" value="0">
                            <span class="text-sm">Externo</span>
                        </label>
                    </div>
                    <select id="edit-s-status"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                    </select>
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-700 rounded-xl text-sm">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 rounded-xl text-white text-sm">Salvar</button>
                    </div>
                </form>
            `);
            
            // Set values safely
            $('#edit-s-name').val(name);
            $('#edit-s-url').val(url);
            $('#edit-s-desc').val(desc);
            $(`input[name="edit-internal"][value="${isInternal}"]`).prop('checked', true);
            $('#edit-s-status').val(status);
            
            $('#form-edit-site').on('submit', function (e) {
                e.preventDefault();
                api.put('/api/sites/' + id, {
                    name: $('#edit-s-name').val(),
                    url: $('#edit-s-url').val(),
                    description: $('#edit-s-desc').val(),
                    is_internal: parseInt($('input[name="edit-internal"]:checked').val()),
                    status: $('#edit-s-status').val(),
                    category_id: $('#edit-s-category').val() || null
                })
                .done(function () {
                    showToast('Site atualizado!', 'success');
                    closeModal();
                    location.reload();
                })
                .fail(function () {
                    showToast('Erro ao atualizar site', 'error');
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

// Funções de filtro para sites
function filterSites() {
    const searchTerm = $('#search-sites').val().toLowerCase();
    const categoryId = $('#filter-category-site').val();
    const type = $('#filter-type-site').val();
    const status = $('#filter-status-site').val();
    
    $('.site-card').each(function() {
        const $card = $(this);
        const name = $card.data('name').toLowerCase();
        const description = $card.data('description').toLowerCase();
        const cardCategory = $card.data('category');
        const cardType = $card.data('type');
        const cardStatus = $card.data('status');
        
        let show = true;
        
        if (searchTerm && !name.includes(searchTerm) && !description.includes(searchTerm)) {
            show = false;
        }
        
        if (categoryId && cardCategory != categoryId) {
            show = false;
        }
        
        if (type !== '' && cardType != type) {
            show = false;
        }
        
        if (status !== '' && cardStatus !== status) {
            show = false;
        }
        
        show ? $card.show() : $card.hide();
    });
}

// Event listeners para filtros
$('#search-sites').on('keyup', filterSites);
$('#filter-category-site').on('change', function() {
    const categoryId = $(this).val();
    if (categoryId) {
        window.location.href = `/sites?category_id=${categoryId}`;
    } else {
        window.location.href = '/sites';
    }
});
$('#filter-type-site').on('change', filterSites);
$('#filter-status-site').on('change', filterSites);
