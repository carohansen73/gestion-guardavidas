let bd;

function abrirIndexedDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('AsistenciasDB', 1);
        request.onupgradeneeded = function(event) {
            const db = event.target.result;
            db.createObjectStore('asistencias', { keyPath: 'id', autoIncrement: true });
        };
        request.onsuccess = function(event) {
            resolve(event.target.result);
        };
        request.onerror = function(event) {
            reject(event.target.error);
        };
    });
}

function iniciarBaseDatos(){

    //Abre la bd, si no existe la crea
    let solicitud = indexedDB.open("datosAsistencia");
    solicitud.addEventListener("error", mostrarError);
    solicitud.addEventListener("success", comenzar);
    solicitud.addEventListener("upgradeneeded", crearAlmacen); // Se dispara cuando se quiere abrir una base de datos no existente
}

window.addEventListener("load", iniciarBaseDatos);

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
            await swReg.sync.register('sync-asistencias');
            console.log('Sincronización registrada');
        }
        return true;
    } catch (error) {
        console.error("Error en guardarAsistenciaOffline:", error);
        throw error;
    }
}


