// Sticky header behaviour, dropdown menus, the announcement rotator, the
// reading-progress bar and the back-to-top button.
import { prefersReducedMotion, rafThrottle } from './ui';

const closeMenus = (except = null) => {
    document.querySelectorAll('[data-menu-group]').forEach((group) => {
        if (group === except) return;
        group.dataset.open = 'false';
        group.querySelector('[data-menu-button]')?.setAttribute('aria-expanded', 'false');
    });
};

export const isOverlayOpen = () => document.documentElement.classList.contains('has-overlay');

export const initHeader = () => {
    const header = document.querySelector('[data-site-header]');
    const progressBar = document.querySelector('[data-scroll-progress]');
    const backToTop = document.querySelector('[data-back-to-top]');
    let lastY = window.scrollY;

    const update = () => {
        const y = window.scrollY;
        const max = document.documentElement.scrollHeight - window.innerHeight;
        const progress = max > 0 ? Math.min(1, Math.max(0, y / max)) : 0;

        if (header) {
            header.classList.toggle('is-scrolled', y > 8);
            const menuOpen = header.querySelector('[data-menu-group][data-open="true"]') || header.matches(':focus-within');
            if (y > 260 && y > lastY + 4 && !menuOpen && !isOverlayOpen()) header.classList.add('is-hidden');
            else if (y < lastY - 4 || y <= 260) header.classList.remove('is-hidden');
        }
        progressBar?.style.setProperty('--progress', progress.toFixed(4));
        if (backToTop) {
            backToTop.classList.toggle('is-visible', y > window.innerHeight * 0.9);
            backToTop.style.setProperty('--progress', progress.toFixed(4));
        }
        lastY = y;
    };

    window.addEventListener('scroll', rafThrottle(update), { passive: true });
    update();

    backToTop?.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: prefersReducedMotion() ? 'auto' : 'smooth' });
        document.getElementById('main-content')?.focus({ preventScroll: true });
    });

    document.querySelectorAll('[data-menu-group]').forEach((group) => {
        const button = group.querySelector('[data-menu-button]');
        button?.addEventListener('click', (event) => {
            event.stopPropagation();
            const open = group.dataset.open !== 'true';
            closeMenus(group);
            group.dataset.open = String(open);
            button.setAttribute('aria-expanded', String(open));
        });
    });
    document.addEventListener('click', (event) => {
        if (!(event.target instanceof Element) || !event.target.closest('[data-menu]')) closeMenus();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeMenus();
    });

    // Rotating announcement messages.
    const rotator = document.querySelector('[data-rotator]');
    const messages = rotator ? [...rotator.children] : [];
    if (messages.length > 1) {
        let index = 0;
        let paused = false;
        rotator.addEventListener('mouseenter', () => { paused = true; });
        rotator.addEventListener('mouseleave', () => { paused = false; });
        rotator.addEventListener('focusin', () => { paused = true; });
        rotator.addEventListener('focusout', () => { paused = false; });
        messages.forEach((message, position) => {
            if (position !== 0) message.setAttribute('tabindex', '-1');
        });
        window.setInterval(() => {
            if (paused || document.hidden) return;
            const current = messages[index];
            index = (index + 1) % messages.length;
            const next = messages[index];
            current.classList.remove('is-active');
            current.classList.add('is-leaving');
            current.setAttribute('tabindex', '-1');
            window.setTimeout(() => current.classList.remove('is-leaving'), 650);
            next.classList.add('is-active');
            next.removeAttribute('tabindex');
        }, 4500);
    }
};
