// Scroll-linked motion: reveal-on-scroll, split headlines, count-up numbers,
// parallax, magnetic buttons and the scroll-driven provenance story. Every
// effect is skipped (content shown as authored) when reduced motion is on.
import { hasFinePointer, prefersReducedMotion, rafThrottle } from './ui';

const belowFold = (element) => element.getBoundingClientRect().top > window.innerHeight * 0.9;

export const splitWords = (element) => {
    if (!element || element.dataset.splitDone) return;
    const text = element.textContent.trim().replace(/\s+/g, ' ');
    element.setAttribute('aria-label', text);
    element.replaceChildren();
    text.split(' ').forEach((word, index) => {
        const outer = document.createElement('span');
        outer.className = 'split-word';
        outer.setAttribute('aria-hidden', 'true');
        const inner = document.createElement('span');
        inner.style.setProperty('--w', String(index));
        inner.textContent = word;
        outer.appendChild(inner);
        element.append(outer, ' ');
    });
    element.dataset.splitDone = 'true';
    element.classList.add('is-split');
};

export const playSplit = (element) => {
    if (!element?.dataset.splitDone) return;
    element.classList.remove('is-split-visible');
    void element.offsetWidth;
    element.classList.add('is-split-visible');
};

const initReveal = () => {
    document.querySelectorAll('[data-reveal-stagger]').forEach((group) => {
        [...group.querySelectorAll(':scope > [data-reveal]')].forEach((child, index) => {
            if (!child.style.getPropertyValue('--i')) child.style.setProperty('--i', String(index));
        });
    });

    const elements = [...document.querySelectorAll('[data-reveal]')].filter(belowFold);
    if (!elements.length) return;
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('is-revealed');
            observer.unobserve(entry.target);
            entry.target.addEventListener('transitionend', () => {
                entry.target.classList.remove('reveal-ready', 'is-revealed');
                entry.target.style.removeProperty('--i');
            }, { once: true });
        });
    }, { rootMargin: '0px 0px -10% 0px', threshold: 0.12 });

    elements.forEach((element) => {
        element.classList.add('reveal-ready');
        observer.observe(element);
    });
};

const formatNumber = new Intl.NumberFormat('en-IN');
const initCounters = () => {
    const counters = [...document.querySelectorAll('[data-count-to]')].filter(belowFold);
    if (!counters.length) return;
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            observer.unobserve(entry.target);
            const element = entry.target;
            const target = Number(element.dataset.countTo) || 0;
            const duration = 1600;
            const start = performance.now();
            const step = (now) => {
                const t = Math.min(1, (now - start) / duration);
                const eased = 1 - Math.pow(2, -10 * t);
                element.textContent = formatNumber.format(Math.round(target * (t === 1 ? 1 : eased)));
                if (t < 1) window.requestAnimationFrame(step);
            };
            window.requestAnimationFrame(step);
        });
    }, { threshold: 0.6 });
    counters.forEach((counter) => {
        counter.textContent = '0';
        observer.observe(counter);
    });
};

const initParallax = () => {
    const elements = [...document.querySelectorAll('[data-parallax]')];
    if (!elements.length) return;
    const update = () => {
        const viewport = window.innerHeight;
        elements.forEach((element) => {
            const rect = element.getBoundingClientRect();
            if (rect.bottom < -100 || rect.top > viewport + 100) return;
            const factor = Number(element.dataset.parallax) || 0.1;
            const offset = (rect.top + rect.height / 2 - viewport / 2) * -factor;
            // `translate` composes with any `transform` animation on the same element.
            element.style.translate = `0 ${offset.toFixed(1)}px`;
        });
    };
    window.addEventListener('scroll', rafThrottle(update), { passive: true });
    window.addEventListener('resize', rafThrottle(update), { passive: true });
    update();
};

const initMagnetic = () => {
    if (!hasFinePointer()) return;
    document.querySelectorAll('[data-magnetic]').forEach((element) => {
        element.addEventListener('pointermove', (event) => {
            const rect = element.getBoundingClientRect();
            const x = (event.clientX - rect.left - rect.width / 2) * 0.22;
            const y = (event.clientY - rect.top - rect.height / 2) * 0.32;
            element.style.translate = `${x.toFixed(1)}px ${y.toFixed(1)}px`;
        });
        element.addEventListener('pointerleave', () => { element.style.translate = '0 0'; });
    });
};

const initStory = () => {
    const story = document.querySelector('[data-story]');
    if (!story) return;
    const steps = [...story.querySelectorAll('[data-story-step]')];
    const visuals = [...story.querySelectorAll('[data-story-visual]')];
    const progress = story.querySelector('[data-story-progress]');
    const list = progress?.closest('ol');

    const activate = (index) => {
        steps.forEach((step, position) => step.classList.toggle('is-active', position === index));
        visuals.forEach((visual, position) => visual.classList.toggle('is-active', position === index));
    };
    activate(0);
    story.classList.add('is-story-ready');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) activate(steps.indexOf(entry.target));
        });
    }, { rootMargin: '-45% 0px -45% 0px' });
    steps.forEach((step) => observer.observe(step));

    if (progress && list) {
        const update = () => {
            const rect = list.getBoundingClientRect();
            const value = Math.min(1, Math.max(0, (window.innerHeight * 0.55 - rect.top) / rect.height));
            progress.style.setProperty('--progress', value.toFixed(3));
        };
        window.addEventListener('scroll', rafThrottle(update), { passive: true });
        update();
    }
};

export const initMotion = () => {
    const reduced = prefersReducedMotion();
    document.querySelectorAll('[data-split]').forEach((heading) => {
        if (reduced) return;
        splitWords(heading);
        if (!heading.closest('[data-hero-slide]') || heading.closest('.is-active')) playSplit(heading);
    });
    initStory();
    if (reduced) return;
    initReveal();
    initCounters();
    initParallax();
    initMagnetic();
};
