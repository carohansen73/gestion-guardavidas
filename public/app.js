//Sincronizacion de Service Worker para poder escuchar cuando vuelva el internet para guardar las asistencias
//que quedaron en la base de datos del navegador
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js', { type: 'module' })
        .then(reg => {
            console.log('✅ Service Worker registrado con éxito:', reg);
            if ('SyncManager' in window) {
                return navigator.serviceWorker.ready;
            } else {
                console.warn('SyncManager no soportado en este navegador');
            }
        })
        .then(swReg => {
            if (swReg) {
                swReg.sync.register('sync-asistencias');
                console.log('🔄 Sincronización registrada');
            }
        })
        .catch(err => console.error('❌ Error al registrar SW o Sync:', err));
}