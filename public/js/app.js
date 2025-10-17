if ('serviceWorker' in navigator && 'SyncManager' in window) {
  navigator.serviceWorker.register('/sw.js')
    .then(reg => console.log('✅ Service Worker registrado'))
    .catch(err => console.error('❌ Error al registrar SW:', err));
}