import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/filament/studio/theme.css',
                'resources/css/filament/admin/theme.css',
                'resources/css/treeselect.css',
                'resources/js/select-tree.js',
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});
