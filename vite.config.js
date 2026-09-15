import laravel from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
    server: {
        // Sin esto, el navegador bloquea por CORS los scripts que pide a
        // localhost:5173/[::1]:5173 cuando el sitio se ve desde un dominio
        // local distinto (guardavidas.local vía Laragon). true = permitir
        // cualquier origen — solo afecta al servidor de dev, nunca a producción
        // (ahí no se usa Vite dev server, solo el build ya compilado).
        cors: true,
    },
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
                'resources/js/darkMode.js',
                "public/js/qrAsistencia.js"
            ],
            refresh: true,
        }),
    ],
});
