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

export function guardarAsistenciaOffline(data){
    if (!bd) {
        console.warn("BD aún no lista, reintentá más tarde");
        return;
    }
    let transaccion = bd.transaction(["Asistencia"], "readwrite");
    let almacen = transaccion.objectStore("Asistencia");
    almacen.add(data);
    transaccion.oncomplete = () => {
        console.log("Asistencia guardada offline:", data);
    };
    transaccion.onerror = (event) => {
        console.error("Error guardando en IndexedDB:", event.target.error);
    };
}

function recuperarDatos(){
    let datosGuardavidas = objectStore.getAll();
    return datosGuardavidas;
}

function eliminarDatosIndexed(id){
    let request = objectStore.delete(id);
    request.onsuccess = function(event) {
        console.log(`Registro ${id} eliminado correctamente`);
    };
    request.onerror = function(event) {
        console.error(`Error al eliminar registro ${id}:`, event.target.error);
    };
}