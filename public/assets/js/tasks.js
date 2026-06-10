$(function () {

    // ---- Colapsar/expandir seções ----
    $(document).on('click', '.task-section-toggle', function () {
        const targetId = $(this).data('target');
        const $body    = $('#' + targetId);
        const $chevron = $(this).find('.section-chevron');
        const isHidden = $body.is(':hidden');

        $body.slideToggle(180);
        $chevron.css('transform', isHidden ? 'rotate(0deg)' : 'rotate(-90deg)');
    });

    // ---- Checkbox: marcar/desmarcar como concluído ----
    $(document).on('click', '.btn-toggle-done', function (e) {
        e.stopPropagation();
        const $btn    = $(this);
        const id      = $btn.data('id');
        const current = $btn.data('status');
        const newStatus = current === 'done' ? 'todo' : 'done';

        $btn.prop('disabled', true);
        api.patch(`/api/tasks/${id}/move`, { status: newStatus })
            .done(function () {
                showToast(newStatus === 'done' ? '✓ Tarefa concluída!' : 'Tarefa reaberta', 'success');
                location.reload();
            })
            .fail(function () {
                showToast('Erro ao atualizar tarefa', 'error');
                $btn.prop('disabled', false);
            });
    });

    // ---- Select de mover status ----
    $(document).on('change', '.btn-move-status', function () {
        const id        = $(this).data('id');
        const newStatus = $(this).val();
        api.patch(`/api/tasks/${id}/move`, { status: newStatus })
            .done(function () {
                const labels = { todo: 'A Fazer', progress: 'Em Andamento', review: 'Em Revisão', done: 'Concluído' };
                showToast(`Movida para "${labels[newStatus]}"`, 'success');
                location.reload();
            })
            .fail(function () {
                showToast('Erro ao mover tarefa', 'error');
            });
    });

    // ---- Nova Categoria ----
    $('#btn-new-category-task').on('click', function () {
        CategoryManager.showCreateCategoryModal('task');
    });

    // ---- Nova Tarefa ----
    $('#btn-new-task').on('click', function () {
        openTaskModal();
    });

    // ---- Editar Tarefa ----
    $(document).on('click', '.btn-edit-task', function (e) {
        e.stopPropagation();
        const id     = $(this).data('id');
        const $row   = $(this).closest('.task-row');
        openTaskModal({
            id,
            title:    $row.data('title'),
            desc:     $row.data('description'),
            priority: $row.data('priority'),
            category: $row.data('category'),
            status:   $row.data('status'),
            date:     $row.data('date'),
        });
    });

    // ---- Deletar Tarefa ----
    $(document).on('click', '.btn-delete-task', function (e) {
        e.stopPropagation();
        const id = $(this).data('id');
        if (!confirm('Excluir esta tarefa? Esta ação não pode ser desfeita.')) return;
        api.delete(`/api/tasks/${id}`)
            .done(function () { showToast('Tarefa removida', 'success'); location.reload(); })
            .fail(function () { showToast('Erro ao remover tarefa', 'error'); });
    });

    // -------- Modal de Criar/Editar --------
    function openTaskModal(edit = null) {
        CategoryManager.loadCategories('task').done(function (categories) {
            let catOpts = '<option value="">Sem pasta</option>';
            categories.forEach(c => {
                catOpts += `<option value="${c.id}" ${edit && c.id == edit.category ? 'selected' : ''}>${c.name}</option>`;
            });

            const statusOpts = `
                <option value="todo"     ${edit && edit.status==='todo'     ? 'selected':''}>📋 A Fazer</option>
                <option value="progress" ${edit && edit.status==='progress' ? 'selected':''}>⚡ Em Andamento</option>
                <option value="review"   ${edit && edit.status==='review'   ? 'selected':''}>🔍 Em Revisão</option>
                <option value="done"     ${edit && edit.status==='done'     ? 'selected':''}>✅ Concluído</option>
            `;

            const isEdit  = !!edit;
            const title   = isEdit ? 'Editar Tarefa' : 'Nova Tarefa';
            const btnLabel= isEdit ? 'Salvar Alterações' : 'Criar Tarefa';

            openModal(`
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <div>
                        <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);">${title}</h3>
                        ${isEdit ? `<p style="font-size:11px;color:var(--text-faint);margin-top:2px;">ID #${edit.id}</p>` : ''}
                    </div>
                    <button type="button" onclick="closeModal()" style="width:28px;height:28px;background:var(--bg-elevated);border:1px solid var(--border);border-radius:8px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text-muted);font-size:16px;line-height:1;">✕</button>
                </div>

                <form id="form-task" style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Título da Tarefa *</label>
                        <input type="text" id="t-title" placeholder="Ex: Revisar relatório mensal" required class="input-field">
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Descrição</label>
                        <textarea id="t-desc" placeholder="Detalhes da tarefa..." rows="3" class="input-field" style="resize:vertical;"></textarea>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Prioridade</label>
                            <select id="t-priority" class="select-field" style="width:100%;">
                                <option value="baixa" ${isEdit && edit.priority==='baixa' ? 'selected':''}>🟢 Baixa</option>
                                <option value="media" ${!isEdit || edit.priority==='media' ? 'selected':''}>🟡 Média</option>
                                <option value="alta"  ${isEdit && edit.priority==='alta'  ? 'selected':''}>🔴 Alta</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Status</label>
                            <select id="t-status" class="select-field" style="width:100%;">${statusOpts}</select>
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Pasta</label>
                            <select id="t-category" class="select-field" style="width:100%;">${catOpts}</select>
                        </div>
                        <div>
                            <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:500;color:var(--text-muted);">Data de Vencimento</label>
                            <input type="date" id="t-date" class="input-field">
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:8px;border-top:1px solid var(--border);">
                        <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="task-submit-btn">
                            <i data-lucide="${isEdit ? 'save' : 'plus'}" style="width:13px;height:13px;"></i>
                            ${btnLabel}
                        </button>
                    </div>
                </form>
            `);

            // Pre-fill if editing
            if (isEdit) {
                $('#t-title').val(edit.title);
                $('#t-desc').val(edit.desc);
                if (edit.date) $('#t-date').val(edit.date);
            }

            // Form submit
            $('#form-task').on('submit', function (e) {
                e.preventDefault();
                const $btn = $('#task-submit-btn');
                $btn.prop('disabled', true).text('Salvando...');

                const payload = {
                    title:       $('#t-title').val().trim(),
                    description: $('#t-desc').val().trim(),
                    priority:    $('#t-priority').val(),
                    status:      $('#t-status').val(),
                    due_date:    $('#t-date').val() || null,
                    category_id: $('#t-category').val() || null,
                };

                const req = isEdit
                    ? api.put(`/api/tasks/${edit.id}`, payload)
                    : api.post('/api/tasks', payload);

                req.done(function () {
                    showToast(isEdit ? 'Tarefa atualizada!' : 'Tarefa criada com sucesso!', 'success');
                    closeModal();
                    location.reload();
                }).fail(function () {
                    showToast('Erro ao salvar tarefa', 'error');
                    $btn.prop('disabled', false).text(isEdit ? 'Salvar Alterações' : 'Criar Tarefa');
                });
            });
        });
    }
});

// ---- Filtros ----
function filterTasks() {
    const search     = $('#search-tasks').val().toLowerCase();
    const categoryId = $('#filter-category-task').val();
    const priority   = $('#filter-priority-task').val();

    let visibleBySection = {};

    $('.task-row').each(function () {
        const $r    = $(this);
        const title = String($r.data('title')).toLowerCase();
        const desc  = String($r.data('description')).toLowerCase();
        const cat   = String($r.data('category'));
        const prio  = $r.data('priority');
        const sec   = $r.data('status');

        let show = true;
        if (search     && !title.includes(search) && !desc.includes(search)) show = false;
        if (categoryId && cat !== categoryId)                                  show = false;
        if (priority   && prio !== priority)                                   show = false;

        $r.toggle(show);
        if (!visibleBySection[sec]) visibleBySection[sec] = 0;
        if (show) visibleBySection[sec]++;
    });

    // Atualizar contagens das seções
    $('.task-section').each(function () {
        const sec   = $(this).data('section');
        const count = visibleBySection[sec] || 0;
        $(this).find('.task-section-toggle span:nth-child(3)').text(count);
    });
}

$('#search-tasks').on('keyup', filterTasks);
$('#filter-category-task').on('change', function () {
    const id = $(this).val();
    window.location.href = id ? `/tasks?category_id=${id}` : '/tasks';
});
$('#filter-priority-task').on('change', filterTasks);
