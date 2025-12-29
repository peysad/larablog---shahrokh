import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/sidebar.css',
                'resources/css/post.css',
                'resources/css/dashboard.css',
                'resources/css/author.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});