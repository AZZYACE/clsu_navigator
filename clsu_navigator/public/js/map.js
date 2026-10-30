// Campus map menu

document.addEventListener('DOMContentLoaded', () => {
    // Filters
    document.querySelectorAll('.acc-btn').forEach((button) => {
        button.addEventListener('click', () => {
            const panel = document.getElementById(button.getAttribute('aria-controls'));
            const open = button.getAttribute('aria-expanded') === 'true';

            button.setAttribute('aria-expanded', String(!open));
            button.querySelector('.acc-ico').textContent = open ? '+' : '−';
            panel.hidden = open;
        });
    });

    // Map view buttons
    const viewButtons = document.querySelectorAll('.seg button');
    viewButtons.forEach((button) => {
        button.addEventListener('click', () => {
            viewButtons.forEach((other) => other.setAttribute('aria-pressed', String(other === button)));
            document.dispatchEvent(new CustomEvent('map:view', { detail: button.dataset.view }));
        });
    });

    // Clear filters
    const clear = document.getElementById('clearFilters');
    if (clear) {
        clear.addEventListener('click', () => {
            document.querySelectorAll('.acc-panel input[type=checkbox]').forEach((box) => { box.checked = false; });
        });
    }

    // Collapse the menu
    const layout = document.getElementById('mapLayout');
    const toggle = document.getElementById('sideToggle');

    if (layout && toggle) {
        const text = toggle.querySelector('.side-toggle-text');
        const chev = toggle.querySelector('.chev');

        const setCollapsed = (collapsed, save) => {
            layout.classList.toggle('collapsed', collapsed);
            toggle.setAttribute('aria-expanded', String(!collapsed));
            toggle.setAttribute('aria-label', collapsed ? 'Open the menu' : 'Collapse the menu');
            text.textContent = collapsed ? 'Show menu' : 'Hide menu';
            chev.innerHTML = collapsed ? '&rsaquo;' : '&lsaquo;';

            if (save) {
                try { localStorage.setItem('clsuMapMenuCollapsed', collapsed ? '1' : '0'); } catch (e) { /* storage unavailable */ }
            }
        };

        let saved = false;
        try { saved = localStorage.getItem('clsuMapMenuCollapsed') === '1'; } catch (e) { /* storage unavailable */ }
        setCollapsed(saved, false);

        // Animate only after the saved state is applied
        requestAnimationFrame(() => layout.classList.add('ready'));

        toggle.addEventListener('click', () => {
            setCollapsed(!layout.classList.contains('collapsed'), true);
        });

        // Lets the map know when to resize
        layout.addEventListener('transitionend', (event) => {
            if (event.target === layout) document.dispatchEvent(new CustomEvent('map:resize'));
        });
    }

    // Filter style switch
    const styleSwitch = document.getElementById('filterStyle');
    const boxed = document.getElementById('filtersBoxed');
    const bubble = document.getElementById('filtersBubble');

    if (styleSwitch && boxed && bubble) {
        const setStyle = (useBubble, save) => {
            styleSwitch.setAttribute('aria-checked', String(useBubble));
            boxed.hidden = useBubble;
            bubble.hidden = !useBubble;
            document.getElementById('optBoxed').classList.toggle('on', !useBubble);
            document.getElementById('optBubble').classList.toggle('on', useBubble);

            if (save) {
                try { localStorage.setItem('clsuMapFilterStyle', useBubble ? 'bubble' : 'boxed'); } catch (e) { /* storage unavailable */ }
            }
            document.dispatchEvent(new CustomEvent('map:filterstyle', { detail: useBubble ? 'bubble' : 'boxed' }));
        };

        let saved = 'boxed';
        try { saved = localStorage.getItem('clsuMapFilterStyle') || 'boxed'; } catch (e) { /* storage unavailable */ }
        setStyle(saved === 'bubble', false);

        styleSwitch.addEventListener('click', () => {
            setStyle(styleSwitch.getAttribute('aria-checked') !== 'true', true);
        });
    }
});
