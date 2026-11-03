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
                'resources/js/pages/member-form.js',
                'resources/js/pages/client-form.js',
                'resources/js/pages/report-editor.js',
                'resources/js/pages/letters.js',
                'resources/js/pages/lks-profile.js',
                'resources/js/pages/registration-review.js',
                'resources/js/pages/partner-accounts.js',
                'resources/js/pages/correspondence.js',
                'resources/js/pages/landing-content.js',
                'resources/js/pages/account-settings.js',
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
