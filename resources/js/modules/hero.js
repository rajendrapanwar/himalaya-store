// Homepage hero slideshow. Each slide's progress bar is a CSS animation, and
// its `animationend` advances the slideshow — so pausing the animation (hover,
// focus, hidden tab or the pause button) pauses the slideshow in lockstep.
import { playSplit } from './gsap-motion';
import { prefersReducedMotion } from './ui';

export const initHero = () => {
    document.querySelectorAll('[data-hero-slider]').forEach((slider) => {
        const slides = [...slider.querySelectorAll('[data-hero-slide]')];
        const images = [...slider.querySelectorAll('[data-hero-image]')];
        const dots = [...slider.querySelectorAll('[data-hero-dot]')];
        const bars = dots.map((dot) => dot.querySelector('[data-hero-progress]'));
        const toggle = slider.querySelector('[data-hero-toggle]');
        if (slides.length < 2) return;

        const delay = Number(slider.dataset.heroDelay || 7000);
        slider.style.setProperty('--hero-delay', `${delay}ms`);
        // Reduced motion collapses CSS animations to ~0ms, so the progress bar
        // cannot act as the timer there: start paused and use a plain timeout.
        const reduced = prefersReducedMotion();
        const pauseReasons = new Set(reduced ? ['user'] : []);
        let current = 0;
        let pointerStart = null;
        let fallbackTimer = null;
        let pendingAdvance = false;

        const scheduleFallback = () => {
            window.clearTimeout(fallbackTimer);
            if (reduced && pauseReasons.size === 0) fallbackTimer = window.setTimeout(() => show(current + 1), delay);
        };

        const syncPaused = () => {
            if (pauseReasons.size === 0 && pendingAdvance) {
                pendingAdvance = false;
                show(current + 1);
            }
            scheduleFallback();
            slider.classList.toggle('is-paused', pauseReasons.size > 0);
            if (!toggle) return;
            const userPaused = pauseReasons.has('user');
            toggle.setAttribute('aria-pressed', String(userPaused));
            toggle.setAttribute('aria-label', userPaused ? 'Play slideshow' : 'Pause slideshow');
            toggle.querySelector('[data-icon-pause]')?.classList.toggle('hidden', userPaused);
            toggle.querySelector('[data-icon-play]')?.classList.toggle('hidden', !userPaused);
        };

        const show = (index) => {
            pendingAdvance = false;
            current = (index + slides.length) % slides.length;
            slides.forEach((slide, position) => {
                const active = position === current;
                slide.classList.toggle('is-active', active);
                slide.setAttribute('aria-hidden', String(!active));
                slide.inert = !active;
                if (active) playSplit(slide.querySelector('[data-split]'));
            });
            images.forEach((image, position) => image.classList.toggle('is-active', position === current));
            dots.forEach((dot, position) => dot.setAttribute('aria-selected', String(position === current)));
            bars.forEach((bar, position) => {
                if (!bar) return;
                bar.classList.remove('is-active');
                bar.classList.toggle('is-done', position < current);
                if (position === current) {
                    void bar.offsetWidth; // restart the fill animation
                    bar.classList.add('is-active');
                }
            });
            scheduleFallback();
        };

        bars.forEach((bar) => bar?.addEventListener('animationend', () => {
            if (reduced || !bar.classList.contains('is-active')) return;
            if (pauseReasons.size === 0) show(current + 1);
            else pendingAdvance = true;
        }));

        slider.querySelector('[data-hero-previous]')?.addEventListener('click', () => show(current - 1));
        slider.querySelector('[data-hero-next]')?.addEventListener('click', () => show(current + 1));
        dots.forEach((dot, index) => dot.addEventListener('click', () => show(index)));
        toggle?.addEventListener('click', () => {
            if (pauseReasons.has('user')) pauseReasons.delete('user');
            else pauseReasons.add('user');
            syncPaused();
        });

        const pauseFor = (reason, paused) => {
            if (paused) pauseReasons.add(reason);
            else pauseReasons.delete(reason);
            syncPaused();
        };
        slider.addEventListener('mouseenter', () => pauseFor('hover', true));
        slider.addEventListener('mouseleave', () => pauseFor('hover', false));
        slider.addEventListener('focusin', () => pauseFor('focus', true));
        slider.addEventListener('focusout', () => {
            window.requestAnimationFrame(() => pauseFor('focus', slider.contains(document.activeElement)));
        });
        document.addEventListener('visibilitychange', () => pauseFor('hidden', document.hidden));

        slider.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowLeft') { event.preventDefault(); show(current - 1); }
            if (event.key === 'ArrowRight') { event.preventDefault(); show(current + 1); }
        });
        slider.addEventListener('pointerdown', (event) => { if (event.pointerType !== 'mouse') pointerStart = event.clientX; });
        slider.addEventListener('pointerup', (event) => {
            if (pointerStart === null) return;
            const distance = event.clientX - pointerStart;
            pointerStart = null;
            if (Math.abs(distance) >= 45) show(current + (distance < 0 ? 1 : -1));
        });

        syncPaused();
    });
};
