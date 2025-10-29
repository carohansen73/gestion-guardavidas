
self.addEventListener('install', event => {
    console.log("instalando SW");
    self.skipWaiting(); // fuerza que se active inmediatamente
});

self.addEventListener('activate', event => {
    console.log("Activado SW");
    event.waitUntil(clients.claim()); // la página queda controlada de inmediato
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

            let resultado = await calcularDistancia(asistencia.lat, asistencia.lng, latitudPuesto, longitudPuesto);
            if (resultado > 200){
                agregarBaseDeDatosErrores(asistencia);
                throw new Error(`No se pudo registrar la asistencia: el QR fue escaneado a más de 200 metros de distancia el día: ${asistencia.fecha_hora}.`);
            }

            let puestoCorrecto = await perteneceQRAlPuesto(asistencia.user_id, idPuesto, asistencia.token_bearer);
            if (!puestoCorrecto || puestoCorrecto.success == false){
                agregarBaseDeDatosErrores(asistencia);
                throw new Error(`No se pudo registrar la asistencia: el QR fue escaneado en el puesto incorrecto el día: ${asistencia.fecha_hora}.`);
            }
            let datos = {
              idPlaya: idPlaya,
              userLat: asistencia.lat,
              userLng: asistencia.lng,
              userPrecision: asistencia.precision,
              user_id: asistencia.user_id,
              idPuesto: idPuesto,
              fecha_hora: asistencia.fecha_hora,
            };
            cargarDatos(datos, asistencia.id, asistencia.token_bearer);

    } catch (err) {
        await notificarClientes('error', err);
    }
}

async function desencriptarQR(valorQR, token_bearer) {
    try{
      const fullAuthHeader = `Bearer ${token_bearer}`;
    console.log("Cabecera de Autorización enviada:", fullAuthHeader);
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

async function recuperarDatos() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('datosAsistencia', 1);

    request.onerror = () => reject('Error abriendo la DB');
    request.onsuccess = () => {
      const db = request.result;
      const tx = db.transaction('Asistencia', 'readonly'); // tu store
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

//Guardamos la asistencia del guardavida en la base de datos
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
           await notificarClientes('success', 'Asistencia sincronizada correctamente!');
        } else {
            await notificarClientes('error', 'El servidor respondió con error');
        }
    } catch (err) {
        console.log("Error catch: ",err);
        await notificarClientes('error', `Ocurrió un error inesperado al registrar la asistencia. Por favor, intentá nuevamente.`);
    }
}

function eliminarDatosIndexed(id) {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('datosAsistencia', 1);

    request.onerror = () => reject('Error abriendo la DB');

    request.onsuccess = () => {
      const db = request.result;
      const tx = db.transaction('Asistencia', 'readwrite'); // readonly → readwrite
      const store = tx.objectStore('Asistencia');
      const deleteRequest = store.delete(id); // nueva variable para no chocar con "request"

      deleteRequest.onsuccess = () => resolve(`Registro ${id} eliminado`);
      deleteRequest.onerror = () => reject('Error eliminando el registro');
    };
  });
}

async function notificarClientes(status, message){
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientes => {
    for (const cliente of clientes) {
      cliente.postMessage({ status, message });
    }
  });
}

async function agregarBaseDeDatosErrores(asistencia){
    return new Promise((resolve, reject) => {
    const request = indexedDB.open('datosAsistencia', 1);

    request.onerror = () => reject('Error abriendo la DB');

    request.onsuccess = () => {
      const db = request.result;
      const tx = db.transaction('erroresDeAsistencia', 'readwrite'); // readonly → readwrite
      const store = tx.objectStore('erroresDeAsistencia');
      const resultado = almacen.add(asistencia);
            resultado.onsuccess = () => {
                console.log("error en asistencia guardado:", asistencia);
                resolve();
            };
            resultado.onerror = (event) => {
                console.error("Error guardando en IndexedDB:", event.target.error);
                reject(event.target.error);
            };

    };
  });
  try {
        const transaccion = bd.transaction(["erroresDeAsistencia"], "readwrite");
        const almacen = transaccion.objectStore("erroresDeAsistencia");

        // Convertimos la operación de add a promesa para poder usar await
        await new Promise((resolve, reject) => {
            const request = almacen.add(asistencia);
            request.onsuccess = () => {
                console.log("error en asistencia guardado:", asistencia);
                resolve();
            };
            request.onerror = (event) => {
                console.error("Error guardando en IndexedDB:", event.target.error);
                reject(event.target.error);
            };
        });

        // Registrar la sincronización solo después de guardar los datos
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            const swReg = await navigator.serviceWorker.ready; // Ver razon por la que no carga
            await swReg.sync.register('sincronizacion-asistencias');
            console.log('Sincronización registrada');
        }
        return true;
    } catch (error) {
        console.error("Error en guardarAsistenciaOffline:", error);
        throw error;
    }
}