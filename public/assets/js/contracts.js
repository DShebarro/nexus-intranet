$(function () {
    // Inicializar botão de nova categoria
    $('#btn-new-category-contract').on('click', function() {
        const type = $(this).data('type');
        CategoryManager.showCreateCategoryModal(type);
    });
    
    // Criar novo contrato
    $('#btn-new-contract').on('click', function () {
        CategoryManager.loadCategories('contract').done(function(categories) {
            let categoryOptions = '<option value="">Sem pasta</option>';
            categories.forEach(function(cat) {
                categoryOptions += `<option value="${cat.id}">${cat.name}</option>`;
            });
            
            openModal(`
                <h3 class="font-bold text-lg text-white mb-4">Novo Contrato</h3>
                <form id="form-contract" class="space-y-4">
                    <input type="text" id="c-code" placeholder="Código do Contrato" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                    <input type="text" id="c-partner" placeholder="Fornecedor/Parceiro" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                    <input type="text" id="c-object" placeholder="Objeto do Contrato" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                    <input type="number" id="c-value" placeholder="Valor" step="0.01" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                    <select id="c-category"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                        ${categoryOptions}
                    </select>
                    <select id="c-status"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                        <option value="vigente">Vigente</option>
                        <option value="em_renovacao">Em Renovação</option>
                        <option value="vencido">Vencido</option>
                    </select>
                    <input type="date" id="c-end-date" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-700 rounded-xl text-sm">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 rounded-xl text-white text-sm">Criar</button>
                    </div>
                </form>
            `);
            
            $('#form-contract').on('submit', function (e) {
                e.preventDefault();
                api.post('/api/contracts', {
                    code: $('#c-code').val(),
                    partner: $('#c-partner').val(),
                    object: $('#c-object').val(),
                    value: parseFloat($('#c-value').val()),
                    status: $('#c-status').val(),
                    end_date: $('#c-end-date').val(),
                    category_id: $('#c-category').val() || null
                })
                .done(function () {
                    showToast('Contrato criado!', 'success');
                    closeModal();
                    location.reload();
                })
                .fail(function () {
                    showToast('Erro ao criar contrato', 'error');
                });
            });
        });
    });
    
    // Deletar contrato
    $(document).on('click', '.btn-delete-contract', function () {
        const id = $(this).data('id');
        if (!confirm('Excluir este contrato?')) return;
        api.delete(`/api/contracts/${id}`)
           .done(() => { showToast('Contrato removido', 'success'); location.reload(); })
           .fail(() => showToast('Erro ao remover', 'error'));
    });
});

// Funções de filtro para contratos
function filterContracts() {
    const searchTerm = $('#search-contracts').val().toLowerCase();
    const categoryId = $('#filter-category-contract').val();
    const status = $('#filter-status-contract').val();
    
    let visibleCount = 0;
    
    $('.contract-row').each(function() {
        const $row = $(this);
        const code = $row.data('code').toLowerCase();
        const partner = $row.data('partner').toLowerCase();
        const object = $row.data('object').toLowerCase();
        const rowStatus = $row.data('status');
        const rowCategory = $row.data('category');
        
        let show = true;
        
        if (searchTerm && !code.includes(searchTerm) && !partner.includes(searchTerm) && !object.includes(searchTerm)) {
            show = false;
        }
        
        if (categoryId && rowCategory != categoryId) {
            show = false;
        }
        
        if (status && rowStatus !== status) {
            show = false;
        }
        
        if (show) {
            $row.show();
            visibleCount++;
        } else {
            $row.hide();
        }
    });
    
    $('#active-count').text(visibleCount);
}

// Event listeners para filtros
$('#search-contracts').on('keyup', filterContracts);
$('#filter-category-contract').on('change', function() {
    const categoryId = $(this).val();
    if (categoryId) {
        window.location.href = `/contracts?category_id=${categoryId}`;
    } else {
        window.location.href = '/contracts';
    }
});
$('#filter-status-contract').on('change', filterContracts);
