import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    experimental: {
        // Fonts and images referenced from CSS resolve relative to the stylesheet,
        // so the build works when the app is served from a sub-directory
        // (e.g. http://localhost/himalaya-store/public/) as well as from a domain root.
        renderBuiltUrl(filename, { hostType }) {
            return hostType === 'css' ? { relative: true } : undefined;
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
