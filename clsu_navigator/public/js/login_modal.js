// Login pop-up (homepage and campus map)

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('loginModal');
    if (!modal) return; // logged-in users have no pop-up

    const email = document.getElementById('loginEmail');
    const password = document.getElementById('loginPassword');
    const toggle = document.getElementById('pwToggle');
    let opener = null;

    function openModal(from) {
        opener = from || null;
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
        (email.value ? password : email).focus();
    }

    function closeModal() {
        modal.hidden = true;
        document.body.style.overflow = '';
        if (opener && opener.focus) opener.focus();
    }

    document.querySelectorAll('[data-login-open]').forEach((el) => {
        el.addEventListener('click', (event) => {
            event.preventDefault();
            openModal(el);
        });
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal || event.target.hasAttribute('data-close')) closeModal();
    });

    document.addEventListener('keydown', (event) => {
        if (modal.hidden) return;

        if (event.key === 'Escape') {
            closeModal();
            return;
        }

        if (event.key === 'Tab') {
            const items = Array.from(modal.querySelectorAll('button, a[href], input'))
                .filter((node) => !node.disabled && node.offsetParent !== null);
            const first = items[0];
            const last = items[items.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    toggle.addEventListener('click', () => {
        const show = password.type === 'password';
        password.type = show ? 'text' : 'password';
        toggle.textContent = show ? 'Hide' : 'Show';
    });

    // Re-open after a failed login
    if (modal.dataset.open === '1') openModal(null);
});
