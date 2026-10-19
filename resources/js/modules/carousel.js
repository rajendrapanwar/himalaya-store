// Horizontally scrolling carousels with buttons, mouse dragging, an optional
// autoplay that users can pause, and a scroll-position track.
import { prefersReducedMotion } from './ui';

export const initCarousels = () => {
    document.querySelectorAll('[data-carousel]').forEach((carousel) => {
        const section = carousel.closest('section');
        const previous = section?.querySelector('[data-carousel-previous]');
        const next = section?.querySelector('[data-carousel-next]');
        const toggle = section?.querySelector('[data-carousel-toggle]');
        const track = section?.querySelector('[data-carousel-progress]');
        const autoplayDelay = Number(carousel.dataset.carouselAutoplay || 0);
        if (!previous || !next) return;

        let isDragging = false;
        let wasDragged = false;
        let startX = 0;
        let startScrollLeft = 0;
        let autoplayTimer = null;
        let carouselIsVisible = !('IntersectionObserver' in window);
        let userPaused = prefersReducedMotion();

        const scrollAmount = () => {
            const firstItem = carousel.firstElementChild;
            return firstItem ? firstItem.getBoundingClientRect().width + 18 : carousel.clientWidth * 0.8;
        };

        const updateControls = () => {
            const end = carousel.scrollWidth - carousel.clientWidth;
            previous.disabled = carousel.scrollLeft <= 2;
            next.disabled = carousel.scrollLeft >= end - 2;
            if (track) {
                const visible = Math.min(1, carousel.clientWidth / Math.max(carousel.scrollWidth, 1));
                const progress = end > 0 ? carousel.scrollLeft / end : 0;
                track.style.setProperty('--thumb', `${(visible * 100).toFixed(2)}%`);
                track.style.setProperty('--offset', `${(progress * (1 / visible - 1) * 100).toFixed(2)}%`);
            }
        };

        const stopAutoplay = () => {
            if (autoplayTimer) window.clearTimeout(autoplayTimer);
            autoplayTimer = null;
        };

        const startAutoplay = () => {
            stopAutoplay();
            const canScroll = carousel.scrollWidth - carousel.clientWidth > 2;
            const isInteracting = isDragging || carousel.matches(':hover') || carousel.contains(document.activeElement);
            if (!autoplayDelay || userPaused || document.hidden || !canScroll || !carouselIsVisible || isInteracting) return;

            autoplayTimer = window.setTimeout(() => {
                const end = carousel.scrollWidth - carousel.clientWidth;
                if (carousel.scrollLeft >= end - 2) carousel.scrollTo({ left: 0, behavior: 'smooth' });
                else carousel.scrollBy({ left: scrollAmount(), behavior: 'smooth' });
                startAutoplay();
            }, autoplayDelay);
        };

        const syncToggle = () => {
            if (!toggle) return;
            toggle.setAttribute('aria-pressed', String(userPaused));
            toggle.setAttribute('aria-label', userPaused ? 'Resume automatic scrolling' : 'Pause automatic scrolling');
            toggle.querySelector('[data-icon-pause]')?.classList.toggle('hidden', userPaused);
            toggle.querySelector('[data-icon-play]')?.classList.toggle('hidden', !userPaused);
        };
        if (toggle && !autoplayDelay) toggle.hidden = true;
        toggle?.addEventListener('click', () => {
            userPaused = !userPaused;
            syncToggle();
            if (userPaused) stopAutoplay();
            else startAutoplay();
        });

        const moveManually = (direction) => {
            stopAutoplay();
            carousel.scrollBy({ left: direction * scrollAmount(), behavior: 'smooth' });
            startAutoplay();
        };

        previous.addEventListener('click', () => moveManually(-1));
        next.addEventListener('click', () => moveManually(1));
        carousel.addEventListener('scroll', updateControls, { passive: true });
        window.addEventListener('resize', () => { updateControls(); startAutoplay(); }, { passive: true });
        carousel.addEventListener('mouseenter', stopAutoplay);
        carousel.addEventListener('mouseleave', startAutoplay);
        carousel.addEventListener('focusin', stopAutoplay);
        carousel.addEventListener('focusout', () => {
            window.requestAnimationFrame(() => {
                if (!carousel.contains(document.activeElement)) startAutoplay();
            });
        });

        carousel.addEventListener('pointerdown', (event) => {
            if (event.pointerType !== 'mouse' || event.button !== 0) return;
            stopAutoplay();
            isDragging = true;
            wasDragged = false;
            startX = event.clientX;
            startScrollLeft = carousel.scrollLeft;
            carousel.classList.add('is-dragging');
            carousel.setPointerCapture(event.pointerId);
        });
        carousel.addEventListener('pointermove', (event) => {
            if (!isDragging) return;
            const distance = event.clientX - startX;
            if (Math.abs(distance) > 5) wasDragged = true;
            carousel.scrollLeft = startScrollLeft - distance;
            if (wasDragged) event.preventDefault();
        });
        const finishDragging = (event) => {
            if (!isDragging) return;
            isDragging = false;
            carousel.classList.remove('is-dragging');
            if (carousel.hasPointerCapture(event.pointerId)) carousel.releasePointerCapture(event.pointerId);
            startAutoplay();
        };
        carousel.addEventListener('pointerup', finishDragging);
        carousel.addEventListener('pointercancel', finishDragging);
        carousel.addEventListener('lostpointercapture', () => {
            isDragging = false;
            carousel.classList.remove('is-dragging');
            startAutoplay();
        });
        carousel.addEventListener('dragstart', (event) => event.preventDefault());
        carousel.addEventListener('click', (event) => {
            if (!wasDragged) return;
            event.preventDefault();
            event.stopPropagation();
            wasDragged = false;
        }, true);
        document.addEventListener('visibilitychange', () => (document.hidden ? stopAutoplay() : startAutoplay()));

        if ('IntersectionObserver' in window) {
            new IntersectionObserver(([entry]) => {
                carouselIsVisible = entry.isIntersecting;
                if (carouselIsVisible) startAutoplay();
                else stopAutoplay();
            }, { threshold: 0.15 }).observe(carousel);
        }

        syncToggle();
        updateControls();
        startAutoplay();
    });
};
