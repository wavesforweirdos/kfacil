import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/scss/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                // Bootstrap 5 sigue usando @import y las funciones globales de Sass
                // (red(), green(), if()...), que Dart Sass ya marca como obsoletas. Hasta que
                // Bootstrap 6 las migre, se silencian solo esos avisos (y los de dependencias)
                // para que no tapen los de nuestro propio SCSS.
                quietDeps: true,
                silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'if-function'],
            },
        },
    },
});
