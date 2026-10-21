import './bootstrap';
import './modules/actions';
import { initCarousels } from './modules/carousel';
import { initCommerce } from './modules/commerce';
import { initGsapMotion } from './modules/gsap-motion';
import { initHeader } from './modules/header';
import { initHero } from './modules/hero';
import { initOverlays } from './modules/overlays';
import { initProductPage } from './modules/product';
import { initTabs } from './modules/tabs';
import { initImageFallbacks, initToasts } from './modules/ui';

// Each initialiser is independent; one failing must not take the others down.
[
    initImageFallbacks,
    initToasts,
    initHeader,
    initOverlays,
    initGsapMotion,
    initHero,
    initCarousels,
    initTabs,
    initCommerce,
    initProductPage,
].forEach((init) => {
    try {
        init();
    } catch (error) {
        console.error(`[storefront] ${init.name} failed`, error);
    }
});
