let bd;

function iniciarBaseDatos(){
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

function crearAlmacen(event){
    let baseDeDatos = event.target.result;
    if (!baseDeDatos.objectStoreNames.contains("Asistencia")) {
        baseDeDatos.createObjectStore("Asistencia", { keyPath: "id", autoIncrement: true });
    }
    if (!baseDeDatos.objectStoreNames.contains("erroresDeAsistencia")) {
        baseDeDatos.createObjectStore("erroresDeAsistencia", { keyPath: "id", autoIncrement: true });
    }
}

// -----------------------------------------------------------
// Guarda asistencia offline en la tabla del Indexed para cuando vuelva el wifi tenga los datos a guardar
// -----------------------------------------------------------
// -----------------------------------------------------------
// agregarBaseDeDatosErrores(data)
// asistencia = json , todos los datos que se guarda del usuario al escanear el QR 
// -----------------------------------------------------------
// Abre la tabla que guarda los datos al escanear el QR para poder escribir nuevos registros
// Guarda los datos en la tabla 
// Registra la sincronización solo después de guardar los datos

export async function guardarAsistenciaOffline(data) {
    if (!bd) {
        console.warn("BD aún no lista, reintentá más tarde");
        return;
    }
    try {
        const transaccion = bd.transaction(["Asistencia"], "readwrite");
        const almacen = transaccion.objectStore("Asistencia");

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

        if ('serviceWorker' in navigator && 'SyncManager' in window) {
            const swReg = await navigator.serviceWorker.ready;
            await swReg.sync.register('sincronizar-asistencias');
            console.log('Sincronización registrada');
        }
        return true;
    } catch (error) {
        console.error("Error en guardarAsistenciaOffline:", error);
        throw error;
    }
}

// -----------------------------------------------------------
// Ante un error en el escaneo en modo offline(puesto incorrecto, más de 200 mts), 
// se pasa esos datos de la tabla Asistencia a erroresDeAsistencia para tener una copia al menos por 10 dias. 
// -----------------------------------------------------------
// -----------------------------------------------------------
// agregarBaseDeDatosErrores(idIndexed, asistencia)
// idIndexed = int , referencia al ID del elemento a eliminar en la tabla Asistencia
// asistencia = json , todos los datos que se guarda del usuario al escanear el QR 
// -----------------------------------------------------------
// Abre la base de datos.
// Abre la tabla que guarda los datos al escanear mal el QR para poder escribir nuevos registros
// Guarda los datos en la tabla y elimina de la tabla asistencia (asi no se encuentran los datos duplicados)

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

// -----------------------------------------------------------
// Ante un error en el escaneo en modo offline(puesto incorrecto, más de 200 mts), 
// se pasa esos datos de la tabla Asistencia a erroresDeAsistencia para tener una copia al menos por 10 dias. 
// -----------------------------------------------------------
// -----------------------------------------------------------
// agregarBaseDeDatosErrores(idIndexed, asistencia)
// idIndexed = int , referencia al ID del elemento a eliminar en la tabla Asistencia
// asistencia = json , todos los datos que se guarda del usuario al escanear el QR 
// -----------------------------------------------------------
// Abre la base de datos.
// Abre la tabla que guarda los datos al escanear mal el QR para poder escribir nuevos registros
// Guarda los datos en la tabla y elimina de la tabla asistencia (asi no se encuentran los datos duplicados)

export async function agregarBaseDeDatosErrores(idIndexed, asistencia) {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('datosAsistencia', 1);

    request.onerror = () => reject('Error abriendo la DB');

    request.onsuccess = () => {
      const db = request.result;
      const tx = db.transaction('erroresDeAsistencia', 'readwrite');
      const store = tx.objectStore('erroresDeAsistencia');
      const resultado = store.add(asistencia);
      resultado.onsuccess = () => {
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

// -----------------------------------------------------------
// Inicializa la base de datos para eliminar datos viejos (si existen).
// -----------------------------------------------------------
// -----------------------------------------------------------
// inicializarBaseDeDatos()
// -----------------------------------------------------------
// Inicializa la base de datos.
// En caso de no existir la base de datos, la crea y agrega las tablas declaradas en crearAlmacen
// Llama a la función limpiarErroresViejos()

export function inicializarBaseDeDatos() {
  const request = indexedDB.open('datosAsistencia', 1);

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

// -----------------------------------------------------------
// Eliminar datos erroneos con más de 10 dias
// -----------------------------------------------------------
// -----------------------------------------------------------
// limpiarErroresViejos()
// -----------------------------------------------------------
// Abre la base de datos.
// De los registros, toma el valor de la fecha cuando se escaneo el QR y se genera una variable con la fecha de hoy.
// Compara las fechas y obtiene la diferencia de dias que tienen
// Si es superior a 10, borra los datos sino continua con otro registro

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



