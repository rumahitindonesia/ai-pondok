import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                compilerOptions: {
                    // treat all tags with 'oj-' as custom elements
                    isCustomElement: (tag) => tag.startsWith('oj-')
                }
            }
        }),
    ],
    resolve: {
        alias: {
            'ojs': path.resolve(__dirname, 'node_modules/@oracle/oraclejet/dist/js/libs/oj/debug_esm'),
            'ojL10n': path.resolve(__dirname, 'node_modules/@oracle/oraclejet/dist/js/libs/oj/debug_esm/ojL10n'),
            'ojtranslations': path.resolve(__dirname, 'node_modules/@oracle/oraclejet/dist/js/libs/oj/resources'),
        },
    },
});
