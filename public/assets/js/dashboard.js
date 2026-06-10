$(function () {
    // Dashboard - atualização automática dos stats a cada 60 segundos
    function refreshDashboardStats() {
        api.get('/api/tasks')
            .done(function (tasks) {
                const active = tasks.filter(t => t.status !== 'done').length;
                // Atualiza a contagem de tarefas ativas se o elemento existir
                const $el = $('.dashboard-active-tasks');
                if ($el.length) $el.text(active);
            });
    }

    // Atualiza o relógio na barra superior se existir
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        $('#dashboard-clock').text(timeStr);
    }

    updateClock();
    setInterval(updateClock, 60000);
});
