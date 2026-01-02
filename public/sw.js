import { agregarBaseDeDatosErrores, eliminarDatosIndexed } from "./js/baseDeDatosNavegador.js";


const CACHE_NAME = "static-v3";

const URLS_TO_CACHE = [
   "/",
  "/manifest.json",
  "/js/pantallaOffline.js",
  "/js/qrAsistencia.js"
];


self.addEventListener("install", event => {
  console.log('SW: install');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(URLS_TO_CACHE))
      .then(() => self.skipWaiting())
      .catch(err => {
        console.error('SW install failed:', err);
      })
  );
});

self.addEventListener('activate', event => {
    console.log("Activado SW");
    event.waitUntil(self.clients.claim()); // la página queda controlada de inmediato
});

// FETCH: responde aunque no haya internet
self.addEventListener('fetch', event => {
  const { request } = event;

  if (request.method !== 'GET') return;

  if (request.url.includes('/api/')) return;

  if (request.url.includes('/ping')) {
    event.respondWith(fetch(request));
    return;
  }

  if (
    request.mode === 'navigate' ||
    request.url.includes('/login') ||
    request.url.includes('/home')
  ) {
    event.respondWith(fetch(request));
    return;
  }

  // ✅ solo assets estáticos
  event.respondWith(
    caches.match(request).then(cached => {
      if (cached) return cached;

      return fetch(request).then(response => {
        return caches.open(CACHE_NAME).then(cache => {
          cache.put(request, response.clone());
          return response;
        });
      });
    })
  );
});


self.addEventListener('sync', event => {
  console.log('Evento de sincronización recibido:', event.tag);
  if (event.tag === 'sincronizar-asistencias') {
    event.waitUntil(
      sincronizarAsistencias(), 
    );
  }
})



async function sincronizarAsistencias() {
  const asistencias = await recuperarDatos();
  for (let asistencia of asistencias) {
    cargarAsistenciaReconexion(asistencia);
  }
}

// -----------------------------------------------------------
// cargarAsistenciaReconexion (asistencia)
// -----------------------------------------------------------
// -----------------------------------------------------------
// Registra la asistencia cuando se reconecta al internet
//    - desencripta QR
//    - verifica distancia (≤ 200m)
//    - valida que el QR pertenezca al puesto correcto
//    - guarda asistencia en base de datos

async function cargarAsistenciaReconexion(asistencia) {
    try {
            let data = await desencriptarQR(asistencia.encrypted, asistencia.token_bearer);
            if (!data){
                throw new Error('Ocurrió un error inesperado al registrar la asistencia. Por favor, intentá nuevamente.');
            }
            let idPlaya = data.playa_id;
            let idPuesto = data.puesto_id;
            let latitudPuesto = data.puesto_lat;
            let longitudPuesto = data.puesto_lng;

            let datos = {
              idPlaya: idPlaya,
              userLat: asistencia.lat,
              userLng: asistencia.lng,
              userPrecision: asistencia.precision,
              user_id: asistencia.user_id,
              idPuesto: idPuesto,
              fecha_hora: asistencia.fecha_hora,
            };

            let resultado = await calcularDistancia(asistencia.lat, asistencia.lng, latitudPuesto, longitudPuesto);
            if (resultado > 200){
                agregarBaseDeDatosErrores(asistencia.id, datos);
                throw new Error(`No se pudo registrar la asistencia: el QR fue escaneado a más de 200 metros de distancia el día: ${asistencia.fecha_hora}.`);
            }

            let puestoCorrecto = await perteneceQRAlPuesto(asistencia.user_id, idPuesto, asistencia.token_bearer);
            if (!puestoCorrecto || puestoCorrecto.success == false){
                agregarBaseDeDatosErrores(asistencia.id, datos);
                throw new Error(`No se pudo registrar la asistencia: el QR fue escaneado en el puesto incorrecto el día: ${asistencia.fecha_hora}.`);
            }

            cargarDatos(datos, asistencia.id, asistencia.token_bearer);

    } catch (err) {
        console.error(err.message);
        await notificarClientes('error', err);
    }
}


async function desencriptarQR(valorQR, token_bearer) {
    try{
        const res = await fetch("api/desencriptar-qr", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                'Authorization': `Bearer ${token_bearer}`
            },
            body: JSON.stringify({ encrypted: valorQR }),
        });
        const data = await res.json();
        return data.data;
    }
    catch(err){
        return undefined;
    }
    
}

// -----------------------------------------------------------
// recuperarDatos ()
// -----------------------------------------------------------
// -----------------------------------------------------------
// Abre la base de datos IndexedDB llamada "datosAsistencia" en modo lectura,
// accede al almacén de objetos "Asistencia" y recupera todos los registros guardados.
// Devuelve una Promesa que se resuelve con los datos o se rechaza si ocurre un error.
// -----------------------------------------------------------
async function recuperarDatos() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('datosAsistencia', 1);

    request.onerror = () => reject('Error abriendo la DB');
    request.onsuccess = () => {
      const db = request.result;
      const tx = db.transaction('Asistencia', 'readonly'); 
      const store = tx.objectStore('Asistencia');
      const getAllRequest = store.getAll();

      getAllRequest.onsuccess = () => resolve(getAllRequest.result);
      getAllRequest.onerror = () => reject('Error leyendo datos');
    };
  });
}

async function perteneceQRAlPuesto(user_id, idPuesto, token_bearer) {
    try {
        const res = await fetch("/api/verPuesto", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                'Authorization': `Bearer ${token_bearer}`
            },
            body: JSON.stringify({ user_id: user_id, puesto_id: idPuesto }),
        });

        const data = await res.json();
        return data;
    } catch (error) {
        await notificarClientes('error', `Ocurrió un error inesperado al registrar la asistencia. Por favor, intentá nuevamente.`);
        return null;
    }
}

async function calcularDistancia(lat1, lon1, lat2, lon2) {
    const R = 6371e3; // radio de la tierra en metros
    const toRad = (x) => (x * Math.PI) / 180;
    const φ1 = toRad(lat1);
    const φ2 = toRad(lat2);
    const Δφ = toRad(lat2 - lat1);
    const Δλ = toRad(lon2 - lon1);
    //calculo de longitud y latitud y si concuerda con los metros de distancia permitidos
    const a =
        Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
        Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c; // en metros
}


// -----------------------------------------------------------
// cargarDatos(datos)
// -----------------------------------------------------------
// Envía los datos de asistencia verificados al backend mediante un POST a la API.
// Si el registro es exitoso, elimina los datos correspondientes de IndexedDB.
// Notifica al usuario el resultado mediante mensajes enviados a los clientes.
// En caso de error, muestra una notificación de fallo y mantiene los datos en IndexedDB.
// -----------------------------------------------------------

async function cargarDatos(datos, idIndexed, token_bearer) {
    try {
        let response = await fetch("/api/cargarAsistencia", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                'Authorization': `Bearer ${token_bearer}`
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                playa_id: datos.idPlaya,
                lat: datos.userLat,
                lng: datos.userLng,
                precision: datos.userPrecision,
                user_id: datos.user_id,
                puesto_id: datos.idPuesto,
                fecha_hora: datos.fecha_hora,
            }),
        });

        if (!response.ok) {
        await notificarClientes('error', `Ocurrió un error inesperado al registrar la asistencia. Por favor, intentá nuevamente.`);
            return undefined;
        }

        let res = await response.json(); 
        if (res.success) {
           eliminarDatosIndexed(idIndexed);
           await notificarClientes('success', `Asistencia sincronizada correctamente con fecha: ${datos.fecha_hora}`);
        } else {
            await notificarClientes('error', `Ocurrió un error inesperado al registrar la asistencia. Por favor, intentá nuevamente.`);
        }
    } catch (err) {
        console.log("Error catch: ",err);
        await notificarClientes('error', `Ocurrió un error inesperado al registrar la asistencia. Por favor, intentá nuevamente.`);
    }
}


// -----------------------------------------------------------
// notificarClientes(status, message)
// -----------------------------------------------------------
// Envía un mensaje a todas las ventanas o pestañas controladas por el service worker.
// Permite notificar al usuario sobre el estado de una operación (éxito, error, etc.)
// -----------------------------------------------------------

async function notificarClientes(status, message){
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientes => {
    for (const cliente of clientes) {
      cliente.postMessage({ status, message });
    }
  });
}

