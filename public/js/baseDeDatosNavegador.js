let bd;

function iniciarBaseDatos(){

    //Abre la bd, si no existe la crea
    let solicitud = indexedDB.open("datosAsistencia");
    solicitud.addEventListener("error", mostrarError);
    solicitud.addEventListener("success", comenzar);
    solicitud.addEventListener("upgradeneeded", crearAlmacen); // Se dispara cuando se quiere abrir una base de datos no existente
}


if (typeof window !== "undefined") {
  window.addEventListener("load", iniciarBaseDatos);
}
function mostrarError(event){
    alert("Tenemos un error: "+ event.code + "/" + event.message);
}

function comenzar(event){
    bd = event.target.result;
}

//Creacion de bd
function crearAlmacen(event){
    let baseDeDatos = event.target.result;
    if (!baseDeDatos.objectStoreNames.contains("Asistencia")) {
        baseDeDatos.createObjectStore("Asistencia", { keyPath: "id", autoIncrement: true });
    }
    if (!baseDeDatos.objectStoreNames.contains("erroresDeAsistencia")) {
        baseDeDatos.createObjectStore("erroresDeAsistencia", { keyPath: "id", autoIncrement: true });
    }
}

export async function guardarAsistenciaOffline(data) {
    if (!bd) {
        console.warn("BD aún no lista, reintentá más tarde");
        return;
    }

    try {
        const transaccion = bd.transaction(["Asistencia"], "readwrite");
        const almacen = transaccion.objectStore("Asistencia");

        // Convertimos la operación de add a promesa para poder usar await
        await new Promise((resolve, reject) => {
            const request = almacen.add(data);
            request.onsuccess = () => {
                console.log("Asistencia guardada offline:", data);
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

export function eliminarDatosIndexed(id) {
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

export async function agregarBaseDeDatosErrores(idIndexed, asistencia){
    return new Promise((resolve, reject) => {
    const request = indexedDB.open('datosAsistencia', 1);

    request.onerror = () => reject('Error abriendo la DB');

    request.onsuccess = () => {
      const db = request.result;
      const tx = db.transaction('erroresDeAsistencia', 'readwrite');
      const store = tx.objectStore('erroresDeAsistencia');
      const resultado = store.add(asistencia);
            resultado.onsuccess = () => {
                console.log("error en asistencia guardado:", asistencia);
                eliminarDatosIndexed(idIndexed);
                resolve();
            };
            resultado.onerror = (event) => {
                console.error("Error guardando en IndexedDB:", event.target.error);
                reject(event.target.error);
            };

    };
  });
}

// Inicializa la base y elimina registros viejos automáticamente
export function inicializarBaseDeDatos() {
  const request = indexedDB.open('datosAsistencia', 1);

  // Se ejecuta solo si la base no existe o se actualiza
  request.onupgradeneeded = (event) => {
    crearAlmacen(event);
  };

  request.onsuccess = () => {
    console.log("Base de datos abierta correctamente");
    limpiarErroresViejos(); // Ejecutar limpieza al iniciar
  };

  request.onerror = () => {
    console.error("Error abriendo la base de datos");
  };
}

// Elimina registros de más de 10 días
export function limpiarErroresViejos() {
  const request = indexedDB.open('datosAsistencia', 1);

  request.onsuccess = () => {
    const db = request.result;
    const tx = db.transaction('erroresDeAsistencia', 'readwrite');
    const store = tx.objectStore('erroresDeAsistencia');
    const cursorRequest = store.openCursor();

    cursorRequest.onsuccess = (event) => {
      const cursor = event.target.result;
      if (cursor) {
        const registro = cursor.value;
        const fechaGuardado = new Date(registro.fecha_hora);
        const ahora = new Date();
        const diasPasados = (ahora - fechaGuardado) / (1000 * 60 * 60 * 24);

        //Eliminar todos los registros con más de 1 minuto
         if (diasPasados > 10) {
          store.delete(cursor.key);
          console.log(`Registro eliminado (${Math.round(diasPasados)} días):`, registro);
        }

        cursor.continue(); //muy importante para seguir recorriendo TODOS
      } else {
        console.log("Limpieza completa de registros viejos");
      }
    };

    cursorRequest.onerror = (event) => {
      console.error("Error al recorrer IndexedDB:", event.target.error);
    };
  };

  request.onerror = () => {
    console.error("Error al abrir la base de datos.");
  };
}



