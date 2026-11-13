// Scroll-driven motion for the storefront, built on GSAP + ScrollTrigger with
// Lenis smooth scrolling. Everything here is progressive enhancement: with
// reduced motion (or if this module fails) content is shown as authored.
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';
import Lenis from 'lenis';
import { hasFinePointer, prefersReducedMotion } from './ui';

gsap.registerPlugin(ScrollTrigger, SplitText);

let lenis = null;
const reduced = prefersReducedMotion();

export const scrollTo = (target) => {
    if (lenis) lenis.scrollTo(target, { offset: -90 });
    else if (typeof target === 'number') window.scrollTo({ top: target, behavior: reduced ? 'auto' : 'smooth' });
    else target?.scrollIntoView({ behavior: reduced ? 'auto' : 'smooth' });
};

const initSmoothScroll = () => {
    if (reduced) return;
    lenis = new Lenis({ lerp: 0.11, smoothWheel: true, anchors: { offset: -90 } });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);
    // Overlays (menu, drawers, search) lock the page while open.
    document.addEventListener('overlay:open', () => lenis.stop());
    document.addEventListener('overlay:close', () => lenis.start());
};

/* ── Headlines: lines rise out of a mask ─────────────────────────────── */
const splits = new WeakMap();
export const playSplit = (element) => {
    const split = splits.get(element);
    if (!split) return;
    gsap.fromTo(split.lines, { yPercent: 110 }, { yPercent: 0, duration: 1.25, ease: 'expo.out', stagger: 0.09, overwrite: true });
};

const initSplitHeadlines = () => {
    document.querySelectorAll('[data-split]').forEach((element) => {
        if (reduced) {
            element.classList.add('is-split');
            return;
        }
        const split = SplitText.create(element, { type: 'lines', mask: 'lines', linesClass: 'split-line', aria: 'auto' });
        splits.set(element, split);
        element.classList.add('is-split');
        const heroSlide = element.closest('[data-hero-copy]');
        if (heroSlide) {
            gsap.set(split.lines, { yPercent: 110 });
            if (heroSlide.classList.contains('is-active')) playSplit(element);
            return;
        }
        gsap.set(split.lines, { yPercent: 110 });
        ScrollTrigger.create({ trigger: element, start: 'top 88%', once: true, onEnter: () => playSplit(element) });
    });
};

/* ── Generic reveals ─────────────────────────────────────────────────── */
const revealFrom = {
    up: { y: 60, autoAlpha: 0 },
    left: { x: -70, autoAlpha: 0 },
    right: { x: 70, autoAlpha: 0 },
    scale: { scale: 0.9, autoAlpha: 0 },
    fade: { autoAlpha: 0 },
};

const initReveals = () => {
    if (reduced) return;
    document.querySelectorAll('[data-reveal]').forEach((element) => {
        const kind = element.dataset.reveal || 'up';
        if (kind === 'clip') {
            const image = element.querySelector('img');
            gsap.set(element, { clipPath: 'inset(100% 0% 0% 0%)', visibility: 'visible' });
            if (image) gsap.set(image, { scale: 1.3 });
            ScrollTrigger.create({
                trigger: element, start: 'top 85%', once: true,
                onEnter: () => {
                    gsap.to(element, { clipPath: 'inset(0% 0% 0% 0%)', duration: 1.4, ease: 'expo.inOut' });
                    if (image) gsap.to(image, { scale: 1, duration: 2, ease: 'expo.out' });
                },
            });
            return;
        }
        const from = revealFrom[kind] ?? revealFrom.up;
        const delay = Number(element.style.getPropertyValue('--i') || 0) * 0.08;
        gsap.set(element, from);
        ScrollTrigger.create({
            trigger: element, start: 'top 90%', once: true,
            onEnter: () => gsap.to(element, { x: 0, y: 0, scale: 1, autoAlpha: 1, duration: 1.2, delay, ease: 'expo.out', clearProps: 'transform' }),
        });
    });

    document.querySelectorAll('[data-reveal-stagger]').forEach((group) => {
        [...group.children].forEach((child, index) => {
            if (child.matches('[data-reveal]')) return;
            gsap.set(child, { y: 50, autoAlpha: 0 });
            ScrollTrigger.create({
                trigger: child, start: 'top 92%', once: true,
                onEnter: () => gsap.to(child, { y: 0, autoAlpha: 1, duration: 1.1, delay: (index % 4) * 0.08, ease: 'expo.out', clearProps: 'transform' }),
            });
        });
    });
};

/* ── Parallax ────────────────────────────────────────────────────────── */
const initParallax = () => {
    if (reduced) return;
    document.querySelectorAll('[data-parallax]').forEach((element) => {
        const amount = (Number(element.dataset.parallax) || 0.1) * 100;
        gsap.fromTo(element, { yPercent: -amount / 2 }, {
            yPercent: amount / 2, ease: 'none',
            scrollTrigger: { trigger: element.parentElement, start: 'top bottom', end: 'bottom top', scrub: true },
        });
    });
};

/* ── Magnetic buttons ────────────────────────────────────────────────── */
const initMagnetic = () => {
    if (reduced || !hasFinePointer()) return;
    document.querySelectorAll('[data-magnetic]').forEach((element) => {
        element.addEventListener('pointermove', (event) => {
            const rect = element.getBoundingClientRect();
            element.style.translate = `${((event.clientX - rect.left - rect.width / 2) * 0.25).toFixed(1)}px ${((event.clientY - rect.top - rect.height / 2) * 0.35).toFixed(1)}px`;
        });
        element.addEventListener('pointerleave', () => { element.style.translate = '0 0'; });
    });
};

/* ── Lists that reveal an image under the pointer ────────────────────── */
const initRevealLists = () => {
    if (!hasFinePointer()) return;
    document.querySelectorAll('[data-reveal-list]').forEach((list) => {
        const preview = document.createElement('div');
        preview.className = 'reveal-preview';
        preview.setAttribute('aria-hidden', 'true');
        const image = document.createElement('img');
        image.alt = '';
        preview.appendChild(image);
        document.body.appendChild(preview);
        const moveX = gsap.quickTo(preview, 'left', { duration: 0.6, ease: 'power3.out' });
        const moveY = gsap.quickTo(preview, 'top', { duration: 0.6, ease: 'power3.out' });

        list.querySelectorAll('[data-preview-src]').forEach((row) => {
            row.addEventListener('pointerenter', () => {
                image.src = row.dataset.previewSrc;
                preview.classList.add('is-visible');
            });
            row.addEventListener('pointerleave', () => preview.classList.remove('is-visible'));
            row.addEventListener('pointermove', (event) => { moveX(event.clientX + 40); moveY(event.clientY); });
        });
    });
};

export const initGsapMotion = () => {
    initSmoothScroll();
    initSplitHeadlines();
    initReveals();
    initParallax();
    initMagnetic();
    initRevealLists();

    const refresh = () => ScrollTrigger.refresh();
    window.addEventListener('load', refresh, { once: true });
    document.fonts?.ready.then(refresh);
};
