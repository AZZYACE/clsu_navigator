// CLSU Navigator homepage JavaScript

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('a[href^="#"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            const targetId = link.getAttribute('href');

            if (targetId === '#map-coming-soon') {
                event.preventDefault();
                alert('The campus map page has not been built yet.');
                return;
            }

            if (!targetId || targetId === '#') return;

            const target = document.querySelector(targetId);
            if (target) {
                event.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
