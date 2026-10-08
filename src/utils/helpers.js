export function escapeHTML(str) {
    if (!str) return '';
    return str.replace(/[&<>"']/g, tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[tag] || tag));
}

export function showToast(message) {
    const toast = document.getElementById('toast');
    const msgSpan = document.getElementById('toast-message');
    if (toast && msgSpan) {
        msgSpan.innerText = message;
        toast.classList.remove('translate-y-20', 'opacity-0');
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 3000);
    }
}

export function updateSavedBadge(portfoliosDB) {
    const badge = document.getElementById('saved-count-badge');
    if (badge) badge.innerText = portfoliosDB.length;
}
