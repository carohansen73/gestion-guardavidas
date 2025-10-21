//Sincronizacion de Service Worker para poder escuchar cuando vuelva el internet para guardar las asistencias
//que se guardaron en la base de datos del navegador
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/js/sw.js')
        .then(reg => {
            console.log('Service Worker registrado');
            // Registrar sincronización en segundo plano
            if ('sync' in reg) {
                reg.sync.register('sync-asistencias');
            }
        })
        .catch(err => console.error('Error al registrar SW:', err));
}
