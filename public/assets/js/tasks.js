$(function () {
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

    $(document).on('click', '.btn-delete-task', function () {
        const id = $(this).data('id');
        if (!confirm('Excluir esta tarefa?')) return;
        api.delete(`/api/tasks/${id}`)
           .done(() => { showToast('Tarefa removida', 'success'); location.reload(); })
           .fail(() => showToast('Erro ao remover', 'error'));
    });
});
