import './bootstrap';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import '../css/app.css';
import { setThemeOnLoad } from "./theme";

import Main from './Layouts/Main.vue';
import { Head, Link } from '@inertiajs/vue3'
import { ZiggyVue } from '../../vendor/tightenco/ziggy';


// Create Inertia app
createInertiaApp({
    title: (title) => `Pledge Environmental ${title}`, // Adjusted for clarity
    resolve: name => {
        // Dynamically import pages
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        let page = pages[`./Pages/${name}.vue`]

        // Use Main layout as default only if not already set
        if (!page.default.layout) {
          page.default.layout = page.default.layout || Main;
        }

        // Set layout if not defined
        page.default.layout = page.default.layout || Main;
          return page;
      },
    setup({ el, App, props, plugin }) {
      createApp({ render: () => h(App, props) })
        .use(plugin)
        .use(ZiggyVue)
        .component('Head', Head)
        .component('Link', Link)
        .mount(el)
    },

    // true shows progress bar and spinner
    progress: {
        color: 'red',
      includeCSS: true,
      showSpinner: true, // Show the spinner
    },
  })

  setThemeOnLoad()

