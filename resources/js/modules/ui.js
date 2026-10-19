// Shared UI helpers: motion preference, toasts and broken-image fallbacks.

export const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;
export const hasFinePointer = () => window.matchMedia('(hover: hover) and (pointer: fine)').matches;

const region = () => document.querySelector('[data-toast-region]');

const scheduleDismiss = (toast) => {
    const dismiss = () => {
        toast.classList.add('is-dismissing');
        window.setTimeout(() => toast.remove(), 300);
    };
    const progress = toast.querySelector('.toast-progress');
    // The progress bar's animation is the timer, so hovering (which pauses the
    // animation) also pauses dismissal. Reduced motion collapses animations, so
    // fall back to a plain timeout there.
    if (progress && !prefersReducedMotion()) {
        progress.addEventListener('animationend', dismiss, { once: true });
    } else {
        window.setTimeout(dismiss, 6000);
    }
};

/**
 * @param {string} message
 * @param {{ type?: 'success'|'error', action?: { label: string, href?: string, onClick?: () => void } }} options
 */
export const toast = (message, { type = 'success', action } = {}) => {
    const container = region();
    if (!container) return;

    const item = document.createElement('div');
    item.className = `toast${type === 'error' ? ' is-error' : ''}`;
    item.setAttribute('role', type === 'error' ? 'alert' : 'status');
    item.dataset.dismissible = '';

    const icon = document.createElement('span');
    icon.className = 'toast-icon';
    icon.setAttribute('aria-hidden', 'true');
    icon.textContent = type === 'error' ? '!' : '✓';

    const body = document.createElement('div');
    body.className = 'flex-1 pt-1';
    const text = document.createElement('p');
    text.className = 'font-semibold';
    text.textContent = message;
    body.appendChild(text);

    if (action) {
        const link = document.createElement(action.href ? 'a' : 'button');
        if (action.href) link.href = action.href;
        else link.type = 'button';
        link.className = 'mt-1 inline-block text-xs';
        link.textContent = action.label;
        if (action.onClick) link.addEventListener('click', action.onClick);
        body.appendChild(link);
    }

    const close = document.createElement('button');
    close.type = 'button';
    close.className = '-mr-1 grid h-7 w-7 place-items-center rounded-full text-white/70 hover:bg-white/10 hover:text-white';
    close.setAttribute('aria-label', 'Dismiss message');
    close.dataset.dismiss = '';
    close.textContent = '×';

    const progress = document.createElement('span');
    progress.className = 'toast-progress';
    progress.setAttribute('aria-hidden', 'true');

    item.append(icon, body, close, progress);
    container.appendChild(item);
    while (container.children.length > 3) container.firstElementChild?.remove();
    scheduleDismiss(item);
};

export const initToasts = () => {
    document.querySelectorAll('[data-toast-region] [data-autodismiss]').forEach(scheduleDismiss);
};

export const initImageFallbacks = () => {
    const fallback = document.body.dataset.imageFallbackSrc;
    if (!fallback) return;
    const attach = (img) => {
        const replace = () => {
            img.removeAttribute('data-image-fallback');
            img.src = fallback;
        };
        img.addEventListener('error', replace, { once: true });
        if (img.complete && img.naturalWidth === 0) replace();
    };
    document.querySelectorAll('img[data-image-fallback]').forEach(attach);
};

/** Run `callback` at most once per animation frame. */
export const rafThrottle = (callback) => {
    let queued = false;
    return (...args) => {
        if (queued) return;
        queued = true;
        window.requestAnimationFrame(() => {
            queued = false;
            callback(...args);
        });
    };
};
