$(function () {
    // Novo Site
    $('#btn-new-site').on('click', function () {
        openModal(`
            <h3 class="font-bold text-lg text-white mb-4">Novo Link / Sistema</h3>
            <form id="form-site" class="space-y-4">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Nome do Sistema</label>
                    <input type="text" id="s-name" placeholder="Ex: Zabbix Monitoramento" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">URL de Acesso</label>
                    <input type="url" id="s-url" placeholder="Ex: https://zabbix.nexus.com" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Descrição</label>
                    <textarea id="s-description" placeholder="Breve descrição do sistema..." rows="2"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Tipo de Rede</label>
                        <select id="s-internal" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                            <option value="1" selected>Interno</option>
                            <option value="0">Externo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Status</label>
                        <select id="s-status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                            <option value="online" selected>Online</option>
                            <option value="offline">Offline</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-700 rounded-xl text-sm">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 rounded-xl text-white text-sm">Salvar</button>
                </div>
            </form>
        `);

        $('#form-site').on('submit', function (e) {
            e.preventDefault();
            api.post('/api/sites', {
                name: $('#s-name').val(),
                url: $('#s-url').val(),
                description: $('#s-description').val(),
                is_internal: $('#s-internal').val(),
                status: $('#s-status').val(),
            })
            .done(function () {
                showToast('Site cadastrado!', 'success');
                closeModal();
                location.reload();
            })
            .fail(function () {
                showToast('Erro ao cadastrar site', 'error');
            });
        });
    });

    // Editar Site
    $(document).on('click', '.btn-edit-site', function () {
        const card = $(this).closest('.site-card');
        const id = card.data('id');
        const name = card.data('name');
        const url = card.data('url');
        const description = card.data('description');
        const internal = card.data('internal');
        const status = card.data('status');

        openModal(`
            <h3 class="font-bold text-lg text-white mb-4">Editar Link / Sistema</h3>
            <form id="form-edit-site" class="space-y-4">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Nome do Sistema</label>
                    <input type="text" id="edit-s-name" value="${escapeHtml(name)}" placeholder="Ex: Zabbix Monitoramento" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">URL de Acesso</label>
                    <input type="url" id="edit-s-url" value="${escapeHtml(url)}" placeholder="Ex: https://zabbix.nexus.com" required
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Descrição</label>
                    <textarea id="edit-s-description" placeholder="Breve descrição do sistema..." rows="2"
                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">${escapeHtml(description)}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Tipo de Rede</label>
                        <select id="edit-s-internal" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                            <option value="1" ${Number(internal) === 1 ? 'selected' : ''}>Interno</option>
                            <option value="0" ${Number(internal) === 0 ? 'selected' : ''}>Externo</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Status</label>
                        <select id="edit-s-status" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-white text-sm">
                            <option value="online" ${status === 'online' ? 'selected' : ''}>Online</option>
                            <option value="offline" ${status === 'offline' ? 'selected' : ''}>Offline</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-700 rounded-xl text-sm">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 rounded-xl text-white text-sm">Salvar</button>
                </div>
            </form>
        `);

        $('#form-edit-site').on('submit', function (e) {
            e.preventDefault();
            api.put(`/api/sites/${id}`, {
                name: $('#edit-s-name').val(),
                url: $('#edit-s-url').val(),
                description: $('#edit-s-description').val(),
                is_internal: $('#edit-s-internal').val(),
                status: $('#edit-s-status').val(),
            })
            .done(function () {
                showToast('Link atualizado!', 'success');
                closeModal();
                location.reload();
            })
            .fail(function () {
                showToast('Erro ao atualizar link', 'error');
            });
        });
    });

    // Excluir Site
    $(document).on('click', '.btn-delete-site', function () {
        const card = $(this).closest('.site-card');
        const id = card.data('id');
        const name = card.data('name');

        if (!confirm(`Deseja excluir o link para "${name}"?`)) return;

        api.delete(`/api/sites/${id}`)
            .done(function () {
                showToast('Link excluído!', 'success');
                location.reload();
            })
            .fail(function () {
                showToast('Erro ao excluir link', 'error');
            });
    });

    function escapeHtml(text) {
        return text ? String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;") : '';
    }
});
