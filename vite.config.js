import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/reporting.css',
                'resources/js/reporting.js',
                'resources/css/style.css',
                'resources/js/utils.js'
                
            ],
            refresh: true,
        }),
    ],
});
