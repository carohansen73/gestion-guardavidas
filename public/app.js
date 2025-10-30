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
                console.warn('SyncManager no soportado en este navegador');
            }
        })
        .catch(err => console.error('Error al registrar SW o Sync:', err));
}

