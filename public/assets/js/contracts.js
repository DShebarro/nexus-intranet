$(function () {
    // Novo Contrato
    $('#btn-new-contract').on('click', function () {
        openModal(`
            <h3 class="font-bold text-lg text-white mb-4">Novo Contrato</h3>
            <form id="form-contract" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Código</label>
                        <input type="text" id="c-code" placeholder="Ex: CT-2026-001" required
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Parceiro</label>
                        <input type="text" id="c-partner" placeholder="Ex: Claro S.A." required
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Objeto do Contrato</label>
                    <input type="text" id="c-object" placeholder="Descrição do objeto" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Valor (R$)</label>
                        <input type="number" step="0.01" id="c-value" placeholder="Ex: 1500.00" required
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Vencimento</label>
                        <input type="date" id="c-date" required
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Status</label>
                    <select id="c-status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                        <option value="vigente" selected>Vigente</option>
                        <option value="em_renovacao">Em Renovação</option>
                        <option value="vencido">Vencido</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-700 rounded-xl text-sm">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 rounded-xl text-white text-sm">Salvar</button>
                </div>
            </form>
        `);

        $('#form-contract').on('submit', function (e) {
            e.preventDefault();
            api.post('/api/contracts', {
                code: $('#c-code').val(),
                partner: $('#c-partner').val(),
                object: $('#c-object').val(),
                value: $('#c-value').val(),
                end_date: $('#c-date').val(),
                status: $('#c-status').val(),
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

    // Editar Contrato
    $(document).on('click', '.btn-edit-contract', function () {
        const row = $(this).closest('.contract-row');
        const id = row.data('id');
        const code = row.data('code');
        const partner = row.data('partner');
        const object = row.data('object');
        const value = row.data('value');
        const date = row.data('date');
        const status = row.data('status');

        openModal(`
            <h3 class="font-bold text-lg text-white mb-4">Editar Contrato</h3>
            <form id="form-edit-contract" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Código</label>
                        <input type="text" id="edit-c-code" value="${escapeHtml(code)}" placeholder="Ex: CT-2026-001" required
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Parceiro</label>
                        <input type="text" id="edit-c-partner" value="${escapeHtml(partner)}" placeholder="Ex: Claro S.A." required
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Objeto do Contrato</label>
                    <input type="text" id="edit-c-object" value="${escapeHtml(object)}" placeholder="Descrição do objeto" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Valor (R$)</label>
                        <input type="number" step="0.01" id="edit-c-value" value="${value}" placeholder="Ex: 1500.00" required
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Vencimento</label>
                        <input type="date" id="edit-c-date" value="${date}" required
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Status</label>
                    <select id="edit-c-status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                        <option value="vigente" ${status === 'vigente' ? 'selected' : ''}>Vigente</option>
                        <option value="em_renovacao" ${status === 'em_renovacao' ? 'selected' : ''}>Em Renovação</option>
                        <option value="vencido" ${status === 'vencido' ? 'selected' : ''}>Vencido</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-700 rounded-xl text-sm">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 rounded-xl text-white text-sm">Salvar</button>
                </div>
            </form>
        `);

        $('#form-edit-contract').on('submit', function (e) {
            e.preventDefault();
            api.put(`/api/contracts/${id}`, {
                code: $('#edit-c-code').val(),
                partner: $('#edit-c-partner').val(),
                object: $('#edit-c-object').val(),
                value: $('#edit-c-value').val(),
                end_date: $('#edit-c-date').val(),
                status: $('#edit-c-status').val(),
            })
            .done(function () {
                showToast('Contrato atualizado!', 'success');
                closeModal();
                location.reload();
            })
            .fail(function () {
                showToast('Erro ao atualizar contrato', 'error');
            });
        });
    });

    // Excluir Contrato
    $(document).on('click', '.btn-delete-contract', function () {
        const row = $(this).closest('.contract-row');
        const id = row.data('id');
        const code = row.data('code');

        if (!confirm(`Excluir o contrato ${code}?`)) return;

        api.delete(`/api/contracts/${id}`)
            .done(function () {
                showToast('Contrato excluído!', 'success');
                location.reload();
            })
            .fail(function () {
                showToast('Erro ao excluir contrato', 'error');
            });
    });

    function escapeHtml(text) {
        return text ? String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;") : '';
    }
});
