import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";
import vue from "@vitejs/plugin-vue";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/css/auth/login.css",
                "resources/js/auth/login/login.ts",
                // 'resources/views/upload/upload.blade.php',
                // 'resources/views/upload/app.js',
            ],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            "@@": path.resolve(__dirname, "./resources"), // deprecated
            resources: path.resolve(__dirname, "./resources"), // deprecated
            "@shadcn": path.resolve(__dirname, "./resources/shadcn"),
        },
    },
    server: {
        port: 2001,
        hmr: {
            host: "localhost",
        },
        fs: {
            // IZINKAN akses luar folder Laravel (misalnya symlink)
            allow: [
                "..", // boleh akses parent
                // D:\data_ferdi\application\S1000D\apps
                path.resolve(__dirname, "views"),
                // D:\data_ferdi\application
                path.resolve(__dirname, "../../lib/csdb_lib/dochub"),
                "D:/data_ferdi/application/S100D/lib/csdb_lib/dochub",
            ],
        },
    },
});
