//Sincronizacion de Service Worker para poder escuchar cuando vuelva el internet para guardar las asistencias
//que quedaron en la base de datos del navegador
import { inicializarBaseDeDatos } from './js/baseDeDatosNavegador.js';


if (!window._indexedDBInicializada) {
  window._indexedDBInicializada = true;
  inicializarBaseDeDatos();
}

if ('serviceWorker' in navigator && !window._swRegistrado) {
    window._swRegistrado = true;
    navigator.serviceWorker.register('/sw.js', { type: 'module' })
        .then(async reg => {
            // Esperar a que esté listo (activo y controlando la página)
            const swReg = await navigator.serviceWorker.ready;
            console.log('Service Worker listo.');

            if ('SyncManager' in window) {
                await swReg.sync.register('sincronizar-asistencias');
                console.log('Sincronización registrada correctamente');
            } else {
                console.warn('SyncManager no soportado en este navegador (ej. Safari/iOS) - se usa el respaldo de abajo');
            }

            // Respaldo para navegadores sin SyncManager (principalmente iOS):
            // le pedimos al Service Worker que sincronice apenas se abre la
            // app con conexión, y de nuevo si la conexión vuelve mientras la
            // app sigue abierta. No reemplaza a SyncManager en los
            // navegadores que sí lo soportan, se suma como red adicional.
            const pedirSincronizacion = () => {
                if (navigator.onLine && swReg.active) {
                    swReg.active.postMessage('sincronizar-asistencias');
                }
            };
            pedirSincronizacion();
            window.addEventListener('online', pedirSincronizacion);
        })
        .catch(err => console.error('Error al registrar SW o Sync:', err));
}

