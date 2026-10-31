document.addEventListener('DOMContentLoaded', () => {
    // Small progressive enhancement: prevent accidental double-submits.
    document.querySelectorAll('form.inquiry-form').forEach((form) => {
        form.addEventListener('submit', () => {
            const button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
                button.dataset.originalText = button.textContent;
                button.textContent = 'Sending…';
            }
        });
    });
});
