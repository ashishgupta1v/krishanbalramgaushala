import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import { ZiggyVue } from 'ziggy-js';
import axios from 'axios';
import '../css/app.css';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ── Inertia App Setup ────────────────────────────────────────────────────────
createInertiaApp({
    title: (title) => title ? `${title} — GauSeva Connect` : 'GauSeva Connect — Krishan Balram Gaushala',
    resolve: async (name) => {
        const page = await resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        );
        return page;
    },
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#d46600',
        showSpinner: false,
    },
});

// ── SPA Page Transition ──────────────────────────────────────────────────────
// Fade-out on navigate start, fade-in after the new page mounts
router.on('before', () => {
    const app = document.getElementById('app');
    if (app) {
        app.style.transition = 'opacity 0.13s ease, transform 0.13s ease';
        app.style.opacity = '0';
        app.style.transform = 'translateY(5px)';
    }
});

router.on('navigate', () => {
    const app = document.getElementById('app');
    if (app) {
        requestAnimationFrame(() => {
            app.style.opacity = '1';
            app.style.transform = 'translateY(0)';
        });
    }
});

// ── Prefetch on hover (debounced) ────────────────────────────────────────────
let _prefetchTimer;
document.addEventListener('mouseover', (e) => {
    const el = e.target.closest('[data-prefetch]');
    if (!el || !el.dataset.prefetch) return;
    clearTimeout(_prefetchTimer);
    _prefetchTimer = setTimeout(() => {
        try { router.prefetch(el.dataset.prefetch); } catch {}
    }, 80);
}, { passive: true });

// ── Service Worker ───────────────────────────────────────────────────────────
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((reg) => console.log('SW registered:', reg.scope))
            .catch((err) => console.error('SW failed:', err));
    });
}
