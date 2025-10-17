importScripts('/js/baseDatosLocal.js'); 

export async function registrarAsistenciaOffline(datos) {
  // Guardar temporalmente
  await guardarAsistenciaOffline(datos);

  // Registrar sincronización de fondo
  const reg = await navigator.serviceWorker.ready;
  await reg.sync.register('sincronizar-asistencias');
  console.log('🕒 Asistencia guardada para enviar cuando vuelva la conexión');
}

self.addEventListener('sync', event => {
  if (event.tag === 'sincronizar-asistencias') {
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
}