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

    // Map view buttons (side menu and bubble UI stay in sync)
    const viewButtons = document.querySelectorAll('[data-view]');
    viewButtons.forEach((button) => {
        button.addEventListener('click', () => {
            viewButtons.forEach((other) => other.setAttribute('aria-pressed', String(other.dataset.view === button.dataset.view)));
            document.dispatchEvent(new CustomEvent('map:view', { detail: button.dataset.view }));
        });
    });

    // Filter checkboxes (side menu and bubble UI stay in sync)
    const allBoxes = () => document.querySelectorAll('.f-box');
    const clearButtons = document.querySelectorAll('#clearFilters, #bbClear');
    const bbClear = document.getElementById('bbClear');

    const selectedFilters = () => {
        const out = {};
        document.querySelectorAll('#filtersBoxed .f-box:checked').forEach((box) => {
            const key = box.name.replace('[]', '');
            (out[key] = out[key] || []).push(box.value);
        });
        return out;
    };

    const refreshFilterUI = () => {
        document.querySelectorAll('[data-count-for]').forEach((badge) => {
            const count = document.querySelectorAll('#' + badge.dataset.countFor + ' .f-box:checked').length;
            badge.textContent = String(count);
            badge.hidden = count === 0;
            badge.closest('.bb-chip').classList.toggle('active', count > 0);
        });
        if (bbClear) bbClear.hidden = document.querySelectorAll('.f-box:checked').length === 0;
    };

    document.addEventListener('change', (event) => {
        const box = event.target;
        if (!box.matches || !box.matches('.f-box')) return;

        allBoxes().forEach((other) => {
            if (other !== box && other.name === box.name && other.value === box.value) other.checked = box.checked;
        });
        refreshFilterUI();
        document.dispatchEvent(new CustomEvent('map:filter', { detail: selectedFilters() }));
    });

    clearButtons.forEach((button) => {
        button.addEventListener('click', () => {
            allBoxes().forEach((box) => { box.checked = false; });
            refreshFilterUI();
            document.dispatchEvent(new CustomEvent('map:filter', { detail: selectedFilters() }));
        });
    });

    // Search (side menu and bubble UI stay in sync)
    const searchInputs = document.querySelectorAll('.f-search');
    searchInputs.forEach((input) => {
        input.addEventListener('input', () => {
            searchInputs.forEach((other) => { if (other !== input) other.value = input.value; });
            document.dispatchEvent(new CustomEvent('map:search', { detail: input.value }));
        });
    });

    // Bubble pop-ups (one open at a time)
    const toggles = document.querySelectorAll('.bb-toggle');
    const popOf = (toggle) => document.getElementById(toggle.getAttribute('aria-controls'));

    const closePops = (except) => {
        toggles.forEach((toggle) => {
            if (toggle === except) return;
            toggle.setAttribute('aria-expanded', 'false');
            popOf(toggle).hidden = true;
        });
    };

    toggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const pop = popOf(toggle);
            const open = toggle.getAttribute('aria-expanded') === 'true';

            closePops(toggle);
            toggle.setAttribute('aria-expanded', String(!open));
            pop.hidden = open;

            if (!open) {
                // Keep the pop-up inside the map
                pop.classList.remove('right');
                const area = document.getElementById('bubbleUI').getBoundingClientRect();
                if (pop.getBoundingClientRect().right > area.right - 8) pop.classList.add('right');
            }
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.bb-wrap')) closePops();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const open = document.querySelector('.bb-toggle[aria-expanded="true"]');
        if (open) { closePops(); open.focus(); }
    });

    // Collapse the menu
    const layout = document.getElementById('mapLayout');
    const toggle = document.getElementById('sideToggle');

    if (layout && toggle) {
        const text = toggle.querySelector('.side-toggle-text');

        const setCollapsed = (collapsed, save) => {
            layout.classList.toggle('collapsed', collapsed);
            toggle.setAttribute('aria-expanded', String(!collapsed));
            toggle.setAttribute('aria-label', collapsed ? 'Open the menu' : 'Collapse the menu');
            text.textContent = collapsed ? 'Show menu' : 'Hide menu';
            toggle.title = collapsed ? 'Show menu' : 'Hide menu';

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

    // Zoom buttons (the map listens for map:zoom)
    document.querySelectorAll('[data-zoom]').forEach((button) => {
        button.addEventListener('click', () => {
            document.dispatchEvent(new CustomEvent('map:zoom', { detail: button.dataset.zoom }));
        });
    });

    // Full screen
    const fsButton = document.getElementById('fsBtn');
    const fsTarget = document.getElementById('mapLayout');
    const fsEnabled = document.fullscreenEnabled || document.webkitFullscreenEnabled;

    if (fsButton && fsTarget && fsEnabled) {
        fsButton.hidden = false;
        const current = () => document.fullscreenElement || document.webkitFullscreenElement;

        fsButton.addEventListener('click', () => {
            if (current()) {
                (document.exitFullscreen || document.webkitExitFullscreen).call(document);
            } else {
                (fsTarget.requestFullscreen || fsTarget.webkitRequestFullscreen).call(fsTarget);
            }
        });

        const onChange = () => {
            const on = current() === fsTarget;
            fsTarget.classList.toggle('is-fullscreen', on);
            fsButton.setAttribute('aria-label', on ? 'Exit full screen' : 'Enter full screen');
            fsButton.title = on ? 'Exit full screen' : 'Full screen';
            document.dispatchEvent(new CustomEvent('map:resize'));
        };
        document.addEventListener('fullscreenchange', onChange);
        document.addEventListener('webkitfullscreenchange', onChange);
    }

    // Filter style switch
    const styleSwitch = document.getElementById('filterStyle');
    const bubbleUI = document.getElementById('bubbleUI');

    if (styleSwitch && layout && bubbleUI) {
        const setStyle = (useBubble, save) => {
            styleSwitch.setAttribute('aria-checked', String(useBubble));
            layout.classList.toggle('bubble-mode', useBubble);
            bubbleUI.hidden = !useBubble;
            if (toggle) toggle.hidden = useBubble; // side menu is not used in bubble style
            closePops();
            document.getElementById('optBoxed').classList.toggle('on', !useBubble);
            document.getElementById('optBubble').classList.toggle('on', useBubble);

            if (save) {
                try { localStorage.setItem('clsuMapFilterStyle', useBubble ? 'bubble' : 'boxed'); } catch (e) { /* storage unavailable */ }
            }
            document.dispatchEvent(new CustomEvent('map:filterstyle', { detail: useBubble ? 'bubble' : 'boxed' }));
            document.dispatchEvent(new CustomEvent('map:resize'));
        };

        let saved = 'boxed';
        try { saved = localStorage.getItem('clsuMapFilterStyle') || 'boxed'; } catch (e) { /* storage unavailable */ }
        setStyle(saved === 'bubble', false);

        styleSwitch.addEventListener('click', () => {
            setStyle(styleSwitch.getAttribute('aria-checked') !== 'true', true);
        });
    }
});
