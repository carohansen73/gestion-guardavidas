const CACHE_NAME = "guardavidas-service-offline1";

const ASSETS = [
    "/",
    "/home",
    "/activeCamera", //o la vista exacta donde esta el comienzo del escaneo
    "/loginIdUser", //este nose si no anda eliminar =(
    "offline.html",
    /**scripts que se ejecutan en cada vista guardada :  */

    "/js/baseDeDatosNavegador.js",
    "/js/loginIdUserOffline.js",
    "/js/qrAsistencia.js",
    "/js/script.js",
    "/js/bootstrap.js",
    "/js/app.js",

    /**css que se utilizan  */
    "/css/app.css",
    "/css/qr.css",
    "/css/style.css", //este tambien, si falla , eliminar referencia

    /**libreria externa */
    "/vendor/html5-qrcode.min.js",
];

self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS))
    );
});

self.addEventListener("fetch", (event) => {
    event.respondWith(
        fetch(event.request)
        .then((res) => {
            const clone = res.clone();
            caches
                .open(CACHE_NAME)
                .then((c) => c.put(event.request, clone));
            return res;
        })
        .catch(() => caches.match(event.request))
    );
});