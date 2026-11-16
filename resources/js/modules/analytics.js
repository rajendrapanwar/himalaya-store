// Thin wrapper over Google Analytics 4 and the Meta Pixel. Both are optional:
// with no IDs configured (or a visitor who opted out) every call does nothing.
// Event names follow GA4's e-commerce names; the Pixel gets its own equivalents.
const META_EVENTS = {
    view_item: 'ViewContent',
    add_to_cart: 'AddToCart',
    begin_checkout: 'InitiateCheckout',
    purchase: 'Purchase',
    generate_lead: 'Lead',
};

export const track = (name, params = {}) => {
    try {
        window.gtag?.('event', name, params);

        const metaName = META_EVENTS[name];
        if (!metaName || !window.fbq) return;
        const items = Array.isArray(params.items) ? params.items : [];
        const data = {};
        if (params.value !== undefined) Object.assign(data, { value: params.value, currency: params.currency });
        if (items.length) {
            Object.assign(data, {
                content_type: 'product',
                content_ids: items.map((item) => String(item.item_id)),
                contents: items.map((item) => ({ id: String(item.item_id), quantity: item.quantity ?? 1 })),
            });
        }
        if (params.transaction_id) data.order_id = params.transaction_id;
        window.fbq('track', metaName, data);
    } catch {
        // Analytics must never get in the way of shopping.
    }
};

// Sends the events the server queued for this page with <x-analytics-event>.
export const initAnalytics = () => {
    document.querySelectorAll('script[data-analytics-event]').forEach((node) => {
        let event;
        try {
            event = JSON.parse(node.textContent);
        } catch {
            return;
        }
        if (event.once) {
            // A purchase is counted once, even if the thank-you page is reloaded.
            const key = `pr:tracked:${event.once}`;
            try {
                if (window.localStorage.getItem(key)) return;
                window.localStorage.setItem(key, '1');
            } catch {
                // Storage blocked (private mode): count it rather than lose the sale.
            }
        }
        track(event.name, event.params ?? {});
    });
};
