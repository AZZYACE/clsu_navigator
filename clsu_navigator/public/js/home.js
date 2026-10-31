// Homepage

document.addEventListener('DOMContentLoaded', () => {
    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach((link) => {
        link.addEventListener('click', (event) => {
            const targetId = link.getAttribute('href');

            if (targetId === '#home') {
                event.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
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

    // Sticky header and nav highlight
    const top = document.getElementById('siteTop');
    const wrap = document.getElementById('topWrap');
    const navLinks = Array.from(document.querySelectorAll('.navbar ul a[href^="#"]'));

    if (top && wrap) {
        document.documentElement.classList.add('has-fixed-top');

        // Keeps the page from jumping when the header shrinks
        const measure = () => {
            const wasScrolled = top.classList.contains('scrolled');
            top.classList.add('measuring');

            // Shrunken height: sections scroll to just below it
            top.classList.add('scrolled');
            document.documentElement.style.setProperty('--hdr', top.offsetHeight + 'px');

            // Full height: reserved at the top of the page
            top.classList.remove('scrolled');
            wrap.style.height = top.offsetHeight + 'px';

            if (wasScrolled) top.classList.add('scrolled');
            top.classList.remove('measuring');
        };

        const updateHeader = () => {
            const y = window.scrollY;
            if (!top.classList.contains('scrolled') && y > 60) top.classList.add('scrolled');
            else if (top.classList.contains('scrolled') && y < 10) top.classList.remove('scrolled');
        };

        const updateActiveLink = () => {
            const offset = top.offsetHeight + 40;
            const atBottom = window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 4;
            let current = navLinks[0];
            let best = -Infinity;
            let lowest = -Infinity;
            let last = navLinks[navLinks.length - 1];

            // Highlight by section position
            navLinks.forEach((link) => {
                const section = document.querySelector(link.getAttribute('href'));
                if (!section) return;
                const top = section.getBoundingClientRect().top;
                if (top <= offset && top > best) { best = top; current = link; }
                if (top > lowest) { lowest = top; last = link; }
            });

            // At the bottom, highlight the last section
            if (atBottom) current = last;

            navLinks.forEach((link) => {
                link.parentElement.classList.toggle('on', link === current);
            });
        };

        const onScroll = () => {
            updateHeader();
            updateActiveLink();
        };

        measure();
        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', () => { measure(); onScroll(); });
        window.addEventListener('load', measure);
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(measure);
    }
});
