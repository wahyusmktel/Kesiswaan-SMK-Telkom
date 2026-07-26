import { createApp } from 'vue';
import LandingPage from './LandingPage.vue';
import './landing.css';

const element = document.getElementById('stella-vue-landing');

if (element) {
    const payload = window.__STELLA_LANDING__ || {};
    createApp(LandingPage, { payload }).mount(element);
}
