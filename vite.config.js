import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/perfilGuardavidas.css",
                "resources/js/app.js",
                "resources/css/style.css",
                "resources/css/qr.css",
                "resources/js/filterPuestoByPlaya.js",
                "resources/js/dashboard-charts.js",
                'resources/js/darkMode.js'
            ],
            refresh: true,
        }),
    ],
});
