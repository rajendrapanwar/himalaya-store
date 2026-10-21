// Slide-out drawers (navigation and cart) and the full-width search overlay,
// including instant product suggestions.

const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select, textarea, [tabindex]:not([tabindex="-1"])';
const backdrop = () => document.querySelector('[data-overlay-backdrop]');
let active = null;
let returnFocus = null;

const setTriggersExpanded = (id, expanded) => {
    document.querySelectorAll(`[data-drawer-open="${id}"]`).forEach((trigger) => trigger.setAttribute('aria-expanded', String(expanded)));
};

export const closeOverlay = () => {
    if (!active) return;
    const panel = active;
    panel.classList.remove('is-open');
    if (panel.id) setTriggersExpanded(panel.id, false);
    if (panel.matches('[data-search-panel]')) document.querySelector('[data-search-open]')?.setAttribute('aria-expanded', 'false');
    backdrop()?.classList.remove('is-open');
    document.documentElement.classList.remove('has-overlay');
    document.documentElement.style.overflow = '';
    document.body.classList.remove('menu-is-open');
    active = null;
    returnFocus?.focus({ preventScroll: true });
    returnFocus = null;
    document.dispatchEvent(new CustomEvent('overlay:close'));
};

export const openOverlay = (panel, trigger = null) => {
    if (!panel) return;
    if (active && active !== panel) closeOverlay();
    active = panel;
    returnFocus = trigger ?? document.activeElement;
    panel.classList.add('is-open');
    if (panel.id) setTriggersExpanded(panel.id, true);
    // The full-screen menu covers everything, so it needs no dimmed backdrop.
    if (panel.matches('[data-menu-overlay]')) document.body.classList.add('menu-is-open');
    else backdrop()?.classList.add('is-open');
    document.documentElement.classList.add('has-overlay');
    document.documentElement.style.overflow = 'hidden';
    document.dispatchEvent(new CustomEvent('overlay:open'));
    window.setTimeout(() => {
        const preferred = panel.querySelector('[data-autofocus], input[type="search"]') ?? panel.querySelector(FOCUSABLE);
        preferred?.focus({ preventScroll: true });
    }, 80);
};

export const openDrawer = (id, trigger) => openOverlay(document.getElementById(id), trigger);

const trapFocus = (event) => {
    if (!active || event.key !== 'Tab') return;
    const focusable = [...active.querySelectorAll(FOCUSABLE)].filter((element) => element.offsetParent !== null);
    if (!focusable.length) return;
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
    }
};

const initSuggestions = (panel) => {
    const input = panel.querySelector('input[type="search"]');
    const results = panel.querySelector('[data-search-results]');
    const endpoint = document.body.dataset.suggestionsUrl;
    if (!input || !results || !endpoint) return;

    const initialMarkup = results.innerHTML;
    let timer;
    let controller;

    const heading = (copy) => {
        const element = document.createElement('p');
        element.className = 'text-[11px] font-black uppercase tracking-[.18em] text-stone-500';
        element.textContent = copy;
        return element;
    };

    const renderSkeleton = () => {
        results.replaceChildren(heading('Searching…'));
        const list = document.createElement('div');
        list.className = 'mt-4 grid gap-3';
        for (let index = 0; index < 3; index++) {
            const row = document.createElement('div');
            row.className = 'flex items-center gap-4';
            row.innerHTML = '<span class="skeleton h-16 w-16"></span><span class="flex-1 space-y-2"><span class="skeleton block h-3 w-1/3"></span><span class="skeleton block h-4 w-2/3"></span></span>';
            list.appendChild(row);
        }
        results.appendChild(list);
    };

    const render = (data, term) => {
        results.replaceChildren();
        if (!data.products.length && !data.categories.length) {
            results.appendChild(heading(`No matches for “${term}”`));
            const hint = document.createElement('p');
            hint.className = 'mt-3 text-sm text-stone-500';
            hint.textContent = 'Try a product name, an ingredient or a region such as Munsiyari.';
            results.appendChild(hint);
            return;
        }

        if (data.categories.length) {
            results.appendChild(heading('Collections'));
            const chips = document.createElement('div');
            chips.className = 'mb-6 mt-3 flex flex-wrap gap-2';
            data.categories.forEach((category) => {
                const chip = document.createElement('a');
                chip.className = 'chip';
                chip.href = category.url;
                chip.textContent = category.name;
                chips.appendChild(chip);
            });
            results.appendChild(chips);
        }

        if (data.products.length) {
            results.appendChild(heading('Products'));
            const list = document.createElement('ul');
            list.className = 'mt-3 grid gap-1 sm:grid-cols-2';
            list.setAttribute('role', 'listbox');
            data.products.forEach((product, index) => {
                const item = document.createElement('li');
                item.className = 'stagger-item';
                item.style.animation = `rise-in .5s var(--ease-out-expo) ${index * 40}ms both`;
                const link = document.createElement('a');
                link.href = product.url;
                link.className = 'search-result';
                link.setAttribute('role', 'option');
                link.dataset.searchOption = '';

                const media = document.createElement('span');
                media.className = 'h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-parchment';
                if (product.image) {
                    const image = document.createElement('img');
                    image.src = product.image;
                    image.alt = '';
                    image.className = 'h-full w-full object-cover';
                    image.loading = 'lazy';
                    media.appendChild(image);
                }

                const copy = document.createElement('span');
                copy.className = 'min-w-0 flex-1';
                const meta = document.createElement('small');
                meta.className = 'block truncate text-[10px] font-black uppercase tracking-[.14em] text-brand-600';
                meta.textContent = [product.category, product.origin].filter(Boolean).join(' · ');
                const name = document.createElement('strong');
                name.className = 'block truncate font-display text-lg font-medium text-ink';
                name.textContent = product.name;
                copy.append(meta, name);

                const price = document.createElement('span');
                price.className = 'text-sm font-black text-ink';
                price.textContent = product.price;

                link.append(media, copy, price);
                item.appendChild(link);
                list.appendChild(item);
            });
            results.appendChild(list);
        }

        const all = document.createElement('a');
        all.href = data.all_url;
        all.className = 'button-secondary mt-6';
        all.textContent = `See all results for “${term}” →`;
        results.appendChild(all);
    };

    input.addEventListener('input', () => {
        const term = input.value.trim();
        window.clearTimeout(timer);
        controller?.abort();
        if (term.length < 2) {
            results.innerHTML = initialMarkup;
            return;
        }
        timer = window.setTimeout(async () => {
            controller = new AbortController();
            renderSkeleton();
            try {
                const response = await fetch(`${endpoint}?q=${encodeURIComponent(term)}`, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    signal: controller.signal,
                });
                if (!response.ok) throw new Error(String(response.status));
                render(await response.json(), term);
            } catch (error) {
                if (error.name === 'AbortError') return;
                results.replaceChildren(heading('Instant results are unavailable — press Enter to search.'));
            }
        }, 220);
    });

    // Arrow keys move between results; Enter on the input submits a full search.
    panel.addEventListener('keydown', (event) => {
        if (!['ArrowDown', 'ArrowUp'].includes(event.key)) return;
        const options = [...panel.querySelectorAll('[data-search-option]')];
        if (!options.length) return;
        event.preventDefault();
        const current = options.indexOf(document.activeElement);
        const next = event.key === 'ArrowDown'
            ? (current + 1) % options.length
            : current <= 0 ? -1 : current - 1;
        (next === -1 ? input : options[next]).focus();
    });
};

export const initOverlays = () => {
    document.addEventListener('click', (event) => {
        const target = event.target instanceof Element ? event.target : null;
        if (!target) return;

        const opener = target.closest('[data-drawer-open]');
        if (opener) {
            event.preventDefault();
            openDrawer(opener.dataset.drawerOpen, opener);
            return;
        }
        if (target.closest('[data-search-open]')) {
            event.preventDefault();
            const trigger = target.closest('[data-search-open]');
            trigger.setAttribute('aria-expanded', 'true');
            openOverlay(document.querySelector('[data-search-panel]'), trigger);
            return;
        }
        if (target.closest('[data-drawer-close], [data-search-close]') || target.matches('[data-overlay-backdrop]')) {
            closeOverlay();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && active) {
            closeOverlay();
            return;
        }
        trapFocus(event);
        // "/" or Ctrl/⌘+K opens search from anywhere outside a text field.
        const typing = event.target instanceof Element && event.target.closest('input, textarea, select, [contenteditable="true"]');
        const searchPanel = document.querySelector('[data-search-panel]');
        if (searchPanel && !active && !typing && (event.key === '/' || ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k'))) {
            event.preventDefault();
            openOverlay(searchPanel, document.querySelector('[data-search-open]'));
        }
    });

    const searchPanel = document.querySelector('[data-search-panel]');
    if (searchPanel) initSuggestions(searchPanel);

    // Full-screen menu: hovering a link swaps the preview image beside it.
    const menu = document.querySelector('[data-menu-overlay]');
    if (menu) {
        const previews = [...menu.querySelectorAll('[data-menu-preview] img')];
        menu.querySelectorAll('[data-menu-image]').forEach((link) => {
            const show = () => previews.forEach((image, index) => image.classList.toggle('is-active', index === Number(link.dataset.menuImage)));
            link.addEventListener('pointerenter', show);
            link.addEventListener('focus', show);
        });
    }
};
