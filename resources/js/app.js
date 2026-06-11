import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import { ZiggyVue } from 'ziggy-js';
import axios from 'axios';
import FloatingAudioControl from './Components/FloatingAudioControl.vue';
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

        const RootApp = {
            name: 'RootApp',
            render() {
                return h('div', { id: 'app-layout', style: { width: '100%', height: '100%' } }, [
                    h(App, props),
                    h(FloatingAudioControl)
                ]);
            }
        };

        createApp({ render: () => h(RootApp) })
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
// Page transition animations are handled natively via page rendering to avoid global opacity lockups.

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
