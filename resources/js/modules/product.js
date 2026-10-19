// Product page: thumbnail gallery, hover zoom, full-screen lightbox and the
// sticky "add to cart" bar that appears once the main buy box scrolls away.
import { hasFinePointer } from './ui';

export const initProductPage = () => {
    const mainImage = document.getElementById('mainProductImage');
    const thumbnails = [...document.querySelectorAll('[data-product-thumbnail]')];
    const sources = thumbnails.length ? thumbnails.map((thumb) => thumb.dataset.src) : (mainImage ? [mainImage.src] : []);
    let current = 0;

    const showImage = (index) => {
        if (!mainImage || !sources.length) return;
        current = (index + sources.length) % sources.length;
        mainImage.animate?.([{ opacity: 0.25, transform: 'scale(1.02)' }, { opacity: 1, transform: 'scale(1)' }], { duration: 450, easing: 'cubic-bezier(.16,1,.3,1)' });
        mainImage.src = sources[current];
        thumbnails.forEach((thumb, position) => thumb.setAttribute('aria-current', String(position === current)));
    };
    thumbnails.forEach((thumb, index) => thumb.addEventListener('click', () => showImage(index)));

    const zoom = document.querySelector('[data-zoom]');
    if (zoom && mainImage && hasFinePointer()) {
        zoom.addEventListener('pointerenter', () => zoom.classList.add('is-zooming'));
        zoom.addEventListener('pointerleave', () => zoom.classList.remove('is-zooming'));
        zoom.addEventListener('pointermove', (event) => {
            const rect = zoom.getBoundingClientRect();
            zoom.style.setProperty('--zoom-x', `${((event.clientX - rect.left) / rect.width) * 100}%`);
            zoom.style.setProperty('--zoom-y', `${((event.clientY - rect.top) / rect.height) * 100}%`);
        });
    }

    const lightbox = document.querySelector('[data-lightbox]');
    const lightboxImage = lightbox?.querySelector('img');
    const openLightbox = () => {
        if (!lightbox || !lightboxImage || typeof lightbox.showModal !== 'function' || !sources.length) return;
        lightboxImage.src = sources[current];
        lightboxImage.alt = mainImage?.alt ?? '';
        lightbox.showModal();
    };
    document.querySelectorAll('[data-lightbox-open]').forEach((trigger) => trigger.addEventListener('click', openLightbox));
    lightbox?.addEventListener('click', (event) => {
        const target = event.target instanceof Element ? event.target : null;
        if (!target) return;
        if (target.closest('[data-lightbox-next]')) { showImage(current + 1); lightboxImage.src = sources[current]; return; }
        if (target.closest('[data-lightbox-previous]')) { showImage(current - 1); lightboxImage.src = sources[current]; return; }
        if (target === lightbox || target.closest('[data-lightbox-close]')) lightbox.close();
    });
    lightbox?.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowRight') { showImage(current + 1); lightboxImage.src = sources[current]; }
        if (event.key === 'ArrowLeft') { showImage(current - 1); lightboxImage.src = sources[current]; }
    });

    const buyBox = document.querySelector('[data-buy-box]');
    const stickyBar = document.querySelector('[data-sticky-buy]');
    if (buyBox && stickyBar && 'IntersectionObserver' in window) {
        new IntersectionObserver(([entry]) => {
            const passed = !entry.isIntersecting && entry.boundingClientRect.top < 0;
            stickyBar.classList.toggle('is-visible', passed);
            stickyBar.inert = !passed;
        }).observe(buyBox);
        stickyBar.inert = true;
    }
};
