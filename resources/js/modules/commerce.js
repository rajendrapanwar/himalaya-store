// Shopping interactions: in-place add to cart (with a fly-to-basket animation),
// wishlist toggles, delivery pincode checks, sharing and quantity steppers.
// Every form still works as a normal POST when JavaScript or a request fails.
import { openDrawer } from './overlays';
import { prefersReducedMotion, toast } from './ui';

const jsonHeaders = { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' };

const flyToCart = (source) => {
    const target = document.querySelector('[data-cart-trigger]');
    if (!source || !target || prefersReducedMotion()) return Promise.resolve();
    const from = source.getBoundingClientRect();
    const to = target.getBoundingClientRect();
    if (!from.width || !to.width) return Promise.resolve();

    const ghost = document.createElement('div');
    ghost.className = 'fly-ghost';
    Object.assign(ghost.style, { left: `${from.left}px`, top: `${from.top}px`, width: `${from.width}px`, height: `${from.height}px` });
    const image = source.cloneNode(true);
    image.removeAttribute('id');
    image.className = 'h-full w-full object-cover';
    ghost.appendChild(image);
    document.body.appendChild(ghost);

    const scale = Math.max(0.08, 28 / from.width);
    const dx = to.left + to.width / 2 - (from.left + from.width / 2);
    const dy = to.top + to.height / 2 - (from.top + from.height / 2);
    return new Promise((resolve) => {
        window.requestAnimationFrame(() => {
            ghost.style.transform = `translate(${dx}px, ${dy}px) scale(${scale})`;
            ghost.style.opacity = '.4';
            ghost.style.borderRadius = '999px';
        });
        window.setTimeout(() => { ghost.remove(); resolve(); }, 820);
    });
};

const updateCart = (data) => {
    document.querySelectorAll('[data-cart-count]').forEach((badge) => {
        badge.textContent = data.cart_count;
        badge.classList.toggle('hidden', !Number(data.cart_count));
        badge.classList.remove('is-bumping');
        void badge.offsetWidth;
        badge.classList.add('is-bumping');
    });
    document.querySelectorAll('[data-cart-drawer-count]').forEach((count) => { count.textContent = `(${data.cart_count})`; });
    const trigger = document.querySelector('[data-cart-trigger]');
    trigger?.setAttribute('aria-label', `Shopping cart, ${data.cart_count} items`);
    // The drawer markup is rendered (and escaped) by the server.
    const body = document.querySelector('[data-cart-drawer-body]');
    if (body && typeof data.drawer === 'string') body.innerHTML = data.drawer;
};

const initAddToCart = () => {
    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.matches('[data-add-to-cart]') || event.defaultPrevented) return;
        event.preventDefault();
        const button = form.querySelector('button[type="submit"]');
        if (button?.getAttribute('aria-busy') === 'true') return;
        button?.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: jsonHeaders });
            if (response.status === 419 || response.status >= 500) throw new Error('fallback');
            const data = await response.json();
            if (!response.ok || !data.added) {
                toast(data.message || 'This product could not be added.', { type: 'error' });
                return;
            }

            const card = form.closest('[data-product-card]');
            const source = card?.querySelector('[data-product-media] img') ?? document.getElementById('mainProductImage');
            await flyToCart(source);
            updateCart(data);

            if (button?.classList.contains('add-to-cart-chip')) {
                button.classList.add('is-added');
                window.setTimeout(() => button.classList.remove('is-added'), 1600);
            }
            if (form.hasAttribute('data-open-drawer')) {
                openDrawer('cartDrawer', document.querySelector('[data-cart-trigger]'));
            } else {
                toast(data.message, { action: { label: 'View basket →', onClick: () => openDrawer('cartDrawer', document.querySelector('[data-cart-trigger]')) } });
            }
        } catch {
            // Session expired, offline or server error: let the browser do a normal submit.
            form.removeAttribute('data-add-to-cart');
            form.submit();
        } finally {
            button?.removeAttribute('aria-busy');
        }
    });
};

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
    if (label) label.textContent = saved ? 'Saved to wishlist' : 'Save to wishlist';
    if (saved && button) {
        button.classList.remove('is-popping');
        void button.offsetWidth;
        button.classList.add('is-popping');
    }
};

const initWishlist = () => {
    document.querySelectorAll('[data-wishlist-form]').forEach((form) => form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const button = form.querySelector('[data-wishlist-button]');
        if (button?.disabled) return;
        if (button) {
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
        }

        try {
            const response = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: jsonHeaders });
            if (!response.ok) throw new Error(`Wishlist request failed with ${response.status}`);
            const result = await response.json();

            document.querySelectorAll(`[data-wishlist-form][data-product-id="${form.dataset.productId}"]`)
                .forEach((matchingForm) => setWishlistState(matchingForm, result.saved));

            const count = document.querySelector('[data-wishlist-count]');
            const countLabel = document.querySelector('[data-wishlist-count-label]');
            if (count) count.textContent = result.wishlist_count;
            if (countLabel) countLabel.textContent = Number(result.wishlist_count) === 1 ? 'item' : 'items';

            toast(result.message);
            if (!result.saved && form.dataset.removeCard === 'true') {
                const card = form.closest('.product-card');
                card?.classList.add('opacity-0', 'scale-95');
                window.setTimeout(() => card?.remove(), 220);
            }
        } catch {
            toast('Could not update your wishlist. Please try again.', { type: 'error' });
        } finally {
            if (button) {
                button.disabled = false;
                button.removeAttribute('aria-busy');
            }
        }
    }));
};

const initPincode = () => {
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
            const response = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: jsonHeaders });
            const data = await response.json();
            if (!response.ok) throw new Error(data.errors?.pincode?.[0] || data.message || 'Please enter a valid six-digit pincode.');

            resultBox.replaceChildren();
            resultBox.className = `mt-3 rounded-2xl border p-4 text-sm animate-[rise-in_.5s_ease_both] ${data.serviceable ? 'border-brand-200 bg-brand-50 text-brand-950' : 'border-marigold-200 bg-marigold-100 text-clay-900'}`;

            const heading = document.createElement('p');
            heading.className = 'flex items-center gap-2 font-black';
            const badge = document.createElement('span');
            badge.className = `grid h-6 w-6 place-items-center rounded-full text-xs text-white ${data.serviceable ? 'bg-brand-600' : 'bg-saffron'}`;
            badge.textContent = data.serviceable ? '✓' : 'i';
            heading.append(badge, document.createTextNode(data.message));
            resultBox.appendChild(heading);

            if (data.serviceable) {
                const details = document.createElement('div');
                details.className = 'mt-3 flex flex-wrap gap-2 text-xs font-bold';
                const shipping = Number(data.shipping_amount) === 0
                    ? 'Free delivery'
                    : `${new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR', maximumFractionDigits: 0 }).format(data.shipping_amount)} delivery`;
                [`Arrives in ${data.estimate}`, data.cod_available ? 'COD available' : 'Online payment only', shipping].forEach((copy) => {
                    const item = document.createElement('span');
                    item.className = 'rounded-full bg-white px-3 py-1.5 ring-1 ring-brand-100';
                    item.textContent = copy;
                    details.appendChild(item);
                });
                resultBox.appendChild(details);
            }
        } catch (error) {
            resultBox.replaceChildren();
            resultBox.className = 'mt-3 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-800';
            resultBox.textContent = error.message || 'Could not check this pincode. Please try again.';
        } finally {
            if (button) {
                button.disabled = false;
                button.textContent = originalLabel;
                button.removeAttribute('aria-busy');
            }
        }
    }));
};

const initShare = () => {
    document.addEventListener('click', async (event) => {
        const button = event.target instanceof Element ? event.target.closest('[data-share]') : null;
        if (!button) return;
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
                toast('Link copied to clipboard');
            }
        } catch (error) {
            if (error?.name !== 'AbortError') toast('Unable to share this link', { type: 'error' });
        }
    });
};

const initQuantitySteppers = () => {
    document.querySelectorAll('[data-qty-stepper]').forEach((stepper) => {
        const input = stepper.querySelector('input');
        const decrease = stepper.querySelector('[data-qty-dec]');
        const increase = stepper.querySelector('[data-qty-inc]');
        if (!input) return;
        const bounds = () => ({ min: Number(input.min || 1), max: Number(input.max || 999) });
        const sync = () => {
            const { min, max } = bounds();
            const value = Number(input.value) || min;
            if (decrease) decrease.disabled = value <= min;
            if (increase) increase.disabled = value >= max;
        };
        const change = (delta) => {
            const { min, max } = bounds();
            input.value = String(Math.min(max, Math.max(min, (Number(input.value) || min) + delta)));
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            sync();
        };
        decrease?.addEventListener('click', () => change(-1));
        increase?.addEventListener('click', () => change(1));
        input.addEventListener('input', sync);
        sync();
    });
};

export const initCommerce = () => {
    initAddToCart();
    initWishlist();
    initPincode();
    initShare();
    initQuantitySteppers();
};
