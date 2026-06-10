// Funções compartilhadas para gerenciamento de categorias
window.CategoryManager = {
    createCategory: function(type, name) {
        return api.post('/api/categories', {
            name: name,
            type: type
        });
    },
    
    loadCategories: function(type) {
        return api.get('/api/categories?type=' + type);
    },
    
    showCreateCategoryModal: function(type, onSuccess) {
        openModal(`
            <h3 class="font-bold text-lg text-white mb-4">Criar Nova Pasta</h3>
            <form id="form-category" class="space-y-4">
                <div>
                    <label class="block text-sm text-slate-400 mb-2">Nome da Pasta</label>
                    <input type="text" id="cat-name" placeholder="Ex: Desenvolvimento" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-700 rounded-xl text-sm">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 rounded-xl text-white text-sm">Criar Pasta</button>
                </div>
            </form>
        `);
        
        $('#form-category').on('submit', function(e) {
            e.preventDefault();
            const name = $('#cat-name').val().trim();
            if (!name) {
                showToast('Por favor, insira um nome para a pasta', 'error');
                return;
            }
            
            CategoryManager.createCategory(type, name)
                .done(function(res) {
                    showToast('Pasta criada com sucesso!', 'success');
                    closeModal();
                    if (onSuccess) onSuccess(res.category);
                    setTimeout(() => location.reload(), 1000);
                })
                .fail(function() {
                    showToast('Erro ao criar pasta', 'error');
                });
        });
    }
};
