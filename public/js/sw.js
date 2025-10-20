/*self.addEventListener('install', event => {
    console.log('Service Worker instalado');
    self.skipWaiting();
});

self.addEventListener('activate', event => {
    console.log('Service Worker activado');
});

self.addEventListener('sync', event => {
    if (event.tag === 'sync-asistencias') {
        event.waitUntil(sincronizarAsistencias());
    }
});

async function sincronizarAsistencias() {
  const asistencias = await recuperarDatos();
  for (const asistencia of asistencias) {
    try {
      await fetch("/cargarAsistencia", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': asistencia.csrfToken // viene del cliente
        },
        body: JSON.stringify(asistencia)
      });
      await eliminarDatosIndexed(asistencia.id);
      console.log('✅ Asistencia sincronizada:', asistencia);
    } catch (err) {
      console.error('❌ Error al enviar asistencia:', err);
    }
  }
}*/