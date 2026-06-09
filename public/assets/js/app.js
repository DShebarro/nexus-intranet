window.showToast = function(msg, type = 'info') {
    const toast = $('#toast');
    $('#toast-message').text(msg);
    toast.removeClass('hidden bg-blue-600 bg-green-600 bg-rose-600');
    const colors = { info: 'bg-blue-600', success: 'bg-green-600', error: 'bg-rose-600' };
    toast.addClass(colors[type] || 'bg-blue-600').removeClass('hidden');
    setTimeout(() => toast.addClass('hidden'), 4000);
};

window.api = {
    get: (url) => $.getJSON(url),
    post: (url, data) => $.ajax({ url, method: 'POST', contentType: 'application/json', data: JSON.stringify(data) }),
    put: (url, data) => $.ajax({ url, method: 'PUT', contentType: 'application/json', data: JSON.stringify(data) }),
    patch: (url, data) => $.ajax({ url, method: 'PATCH', contentType: 'application/json', data: JSON.stringify(data) }),
    delete: (url) => $.ajax({ url, method: 'DELETE' }),
};

window.openModal = function(html) {
    $('#modal-body').html(html);
    $('#modal-container').removeClass('hidden').addClass('flex');
    if (typeof lucide !== 'undefined') lucide.createIcons();
};

window.closeModal = function() {
    $('#modal-container').addClass('hidden').removeClass('flex');
};

// Carregar categories.js dinamicamente
$.getScript('/assets/js/categories.js');
