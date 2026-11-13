// Admin entry point. The admin only needs the declarative data-* actions
// (confirm, auto-submit, dismiss, print), so it skips the storefront bundle:
// no GSAP, no Lenis smooth scrolling, no carousels.
import './modules/actions';

// data-filter-list="#listId" on a search input hides the list's
// [data-filter-item] children whose text does not contain the query.
document.addEventListener('input', (event) => {
    const input = event.target;
    if (!(input instanceof HTMLInputElement) || !input.dataset.filterList) return;
    const list = document.querySelector(input.dataset.filterList);
    if (!list) return;
    const query = input.value.trim().toLowerCase();
    let visible = 0;
    list.querySelectorAll('[data-filter-item]').forEach((item) => {
        const match = item.textContent.toLowerCase().includes(query);
        item.hidden = !match;
        if (match) visible += 1;
    });
    const empty = input.dataset.filterEmpty ? document.querySelector(input.dataset.filterEmpty) : null;
    if (empty) empty.hidden = visible > 0;
});

// A filter box lives inside the form it filters; Enter must not save that form.
document.addEventListener('keydown', (event) => {
    if (event.key === 'Enter' && event.target instanceof HTMLInputElement && event.target.dataset.filterList) {
        event.preventDefault();
    }
});
