import { guardarAsistenciaOffline } from "./baseDeDatosNavegador.js";

// -----------------------------------------------------------
// Constantes y elementos HTML
// -----------------------------------------------------------
// contenedorAnimacionCarga: overlay con animación mientras se procesa
// animacionCarga: elemento animado de carga
// puestoSeleccionado: Puesto seleccionado para poder guardar la asistencia en esa ubicación.

let scanning = false;
let html5Scanner;
let timeoutId;


const contenedorAnimacionCarga = document.getElementById("contenedorCarga");
const animacionCarga = document.getElementById("carga");
let puestoSeleccionado = document.getElementById("puestoSeleccionadoFichar");

document.querySelector(".contenedorQR").style.display = "block";

// Toma valores del local storage id, bearer token
let offline_user = localStorage.getItem("offline_user");
const user = JSON.parse(offline_user);

// -----------------------------------------------------------
// Iniciar cámara y escaneo automático
// -----------------------------------------------------------
// -----------------------------------------------------------
// iniciarCamara()
// -----------------------------------------------------------
// Inicializa la cámara trasera del dispositivo.
// Usa Html5Qrcode (fallback).
// Controla timeout de 2 minutos y errores de permisos.
// Solo Android inicia automáticamente

async function iniciarCamara() {
    try {
        // fallback a html5-qrcode
        html5Scanner = new Html5Qrcode("qr-reader");
        await html5Scanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 600 },
            (decodedText) => manejarQRLeido(decodedText)
        );
        // Timeout de 2 minutos
        timeoutId = setTimeout(() => {
            detenerScanner();
            alertaError(
                "El tiempo de escaneo expiró. Intenta nuevamente.",
                "warning"
            );
        }, 2 * 60 * 1000);
    } catch (error) {
        Swal.fire({
            title: "Error",
            text: "No se pudo acceder a la cámara.",
            icon: "error",
            confirmButtonColor: "#36be7f",
        }).then(() => {
            window.location.href = "/home"; // Redireccion despues de cerrar el alert
        });
    }
}

// -----------------------------------------------------------
// Manejar QR leído
// -----------------------------------------------------------
// -----------------------------------------------------------
// manejarQRLeido(valorQR)
// valorQR: string => QR detectado por la cámara
// -----------------------------------------------------------
// Función que se ejecuta cuando se detecta un QR.
// Detiene la cámara/scanner y llama a registrarAsistencia.

async function manejarQRLeido(valorQR) {
    scanning = false;
    clearTimeout(timeoutId);
    await detenerScanner();
    await registrarAsistencia(valorQR);
}

// -----------------------------------------------------------
// Detener escaneo/cámara
// -----------------------------------------------------------
// -----------------------------------------------------------
// detenerScanner()
// -----------------------------------------------------------
// Detiene cualquier scanner activo de Html5Qrcode,
// detiene la cámara, limpia el timeout y resetea flags.

async function detenerScanner() {
    scanning = false;

    if (html5Scanner) {
        try {
            await html5Scanner.stop();
            await html5Scanner.clear();
        } catch (err) {
            console.error("Error al detener html5Scanner:", err);
        }
        html5Scanner = null;
    }

    if (timeoutId) {
        clearTimeout(timeoutId);
        timeoutId = null;
    }
}

// -----------------------------------------------------------
// Registrar asistencia (online/offline)
// -----------------------------------------------------------
// -----------------------------------------------------------
// registrarAsistencia(valorQR)
// valorQR: string => QR leido por la cámara
// -----------------------------------------------------------
// Función principal de registro de asistencia.
// Si hay internet:
//    - desencripta QR
//    - verifica distancia (≤ 200m)
//    - valida que el QR pertenezca al puesto correcto
//    - guarda asistencia en base de datos
// Si no hay internet:
//    - guarda localmente para sincronizar luego

async function registrarAsistencia(valorQR) {
    contenedorAnimacionCarga.style.display = "block";
    animacionCarga.classList.add("animacion");
    const user_id = await obtenerId();
    try {
        if (navigator.onLine) {
            let data = await desencriptarQR(valorQR);
            if (!data) {
                throw new Error(
                    "Ocurrió un error inesperado al registrar la asistencia. Por favor, intentá nuevamente."
                );
            }

            let idPlaya, idPuesto, latitudPuesto, longitudPuesto;

            if (puestoSeleccionado.value !== "default") {
                const optionSeleccionada =
                    puestoSeleccionado.options[
                        puestoSeleccionado.selectedIndex
                    ];

                idPlaya = parseInt(optionSeleccionada.dataset.id);
                idPuesto = parseInt(optionSeleccionada.value);

                //data. referencia a los datos que vienen del QR , idPlaya y idPuesto son los valores al seleccionar el puesto donde fichar
                // se encarga de que, si seleccionó donde se va a registrar la asistencia, escanee el QR correspondiente
                if (idPlaya != data.playa_id || idPuesto != data.puesto_id) {
                    throw new Error(
                        "Debe escanear el QR del puesto correspondiente donde registrará la asistencia."
                    );
                }
                latitudPuesto = parseFloat(optionSeleccionada.dataset.lat);
                longitudPuesto = parseFloat(optionSeleccionada.dataset.lon);
            } else {
                idPlaya = data.playa_id;
                idPuesto = data.puesto_id;
                latitudPuesto = data.puesto_lat;
                longitudPuesto = data.puesto_lng;

                // En caso de no cambiar el lugar de fichaje, se fija si esta escaneando el QR correcto
                let puestoCorrecto = await perteneceQRAlPuesto(
                    user_id,
                    idPuesto
                );
                if (!puestoCorrecto || puestoCorrecto.success == false) {
                    throw new Error(
                        "No se puede registrar la asistencia: el QR esta siendo escaneado en el puesto incorrecto."
                    );
                }
            }

            let resultado = await cargarDistancia(
                latitudPuesto,
                longitudPuesto
            );

            if (resultado == null || isNaN(resultado.distancia)) {
                throw new Error("La ubicación está desactivada o no fue autorizada. Activala para poder registrar la asistencia.");
            }

            //Determina si el idPuesto (ya sea el seleccionado o el asignado) esta registrado como movil
            let esFueraDeZona = await obtenerFueraDeZona(idPuesto);

            if (!esFueraDeZona.success) {
                if (resultado.distancia > 200) {
                    throw new Error(
                        "No se puede registrar la asistencia: el QR esta siendo escaneado a más de 200 metros de distancia."
                    );
                }
            }

            // userLat, userLng, userPrecision en los casos que son fuera de zona de baño se guarda la ubicación pero no se controla
            let datos = {
                idPlaya: idPlaya,
                userLat: resultado.userLat,
                userLng: resultado.userLng,
                userPrecision: resultado.userPrecision,
                user_id: user_id,
                idPuesto: idPuesto,
                fecha_hora: fechaHoraArgentinaDatetime(),
            };
            cargarDatos(datos);
        } else {
            // Offline
            const resultado = await obtenerUbicacion();
            await guardarDatosOffline(
                user_id,
                valorQR,
                resultado.coords.latitude,
                resultado.coords.longitude,
                resultado.coords.accuracy
            );
        }
    } catch (err) {
        contenedorAnimacionCarga.style.display = "none";
        animacionCarga.classList.remove("animacion");
        alertaError(err);
    }
}

async function desencriptarQR(valorQR) {
    try {
        const res = await fetch("api/desencriptar-qr", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${user.token}`,
            },
            body: JSON.stringify({ encrypted: valorQR }),
        });
        const data = await res.json();
        return data.data;
    } catch (err) {
        return undefined;
    }
}

// -----------------------------------------------------------
// fechaHoraArgentinaDatetime()
// -----------------------------------------------------------
// Retorna fecha y hora actual de Argentina en formato YYYY-MM-DD HH:MM:SS

function fechaHoraArgentinaDatetime() {
    const ahora = new Date();
    const opciones = { timeZone: "America/Argentina/Buenos_Aires" };
    const fecha = new Intl.DateTimeFormat("sv-SE", opciones).format(ahora);
    const hora = ahora.toLocaleTimeString("es-AR", {
        timeZone: "America/Argentina/Buenos_Aires",
        hour12: false,
    });

    return `${fecha} ${hora}`;
}

// -----------------------------------------------------------
// perteneceQRAlPuesto(user_id, idPuesto)
// user_id: int => id del usuario
// dPuesto: int => id del puesto
// -----------------------------------------------------------
// Consulta al backend si el QR pertenece al puesto del usuario.
// Permite evitar registros incorrectos fuera del puesto asignado.

async function perteneceQRAlPuesto(user_id, idPuesto) {
    try {
        const res = await fetch("api/verPuesto", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${user.token}`,
            },
            body: JSON.stringify({ user_id: user_id, puesto_id: idPuesto }),
        });

        const data = await res.json();
        return data;
    } catch (error) {
        contenedorAnimacionCarga.style.display = "none";
        animacionCarga.classList.remove("animacion");
        alertaError(
            "Ocurrió un error inesperado al registrar la asistencia. Por favor, intentá nuevamente."
        );
        return null;
    }
}

// -----------------------------------------------------------
// guardarDatosOffline(user_id, valorQR, userLat, userLng, userPrecision)
// user_id: int => id del usuario
// valorQR: string => codigo QR leido por la cámara
// userLat: number => latitud del usuario
// userLng: number => longitud del usuario
// userPrecision: number => Precisión del GPS en metros
// -----------------------------------------------------------
// Guarda localmente los datos de asistencia cuando no hay internet.
// Posteriormente se sincronizan automáticamente (implementación aparte).

async function guardarDatosOffline(
    user_id,
    valorQR,
    userLat,
    userLng,
    userPrecision
) {
    if (!navigator.onLine) {
        try {
            let guardado = await guardarAsistenciaOffline({
                encrypted: valorQR,
                lat: userLat,
                lng: userLng,
                precision: userPrecision,
                user_id: user_id,
                fecha_hora: fechaHoraArgentinaDatetime(),
                token_bearer: user.token,
            });
            contenedorAnimacionCarga.style.display = "none";
            animacionCarga.classList.remove("animacion");
            if (guardado) {
                Swal.fire({
                    title: "OK",
                    text: "Asistencia guardada. Se registrará automáticamente cuando vuelvas a tener conexión.",
                    icon: "success",
                    confirmButtonColor: "#36be7f",
                }).then(() => {
                    window.location.href = "/dashboard";
                });
            } else {
                throw new Error(
                    "No se pudo guardar la asistencia, intente nuevamente"
                );
            }
        } catch (error) {
            alertaError(error);
        }
    } else {
        registrarAsistencia(valorQR);
    }
}

// -----------------------------------------------------------
// cargarDatos(datos)
// datos: JSON => datos a guardar en la base de datos de asistencia
// -----------------------------------------------------------
// Envía al backend los datos de asistencia verificados
// y muestra confirmación al usuario mediante Swal.

async function cargarDatos(datos) {
    try {
        let response = await fetch("api/cargarAsistencia", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${user.token}`,
            },
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

        let res = await response.json();
        if (res.success) {
            contenedorAnimacionCarga.style.display = "none";
            animacionCarga.classList.remove("animacion");
            Swal.fire({
                title: "OK",
                text: "Asistencia registrada correctamente.",
                icon: "success",
                confirmButtonColor: "#36be7f",
            }).then(() => {
                window.location.href = "/dashboard"; // Redireccion despues de cerrar el Swal
            });
        } else {
            throw new Error(
                "Ocurrió un error inesperado al registrar la asistencia. Por favor, intentá nuevamente."
            );
        }
    } catch (err) {
        contenedorAnimacionCarga.style.display = "none";
        animacionCarga.classList.remove("animacion");
        alertaError(err);
    }
}

// -----------------------------------------------------------
// cargarDistancia(latitudPuesto, longitudPuesto)
// latitudPuesto: numeric => latitud del puesto (coordenadas)
// longitudPuesto: numeric => longitud del puesto (coordenadas)
// -----------------------------------------------------------
// Obtiene la ubicación del usuario y calcula la distancia
// en metros entre usuario y puesto usando fórmula Haversine (calcularDistancia).

async function cargarDistancia(latitudPuesto, longitudPuesto) {
    try {
        let position = await obtenerUbicacion();
        let userLat = position.coords.latitude;
        let userLng = position.coords.longitude;
        let userPrecision = position.coords.accuracy; // en metros
        let resultadoDistancia = await calcularDistancia(
            userLat,
            userLng,
            latitudPuesto,
            longitudPuesto
        );

        return {
            distancia: resultadoDistancia,
            userLat: userLat,
            userLng: userLng,
            userPrecision: userPrecision,
        };
    } catch (err) {
        contenedorAnimacionCarga.style.display = "none";
        animacionCarga.classList.remove("animacion");
        alertaError("Error obteniendo su ubicación.");
    }
}

// -----------------------------------------------------------
// obtenerId()
// -----------------------------------------------------------
// Retorna el user_id almacenado en localStorage (ID del guardavidas)

async function obtenerId() {
    let user_id = parseInt(user.id);
    return user_id;
}

// -----------------------------------------------------------
// Obtener estado "movil"
// -----------------------------------------------------------
// -----------------------------------------------------------
// obtenerFueraDeZona(idPuesto)
// idPuesto: number => id del puesto seleccionado/asignado del guardavidas para registrar la asistencia
// -----------------------------------------------------------
// Consulta al backend si el puesto indicado está marcado
// como móvil (fuera de zona de baño).
//
// Retorna:
// - true  → el puesto es móvil
// - false → el puesto no es móvil
//
// En caso de error:
// - Muestra alerta de error

async function obtenerFueraDeZona(idPuesto) {
    idPuesto = Number(idPuesto);
    try {
        const res = await fetch("api/obtenerFueraDeZona", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${user.token}`,
            },
            body: JSON.stringify({ puesto_id: idPuesto }),
        });

        const data = await res.json();
        return data;
    } catch (error) {
        contenedorAnimacionCarga.style.display = "none";
        animacionCarga.classList.remove("animacion");
        alertaError(
            "Ocurrió un error inesperado al registrar la asistencia. Por favor, intentá nuevamente. FUERA DE ZONA"
        );
        //return null;
    }
}

// -----------------------------------------------------------
// obtenerUbicacion()
// -----------------------------------------------------------
// Retorna la ubicación GPS actual del usuario usando Promises.

function obtenerUbicacion() {
    const opciones = {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0,
    };
    return new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject, opciones);
    });
}

// -----------------------------------------------------------
// alertaError(text, icon)
// text: string => Mensaje que se le muestra al usuario
// icon: string => tiene por defecto el valor "error", icono que acompaña al texto
// -----------------------------------------------------------
// Muestra un popup Swal con el mensaje de error.

export function alertaError(text, icon = "error") {
    Swal.fire({
        title: "Error",
        text: text,
        icon: icon,
        confirmButtonColor: "#36be7f",
    }).then(() => {
        iniciarCamara();
        //window.location.href = "/dashboard"; // Redireccion despues de cerrar el alert
        //window.location.href = "/dashboard";
    });
}

// -----------------------------------------------------------
// calcularDistancia(lat1, lon1, lat2, lon2)
// lat1: number  => latitud del usuario
// lon1: number  => longitud del usuario
// lat2: numeric => latitud del puesto
// lon2: numeric => longitud del puesto
// -----------------------------------------------------------
// Implementa la fórmula Haversine para calcular la distancia
// en metros entre dos coordenadas geográficas.

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

window.addEventListener("DOMContentLoaded", iniciarCamara);
