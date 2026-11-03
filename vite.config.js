import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/entries/admin.css',
                'resources/css/entries/lks.css',
                'resources/css/entries/auth.css',
                'resources/js/dashboard/main.js',
                'resources/js/auth/login.js',
                'resources/js/auth/change-password.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
