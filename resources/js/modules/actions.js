// Declarative replacements for inline event-handler attributes, which the
// Content-Security-Policy no longer allows:
//   data-confirm="Question?"  on a <form> (submit) or a button/link (click)
//   data-auto-submit          on a <select>/<input> to submit its form on change
//   data-dismiss              on a button that removes its dismissible container
//   data-print                on a button that opens the print dialog

const confirmed = (element) => {
    const message = element?.dataset.confirm;
    return !message || window.confirm(message);
};

document.addEventListener('submit', (event) => {
    const form = event.target;
    if (form instanceof HTMLFormElement && !confirmed(form)) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }
}, true);

document.addEventListener('click', (event) => {
    const target = event.target instanceof Element ? event.target : null;
    if (!target) return;

    const confirmTrigger = target.closest('button[data-confirm], a[data-confirm], input[data-confirm]');
    if (confirmTrigger && !confirmed(confirmTrigger)) {
        event.preventDefault();
        event.stopImmediatePropagation();
        return;
    }

    const dismiss = target.closest('[data-dismiss]');
    if (dismiss) {
        const container = dismiss.closest('[data-dismissible]') ?? dismiss.parentElement;
        container?.classList.add('is-dismissing');
        window.setTimeout(() => container?.remove(), 200);
        return;
    }

    if (target.closest('[data-print]')) window.print();
}, true);

document.addEventListener('change', (event) => {
    const field = event.target;
    if (field instanceof Element && field.matches('[data-auto-submit]')) {
        field.form?.requestSubmit();
    }
});
