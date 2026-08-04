import { createApp } from 'vue';
import ShowcaseLanding from './ShowcaseLanding.vue';
import '../bootstrap'; // Import bootstrap if necessary for axios, etc.
import '../../css/app.css'; // Assuming Tailwind is configured here

const element = document.getElementById('showcase-app');

if (element) {
    const payload = window.__SHOWCASE_PAYLOAD__ || {};
    createApp(ShowcaseLanding, { payload }).mount(element);
}
