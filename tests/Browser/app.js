import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

Alpine.plugin(focus);

window.Alpine = Alpine;
Alpine.start();

// Readiness marker that browser tests wait for before interacting with the page.
document.documentElement.dataset.alpine = 'ready';
