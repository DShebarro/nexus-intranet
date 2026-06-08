$(function () {
    // Nova Tarefa
    $('#btn-new-task').on('click', function () {
        openModal(`
            <h3 class="font-bold text-lg text-white mb-4">Nova Tarefa</h3>
            <form id="form-task" class="space-y-4">
                <input type="text" id="t-title" placeholder="Título" required
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                <textarea id="t-desc" placeholder="Descrição" rows="3"
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white"></textarea>
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

    // Excluir Tarefa
    $(document).on('click', '.btn-delete-task', function (e) {
        e.stopPropagation();
        const id = $(this).data('id');
        if (!confirm('Excluir esta tarefa?')) return;
        api.delete(`/api/tasks/${id}`)
           .done(() => { showToast('Tarefa removida', 'success'); location.reload(); })
           .fail(() => showToast('Erro ao remover', 'error'));
    });

    // Editar Tarefa (ao clicar no card)
    $(document).on('click', '.task-card', function (e) {
        if ($(e.target).closest('.btn-delete-task').length) return;

        const id = $(this).data('id');
        const title = $(this).data('title');
        const desc = $(this).data('desc');
        const priority = $(this).data('priority');
        const date = $(this).data('date');

        openModal(`
            <h3 class="font-bold text-lg text-white mb-4">Editar Tarefa</h3>
            <form id="form-edit-task" class="space-y-4">
                <input type="text" id="edit-t-title" value="${escapeHtml(title)}" placeholder="Título" required
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                <textarea id="edit-t-desc" placeholder="Descrição" rows="3"
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">${escapeHtml(desc)}</textarea>
                <select id="edit-t-priority"
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                    <option value="baixa" ${priority === 'baixa' ? 'selected' : ''}>Baixa</option>
                    <option value="media" ${priority === 'media' ? 'selected' : ''}>Média</option>
                    <option value="alta" ${priority === 'alta' ? 'selected' : ''}>Alta</option>
                </select>
                <input type="date" id="edit-t-date" value="${date}" required
                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white">
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-700 rounded-xl text-sm">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 rounded-xl text-white text-sm">Salvar</button>
                </div>
            </form>
        `);

        $('#form-edit-task').on('submit', function (evt) {
            evt.preventDefault();
            api.put(`/api/tasks/${id}`, {
                title: $('#edit-t-title').val(),
                description: $('#edit-t-desc').val(),
                priority: $('#edit-t-priority').val(),
                due_date: $('#edit-t-date').val(),
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

    // Drag and Drop
    let draggedCard = null;

    $(document).on('dragstart', '.task-card', function (e) {
        draggedCard = this;
        $(this).addClass('opacity-50');
        e.originalEvent.dataTransfer.setData('text/plain', $(this).data('id'));
    });

    $(document).on('dragend', '.task-card', function () {
        $(this).removeClass('opacity-50');
        draggedCard = null;
    });

    $('.kanban-col').on('dragover', function (e) {
        e.preventDefault();
        $(this).addClass('bg-slate-900/20');
    });

    $('.kanban-col').on('dragleave', function () {
        $(this).removeClass('bg-slate-900/20');
    });

    $('.kanban-col').on('drop', function (e) {
        e.preventDefault();
        $(this).removeClass('bg-slate-900/20');
        
        if (draggedCard) {
            const taskId = $(draggedCard).data('id');
            const targetStatus = $(this).data('status');
            const sourceStatus = $(draggedCard).parent().data('status');

            if (targetStatus !== sourceStatus) {
                api.patch(`/api/tasks/${taskId}/move`, { status: targetStatus })
                   .done(function () {
                       showToast('Tarefa movida!', 'success');
                       location.reload();
                   })
                   .fail(function () {
                       showToast('Erro ao mover tarefa', 'error');
                   });
            }
        }
    });

    // Filtros client-side
    function applyFilters() {
        const searchVal = $('#search-task').val().toLowerCase();
        const priorityVal = $('#filter-priority').val();

        $('.task-card').each(function () {
            const title = String($(this).data('title')).toLowerCase();
            const desc = String($(this).data('desc')).toLowerCase();
            const priority = $(this).data('priority');

            const matchesSearch = title.includes(searchVal) || desc.includes(searchVal);
            const matchesPriority = (priorityVal === 'all' || priority === priorityVal);

            if (matchesSearch && matchesPriority) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        });
    }

    $('#search-task').on('keyup', applyFilters);
    $('#filter-priority').on('change', applyFilters);

    function escapeHtml(text) {
        return text ? String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;") : '';
    }
});
