$(function () {
    // Inicializar botão de nova categoria
    $('#btn-new-category-task').on('click', function() {
        const type = $(this).data('type');
        CategoryManager.showCreateCategoryModal(type);
    });
    
    // Criar nova tarefa
    $('#btn-new-task').on('click', function () {
        // Carregar categorias para o dropdown
        CategoryManager.loadCategories('task').done(function(categories) {
            let categoryOptions = '<option value="">Sem pasta</option>';
            categories.forEach(function(cat) {
                categoryOptions += `<option value="${cat.id}">${cat.name}</option>`;
            });
            
            openModal(`
                <h3 class="font-bold text-lg text-white mb-4">Nova Tarefa</h3>
                <form id="form-task" class="space-y-4">
                    <input type="text" id="t-title" placeholder="Título" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                    <textarea id="t-desc" placeholder="Descrição" rows="3"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white"></textarea>
                    <select id="t-category"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                        ${categoryOptions}
                    </select>
                    <select id="t-priority"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                        <option value="baixa">Baixa</option>
                        <option value="media" selected>Média</option>
                        <option value="alta">Alta</option>
                    </select>
                    <input type="date" id="t-date" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-700 rounded-xl text-sm">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 rounded-xl text-white text-sm">Criar</button>
                    </div>
                </form>
            `);
            
            $('#form-task').on('submit', function (e) {
                e.preventDefault();
                api.post('/api/tasks', {
                    title: $('#t-title').val(),
                    description: $('#t-desc').val(),
                    priority: $('#t-priority').val(),
                    due_date: $('#t-date').val(),
                    category_id: $('#t-category').val() || null
                })
                .done(function (res) {
                    showToast('Tarefa criada!', 'success');
                    closeModal();
                    location.reload();
                })
                .fail(function () {
                    showToast('Erro ao criar tarefa', 'error');
                });
            });
        });
    });
    
    // Drag and Drop para mover tarefas
    let draggedItem = null;
    
    $(document).on('dragstart', '.task-card', function(e) {
        draggedItem = this;
        e.originalEvent.dataTransfer.setData('text/plain', $(this).data('id'));
        $(this).addClass('opacity-50');
    });
    
    $(document).on('dragend', '.task-card', function() {
        draggedItem = null;
        $(this).removeClass('opacity-50');
    });
    
    $(document).on('dragover', '.kanban-col', function(e) {
        e.preventDefault();
        $(this).addClass('bg-slate-800/30');
    });
    
    $(document).on('dragleave', '.kanban-col', function() {
        $(this).removeClass('bg-slate-800/30');
    });
    
    $(document).on('drop', '.kanban-col', function(e) {
        e.preventDefault();
        $(this).removeClass('bg-slate-800/30');
        
        const taskId = $(this).closest('.task-card').data('id') || e.originalEvent.dataTransfer.getData('text/plain');
        const newStatus = $(this).data('status');
        
        if (taskId && newStatus) {
            api.patch(`/api/tasks/${taskId}/move`, { status: newStatus })
                .done(() => location.reload())
                .fail(() => showToast('Erro ao mover tarefa', 'error'));
        }
    });
    // Editar tarefa
    $(document).on('click', '.btn-edit-task', function () {
        const id = $(this).data('id');
        const card = $(this).closest('.task-card');
        const title = card.data('title');
        const desc = card.data('description');
        const priority = card.data('priority');
        const categoryId = card.data('category');
        const status = card.closest('.kanban-col').data('status');
        const rawDate = card.data('date') || '';
        
        CategoryManager.loadCategories('task').done(function(categories) {
            let categoryOptions = '<option value="">Sem pasta</option>';
            categories.forEach(function(cat) {
                const selected = (cat.id == categoryId) ? 'selected' : '';
                categoryOptions += `<option value="${cat.id}" ${selected}>${cat.name}</option>`;
            });
            
            openModal(`
                <h3 class="font-bold text-lg text-white mb-4">Editar Tarefa</h3>
                <form id="form-edit-task" class="space-y-4">
                    <input type="text" id="edit-t-title" placeholder="Título" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                    <textarea id="edit-t-desc" placeholder="Descrição" rows="3"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white"></textarea>
                    <select id="edit-t-category"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                        ${categoryOptions}
                    </select>
                    <select id="edit-t-priority"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                        <option value="baixa">Baixa</option>
                        <option value="media">Média</option>
                        <option value="alta">Alta</option>
                    </select>
                    <input type="date" id="edit-t-date" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                    <div class="flex justify-end space-x-2 pt-2">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-700 rounded-xl text-sm">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 rounded-xl text-white text-sm">Salvar</button>
                    </div>
                </form>
            `);
            
            // Set values safely
            $('#edit-t-title').val(title);
            $('#edit-t-desc').val(desc);
            $('#edit-t-priority').val(priority);
            $('#edit-t-date').val(rawDate);
            
            $('#form-edit-task').on('submit', function (e) {
                e.preventDefault();
                api.put('/api/tasks/' + id, {
                    title: $('#edit-t-title').val(),
                    description: $('#edit-t-desc').val(),
                    priority: $('#edit-t-priority').val(),
                    due_date: $('#edit-t-date').val(),
                    category_id: $('#edit-t-category').val() || null,
                    status: status
                })
                .done(function () {
                    showToast('Tarefa atualizada!', 'success');
                    closeModal();
                    location.reload();
                })
                .fail(function () {
                    showToast('Erro ao atualizar tarefa', 'error');
                });
            });
        });
    });
    
    // Deletar tarefa
    $(document).on('click', '.btn-delete-task', function () {
        const id = $(this).data('id');
        if (!confirm('Excluir esta tarefa?')) return;
        api.delete(`/api/tasks/${id}`)
           .done(() => { showToast('Tarefa removida', 'success'); location.reload(); })
           .fail(() => showToast('Erro ao remover', 'error'));
    });
});

// Funções de filtro para tarefas
function filterTasks() {
    const searchTerm = $('#search-tasks').val().toLowerCase();
    const categoryId = $('#filter-category-task').val();
    const priority = $('#filter-priority-task').val();
    
    $('.task-card').each(function() {
        const $card = $(this);
        const title = $card.data('title').toLowerCase();
        const description = $card.data('description').toLowerCase();
        const cardCategory = $card.data('category');
        const cardPriority = $card.data('priority');
        
        let show = true;
        
        if (searchTerm && !title.includes(searchTerm) && !description.includes(searchTerm)) {
            show = false;
        }
        
        if (categoryId && cardCategory != categoryId) {
            show = false;
        }
        
        if (priority && cardPriority !== priority) {
            show = false;
        }
        
        show ? $card.show() : $card.hide();
    });
    
    // Atualizar contagens das colunas
    $('.kanban-col').each(function() {
        const visibleCount = $(this).find('.task-card:visible').length;
        $(this).closest('.bg-slate-950\\/30').find('.task-count').text(visibleCount);
    });
}

// Event listeners para filtros
$('#search-tasks').on('keyup', filterTasks);
$('#filter-category-task').on('change', function() {
    const categoryId = $(this).val();
    if (categoryId) {
        window.location.href = `/tasks?category_id=${categoryId}`;
    } else {
        window.location.href = '/tasks';
    }
});
$('#filter-priority-task').on('change', filterTasks);
