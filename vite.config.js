import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/authentication.css',
                'resources/js/app.js',
                'resources/js/landing/main.js',
            ],
            refresh: true,
        }),
    ],
});
