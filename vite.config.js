import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue'
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/auth/login.css', 'resources/js/auth/login.ts'],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@@': path.resolve(__dirname, './resources'), // deprecated
            'resources': path.resolve(__dirname, './resources'), // deprecated
            '@shadcn': path.resolve(__dirname, './resources/shadcn'),
        },
    },
    server: {
      port: 2001,
    }
});

// import { defineConfig } from 'vite'
// import laravel from 'laravel-vite-plugin'
// import tailwindcss from '@tailwindcss/vite'

// export default defineConfig({
//   plugins: [
//     laravel({
//       input: ['resources/css/app.css', 'resources/js/app.js'],
//       refresh: true,
//     }),
//     tailwindcss(),
//   ],
// })
