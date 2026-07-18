import './bootstrap';

document.querySelectorAll('[data-carousel]').forEach((carousel) => {
    const section = carousel.closest('section');
    const previous = section?.querySelector('[data-carousel-previous]');
    const next = section?.querySelector('[data-carousel-next]');

    if (!previous || !next) return;

    const scrollAmount = () => {
        const firstItem = carousel.firstElementChild;
        return firstItem ? firstItem.getBoundingClientRect().width + 16 : carousel.clientWidth * 0.8;
    };

    const updateControls = () => {
        const end = carousel.scrollWidth - carousel.clientWidth;
        previous.disabled = carousel.scrollLeft <= 2;
        next.disabled = carousel.scrollLeft >= end - 2;
    };

    previous.addEventListener('click', () => carousel.scrollBy({ left: -scrollAmount(), behavior: 'smooth' }));
    next.addEventListener('click', () => carousel.scrollBy({ left: scrollAmount(), behavior: 'smooth' }));
    carousel.addEventListener('scroll', updateControls, { passive: true });
    window.addEventListener('resize', updateControls, { passive: true });

    let isDragging = false;
    let wasDragged = false;
    let startX = 0;
    let startScrollLeft = 0;

    carousel.addEventListener('pointerdown', (event) => {
        if (event.pointerType !== 'mouse' || event.button !== 0) return;

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
    };

    carousel.addEventListener('pointerup', finishDragging);
    carousel.addEventListener('pointercancel', finishDragging);
    carousel.addEventListener('lostpointercapture', () => {
        isDragging = false;
        carousel.classList.remove('is-dragging');
    });
    carousel.addEventListener('dragstart', (event) => event.preventDefault());
    carousel.addEventListener('click', (event) => {
        if (!wasDragged) return;
        event.preventDefault();
        event.stopPropagation();
        wasDragged = false;
    }, true);

    updateControls();
});

document.querySelectorAll('[data-hero-slider]').forEach((slider) => {
    const slides = [...slider.querySelectorAll('[data-hero-slide]')];
    const dots = [...slider.querySelectorAll('[data-hero-dot]')];
    const previous = slider.querySelector('[data-hero-previous]');
    const next = slider.querySelector('[data-hero-next]');
    const currentLabel = slider.querySelector('[data-hero-current]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let current = 0;
    let timer = null;
    let pointerStart = null;

    if (slides.length < 2) return;

    const show = (index) => {
        current = (index + slides.length) % slides.length;
        slides.forEach((slide, slideIndex) => {
            const active = slideIndex === current;
            slide.classList.toggle('opacity-100', active);
            slide.classList.toggle('opacity-0', !active);
            slide.classList.toggle('pointer-events-none', !active);
            slide.classList.toggle('z-10', active);
            slide.classList.toggle('z-0', !active);
            slide.setAttribute('aria-hidden', String(!active));
            slide.inert = !active;
        });
        dots.forEach((dot, dotIndex) => {
            const active = dotIndex === current;
            dot.classList.toggle('w-9', active);
            dot.classList.toggle('w-5', !active);
            dot.classList.toggle('bg-amber-400', active);
            dot.classList.toggle('bg-white/50', !active);
            dot.setAttribute('aria-selected', String(active));
        });
        if (currentLabel) currentLabel.textContent = String(current + 1).padStart(2, '0');
    };

    const stop = () => {
        if (timer) window.clearInterval(timer);
        timer = null;
    };
    const start = () => {
        stop();
        if (!reducedMotion && !document.hidden) timer = window.setInterval(() => show(current + 1), 6500);
    };

    previous?.addEventListener('click', () => { show(current - 1); start(); });
    next?.addEventListener('click', () => { show(current + 1); start(); });
    dots.forEach((dot, index) => dot.addEventListener('click', () => { show(index); start(); }));
    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', start);
    slider.addEventListener('focusin', stop);
    slider.addEventListener('focusout', () => { if (!slider.contains(document.activeElement)) start(); });
    slider.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') { event.preventDefault(); show(current - 1); }
        if (event.key === 'ArrowRight') { event.preventDefault(); show(current + 1); }
    });
    slider.addEventListener('pointerdown', (event) => { if (event.pointerType !== 'mouse') pointerStart = event.clientX; });
    slider.addEventListener('pointerup', (event) => {
        if (pointerStart === null || Math.abs(event.clientX - pointerStart) < 45) return;
        show(current + (event.clientX < pointerStart ? 1 : -1));
        pointerStart = null;
        start();
    });
    document.addEventListener('visibilitychange', () => document.hidden ? stop() : start());
    start();
});

let toastTimer;
const showInteractionToast = (message, isError = false) => {
    let toast = document.getElementById('interactionToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'interactionToast';
        toast.setAttribute('role', 'status');
        toast.setAttribute('aria-live', 'polite');
        toast.className = 'fixed bottom-24 left-1/2 z-[70] -translate-x-1/2 rounded-lg px-4 py-3 text-sm font-bold text-white shadow-xl transition sm:bottom-6';
        document.body.appendChild(toast);
    }

    toast.textContent = message;
    toast.classList.toggle('bg-red-700', isError);
    toast.classList.toggle('bg-brand-950', !isError);
    toast.classList.remove('hidden');
    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(() => toast.classList.add('hidden'), 2400);
};

document.querySelectorAll('[data-share]').forEach((button) => button.addEventListener('click', async () => {
    const shareData = {
        title: button.dataset.shareTitle || document.title,
        text: button.dataset.shareText || '',
        url: button.dataset.shareUrl || window.location.href,
    };
    const localFeedback = button.parentElement?.querySelector('[data-share-feedback]');

    try {
        if (navigator.share) {
            await navigator.share(shareData);
            return;
        }

        if (navigator.clipboard?.writeText) {
            await navigator.clipboard.writeText(shareData.url);
        } else {
            const field = document.createElement('textarea');
            field.value = shareData.url;
            field.style.position = 'fixed';
            field.style.opacity = '0';
            document.body.appendChild(field);
            field.select();
            document.execCommand('copy');
            field.remove();
        }

        if (localFeedback) {
            localFeedback.textContent = 'Link copied';
            localFeedback.classList.remove('hidden');
            window.setTimeout(() => localFeedback.classList.add('hidden'), 2200);
        } else {
            showInteractionToast('Link copied to clipboard');
        }
    } catch (error) {
        if (error?.name !== 'AbortError') showInteractionToast('Unable to share this link', true);
    }
}));

const setWishlistState = (form, saved) => {
    form.action = saved ? form.dataset.removeUrl : form.dataset.addUrl;
    let method = form.querySelector('input[name="_method"]');
    if (saved && !method) {
        method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        form.appendChild(method);
    }
    if (saved) method.value = 'DELETE';
    else method?.remove();

    const button = form.querySelector('[data-wishlist-button]');
    const icon = form.querySelector('[data-wishlist-icon]');
    const label = form.querySelector('[data-wishlist-label]');
    const productName = form.dataset.productName;
    button?.setAttribute('aria-pressed', String(saved));
    button?.setAttribute('aria-label', `${saved ? 'Remove' : 'Add'} ${productName} ${saved ? 'from' : 'to'} wishlist`);
    if (icon?.tagName.toLowerCase() === 'svg') icon.setAttribute('fill', saved ? 'currentColor' : 'none');
    else if (icon) icon.textContent = saved ? '♥' : '♡';
    if (label) label.textContent = saved ? 'Remove from wishlist' : 'Save to wishlist';
};

document.querySelectorAll('[data-wishlist-form]').forEach((form) => form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const button = form.querySelector('[data-wishlist-button]');
    if (button?.disabled) return;
    if (button) {
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
    }

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        if (!response.ok) throw new Error(`Wishlist request failed with ${response.status}`);
        const result = await response.json();

        document.querySelectorAll(`[data-wishlist-form][data-product-id="${form.dataset.productId}"]`)
            .forEach((matchingForm) => setWishlistState(matchingForm, result.saved));

        const count = document.querySelector('[data-wishlist-count]');
        const countLabel = document.querySelector('[data-wishlist-count-label]');
        if (count) count.textContent = result.wishlist_count;
        if (countLabel) countLabel.textContent = Number(result.wishlist_count) === 1 ? 'item' : 'items';

        showInteractionToast(result.message);
        if (!result.saved && form.dataset.removeCard === 'true') {
            const card = form.closest('.product-card');
            card?.classList.add('opacity-0', 'scale-95');
            window.setTimeout(() => card?.remove(), 180);
        }
    } catch (error) {
        showInteractionToast('Could not update your wishlist. Please try again.', true);
    } finally {
        if (button) {
            button.disabled = false;
            button.removeAttribute('aria-busy');
        }
    }
}));

document.querySelectorAll('[data-pincode-form]').forEach((form) => form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const button = form.querySelector('[data-pincode-button]');
    const resultBox = form.querySelector('[data-pincode-result]');
    const originalLabel = button?.textContent;

    if (button) {
        button.disabled = true;
        button.textContent = 'Checking…';
        button.setAttribute('aria-busy', 'true');
    }

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await response.json();
        if (!response.ok) throw new Error(data.errors?.pincode?.[0] || data.message || 'Please enter a valid six-digit pincode.');

        resultBox.replaceChildren();
        resultBox.className = `mt-3 rounded-lg border p-4 text-sm ${data.serviceable ? 'border-brand-200 bg-brand-50 text-brand-950' : 'border-amber-200 bg-amber-50 text-amber-950'}`;

        const heading = document.createElement('p');
        heading.className = 'font-black';
        heading.textContent = `${data.serviceable ? '✓' : 'i'} ${data.message}`;
        resultBox.appendChild(heading);

        if (data.serviceable) {
            const details = document.createElement('div');
            details.className = 'mt-2 grid gap-1 text-xs sm:grid-cols-3';
            const shipping = Number(data.shipping_amount) === 0
                ? 'Free delivery'
                : `${new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(data.shipping_amount)} delivery`;
            [
                `Estimated: ${data.estimate}`,
                data.cod_available ? 'COD available' : 'Online payment only',
                shipping,
            ].forEach((copy) => {
                const item = document.createElement('span');
                item.textContent = copy;
                details.appendChild(item);
            });
            resultBox.appendChild(details);
        }
    } catch (error) {
        resultBox.replaceChildren();
        resultBox.className = 'mt-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-800';
        resultBox.textContent = error.message || 'Could not check this pincode. Please try again.';
    } finally {
        if (button) {
            button.disabled = false;
            button.textContent = originalLabel;
            button.removeAttribute('aria-busy');
        }
    }
}));
