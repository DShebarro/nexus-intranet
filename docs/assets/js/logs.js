$(function () {
    // Filtragem de Logs
    function applyLogFilters() {
        const searchVal = $('#search-log').val().toLowerCase();
        const typeVal = $('#filter-log-type').val();

        let visibleCount = 0;

        $('.log-row').each(function () {
            const desc = String($(this).data('desc')).toLowerCase();
            const ip = String($(this).data('ip')).toLowerCase();
            const ua = String($(this).data('ua')).toLowerCase();
            const type = $(this).data('type');

            const matchesSearch = desc.includes(searchVal) || ip.includes(searchVal) || ua.includes(searchVal);
            const matchesType = (typeVal === 'all' || type === typeVal);

            if (matchesSearch && matchesType) {
                $(this).removeClass('hidden');
                visibleCount++;
            } else {
                $(this).addClass('hidden');
            }
        });

        // Se nenhum log corresponder aos filtros, exibe linha informativa
        if (visibleCount === 0) {
            if ($('#filter-no-results-row').length === 0) {
                $('#logs-table-body').append(`
                    <tr id="filter-no-results-row">
                        <td colspan="5" class="p-8 text-center text-slate-500">Nenhum log corresponde aos filtros aplicados.</td>
                    </tr>
                `);
            }
        } else {
            $('#filter-no-results-row').remove();
        }
    }

    $('#search-log').on('keyup', applyLogFilters);
    $('#filter-log-type').on('change', applyLogFilters);

    // Limpar Logs
    $('#btn-clear-logs').on('click', function () {
        if (!confirm('Deseja realmente excluir todo o histórico de logs de atividades?')) return;

        api.delete('/api/logs')
            .done(function () {
                showToast('Histórico de logs limpo!', 'success');
                // Recarrega para exibir o novo log de exclusão gerado pelo servidor
                location.reload();
            })
            .fail(function () {
                showToast('Erro ao limpar histórico de logs', 'error');
            });
    });
});
