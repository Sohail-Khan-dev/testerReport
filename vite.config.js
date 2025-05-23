import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/datatables-custom.css',
                'resources/css/sidebar.css',
                'resources/css/navbar.css',
                'resources/css/footer.css',
                'resources/css/theme.css',
                
                'resources/js/app.js',
                'resources/css/reporting.css',
                'resources/js/reporting.js',
                'resources/css/style.css',
                'resources/js/utils.js',
                'resources/js/theme.js'
                
            ],
            refresh: true,
        }),
    ],
});
