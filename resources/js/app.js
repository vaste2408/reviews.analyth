import './bootstrap';
import '../css/app.css';
//import '../scss/styles.scss' //if u want BS
import '@quasar/extras/material-icons/material-icons.css';
import 'quasar/src/css/index.sass';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import { Quasar } from 'quasar';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(Quasar, {plugins: {}})
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
