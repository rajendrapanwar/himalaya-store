// Accessible tabs (arrow keys, Home/End) with a sliding indicator.

export const initTabs = () => {
    document.querySelectorAll('[data-tabs]').forEach((list) => {
        const tabs = [...list.querySelectorAll('[role="tab"]')];
        const indicator = list.querySelector('.tab-indicator');
        const panelFor = (tab) => document.getElementById(tab.getAttribute('aria-controls'));

        const moveIndicator = (tab) => {
            if (!indicator || !tab) return;
            indicator.style.setProperty('--x', `${tab.offsetLeft}px`);
            indicator.style.setProperty('--w', `${tab.offsetWidth}px`);
        };

        const select = (tab, focus = false) => {
            tabs.forEach((item) => {
                const selected = item === tab;
                item.setAttribute('aria-selected', String(selected));
                item.tabIndex = selected ? 0 : -1;
                const panel = panelFor(item);
                if (!panel) return;
                panel.hidden = !selected;
                if (selected) {
                    [...panel.children].forEach((child, index) => child.style.setProperty('--i', String(index)));
                    panel.classList.remove('is-entering');
                    void panel.offsetWidth;
                    panel.classList.add('is-entering');
                }
            });
            moveIndicator(tab);
            if (focus) tab.focus();
        };

        tabs.forEach((tab, index) => {
            tab.addEventListener('click', () => select(tab));
            tab.addEventListener('keydown', (event) => {
                const keys = { ArrowRight: index + 1, ArrowLeft: index - 1, Home: 0, End: tabs.length - 1 };
                if (!(event.key in keys)) return;
                event.preventDefault();
                select(tabs[(keys[event.key] + tabs.length) % tabs.length], true);
            });
        });

        const selected = tabs.find((tab) => tab.getAttribute('aria-selected') === 'true') ?? tabs[0];
        moveIndicator(selected);
        window.addEventListener('resize', () => moveIndicator(tabs.find((tab) => tab.getAttribute('aria-selected') === 'true')), { passive: true });
        document.fonts?.ready.then(() => moveIndicator(tabs.find((tab) => tab.getAttribute('aria-selected') === 'true')));
    });
};
