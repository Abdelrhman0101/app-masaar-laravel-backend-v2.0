// vite.config.js (في جذر Laravel)

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import path from 'path'; // قم بإضافة هذا السطر

export default defineConfig({
    plugins: [
        laravel({
            input: ['admin-panel/src/main.jsx'],
            refresh: true,
        }),
        react(),
    ],
    // أضف هذا الجزء بالكامل
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'admin-panel/src'),
        },
    },
});