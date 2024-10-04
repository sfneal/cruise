import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
export default defineConfig({
    server: {
        strictPort: true,
        https: false,
        port: 5173,
        host: '0.0.0.0',
        cors: true,
        origin: 'http://localhost:5173',
        hmr: {
            host: 'cruise-boilerplate.localhost',
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
