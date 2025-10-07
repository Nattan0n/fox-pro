import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // ให้ Vite รับ connection จากเครื่องอื่น
        port: 5173,      // port สำหรับ Vite
        hmr: {
            host: '10.41.10.1', // IP ของเครื่องคุณ
        },
    },
});
