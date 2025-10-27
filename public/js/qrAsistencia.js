// ===========================================================
// qrAsistencia.js - Versión Final Integrada
// BarcodeDetector + Html5Qrcode + manejo de errores + registro
// ===========================================================
// -----------------------------------------------------------
// Constantes y elementos HTML
// -----------------------------------------------------------
// video: elemento <video> donde se mostrará la cámara
// contenedorAnimacionCarga: overlay con animación mientras se procesa
// animacionCarga: elemento animado de carga
// mensaje: feedback textual al usuario (errores, advertencias, éxito)
// csrfToken: token de seguridad para peticiones POST

const video = document.getElementById("video");
const contenedorAnimacionCarga = document.getElementById("contenedorCarga");
const animacionCarga = document.getElementById("carga");
const mensaje = document.getElementById("mensaje"); // para feedback al usuario
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

let scanning = false;
let detector;
let html5Scanner;
let timeoutId;

// -----------------------------------------------------------
// Iniciar cámara y escaneo automático
// -----------------------------------------------------------
// -----------------------------------------------------------
// iniciarCamara()
// -----------------------------------------------------------
// Inicializa la cámara trasera del dispositivo.
// Decide si usar BarcodeDetector nativo o Html5Qrcode (fallback).
// Controla timeout de 8 minutos y errores de permisos.

async function iniciarCamara() {
    try {
        if ("BarcodeDetector" in window) {
            detector = new BarcodeDetector({ formats: ["qr_code"] });
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: "environment" },
            });
            video.srcObject = stream;
            video.setAttribute("playsinline", true);
            video.play();
            scanning = true;
            requestAnimationFrame(scanFrame);
        } else {
            // fallback a html5-qrcode
            html5Scanner = new Html5Qrcode("video");
            await html5Scanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                (decodedText) => manejarQRLeido(decodedText),
                (errorMessage) => console.log("Escaneando...", errorMessage)
            );
            // Timeout de 8 minutos
            timeoutId = setTimeout(() => {
                detenerScanner();
                mostrarMensaje(
                    "⏱️ Tiempo de escaneo expiró. Intenta de nuevo.",
                    "warning"
                );
            }, 8 * 60 * 1000);
        }
    } catch (error) {
        console.error("No se pudo acceder a la cámara:", error);
        Swal.fire("Error", "No se pudo acceder a la cámara.", "error");
    }
}

// -----------------------------------------------------------
// Escaneo de frames con BarcodeDetector
// -----------------------------------------------------------
// -----------------------------------------------------------
// scanFrame()
// -----------------------------------------------------------
// Función recursiva que analiza cada frame usando BarcodeDetector
// para detectar QR. Si encuentra uno, llama a manejarQRLeido.

async function scanFrame() {
    if (!scanning) return;
    try {
        const barcodes = await detector.detect(video);
        if (barcodes.length > 0) {
            const valorQR = barcodes[0].rawValue;
            manejarQRLeido(valorQR);
        }
    } catch (error) {
        console.error("Error en detección:", error);
    }
    if (scanning) requestAnimationFrame(scanFrame);
}

// -----------------------------------------------------------
// Manejar QR leído
// -----------------------------------------------------------
// -----------------------------------------------------------
// manejarQRLeido(valorQR)
// -----------------------------------------------------------
// Función que se ejecuta cuando se detecta un QR.
// Detiene la cámara/scanner y llama a registrarAsistencia.

async function manejarQRLeido(valorQR) {
    scanning = false;
    detenerScanner();
    await registrarAsistencia(valorQR);
}

// -----------------------------------------------------------
// Detener escaneo/cámara
// -----------------------------------------------------------
// -----------------------------------------------------------
// detenerScanner()
// -----------------------------------------------------------
// Detiene cualquier scanner activo (BarcodeDetector o Html5Qrcode),
// detiene la cámara, limpia el timeout y resetea flags.

function detenerScanner() {
    if (detector) {
        scanning = false;
    }
    if (html5Scanner) {
        html5Scanner
            .stop()
            .catch((err) =>
                console.error("Error al detener html5Scanner:", err)
            );
        html5Scanner.clear();
        html5Scanner = null;
    }
    if (video.srcObject) {
        video.srcObject.getTracks().forEach((track) => track.stop());
        video.srcObject = null;
    }
    if (timeoutId) {
        clearTimeout(timeoutId);
        timeoutId = null;
    }
}

// -----------------------------------------------------------
// Mostrar mensaje al usuario
// -----------------------------------------------------------
// -----------------------------------------------------------
// mostrarMensaje(texto, tipo)
// -----------------------------------------------------------
// Muestra feedback al usuario en el DOM o por consola.
// tipo = 'error', 'success', 'warning'.

function mostrarMensaje(texto, tipo = "error") {
    if (mensaje) {
        mensaje.innerText = texto;
        mensaje.style.color =
            tipo === "success"
                ? "green"
                : tipo === "warning"
                ? "orange"
                : "red";
    } else {
        console.log(`${tipo.toUpperCase()}: ${texto}`);
    }
}

// -----------------------------------------------------------
// Registrar asistencia (online/offline)
// -----------------------------------------------------------
// -----------------------------------------------------------
// registrarAsistencia(valorQR)
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
            const response = await fetch("/desencriptar-qr", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ encrypted: valorQR }),
            });
            const data = await response.json();

            const idPlaya = data.data.playa_id;
            const idPuesto = data.data.puesto_id;
            const latitudPuesto = data.data.puesto_lat;
            const longitudPuesto = data.data.puesto_lng;

            const resultado = await cargarDistancia(
                latitudPuesto,
                longitudPuesto
            );

            if (resultado.distancia > 200) {
                contenedorAnimacionCarga.style.display = "none";
                animacionCarga.classList.remove("animacion");
                Swal.fire({
                    title: "Error",
                    text: "Fuera del rango permitido (200m). Escaneá más cerca del puesto.",
                    icon: "error",
                    confirmButtonColor: "#36be7f",
                }).then(() => {
                    window.location.href = "/activeCamera";
                });
                return;
            }

            const puestoCorrecto = await perteneceQRAlPuesto(user_id, idPuesto);

            if (puestoCorrecto && puestoCorrecto.success) {
                const datos = {
                    idPlaya,
                    userLat: resultado.userLat,
                    userLng: resultado.userLng,
                    userPrecision: resultado.userPrecision,
                    user_id,
                    idPuesto,
                    fecha_hora: fechaHoraArgentinaDatetime(),
                };
                await cargarDatos(datos);
            } else {
                contenedorAnimacionCarga.style.display = "none";
                animacionCarga.classList.remove("animacion");
                alertaError("Debe escanear en el puesto que le corresponde.");
            }
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
        console.error(err);
        contenedorAnimacionCarga.style.display = "none";
        animacionCarga.classList.remove("animacion");
        alertaError("Ocurrió un error inesperado al registrar la asistencia.");
    }
}

//Obtenemos la fecha y hora Argentina para que se guarde en la base de datos
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
// -----------------------------------------------------------
// Consulta al backend si el QR pertenece al puesto del usuario.
// Permite evitar registros incorrectos fuera del puesto asignado.

async function perteneceQRAlPuesto(user_id, idPuesto) {
    try {
        const res = await fetch("/verPuesto", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({ user_id: user_id, puesto_id: idPuesto }),
        });

        const data = await res.json();
        return data;
    } catch (error) {
        console.error("Error en perteneceQRAlPuesto:", error);
        contenedorAnimacionCarga.style.display = "none";
        animacionCarga.classList.remove("animacion");
        alertaError(
            "Ocurrió un error inesperado al registrar la asistencia. Por favor, intentá nuevamente."
        );
        return null;
    }
}

// -----------------------------------------------------------
// cargarDatos(datos)
// -----------------------------------------------------------
// Envía al backend los datos de asistencia verificados
// y muestra confirmación al usuario mediante Swal.

//Guardamos la asistencia del guardavida en la base de datos
async function cargarDatos(datos) {
    console.log(datos);
    try {
        let response = await fetch("/cargarAsistencia", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
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
                window.location.href = "/dashboard"; // Redireccion despues de cerrar el alert
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
// guardarDatosOffline(user_id, valorQR, userLat, userLng, userPrecision)
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
        console.log("Sin conexión, guardando asistencia localmente...");
        try {
            let guardado = await guardarAsistenciaOffline({
                encrypted: valorQR,
                lat: userLat,
                lng: userLng,
                precision: userPrecision,
                user_id: user_id,
                fecha_hora: fechaHoraArgentinaDatetime(),
                csrfToken: csrfToken,
            });
            contenedorAnimacionCarga.style.display = "none";
            animacionCarga.classList.remove("animacion");
            if (guardado) {
                Swal.fire({
                    title: "OK",
                    text: "¡Asistencia guardada para cuando vuelve el wifi se pueda registrar!",
                    icon: "success",
                    confirmButtonColor: "#36be7f",
                }).then(() => {
                    //window.location.href = "/dashboard";
                });
            } else {
                throw new Error(
                    "No se pudo guarda la asistencia, intente nuevamente"
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
// cargarDistancia(latitudPuesto, longitudPuesto)
// -----------------------------------------------------------
// Obtiene la ubicación del usuario y calcula la distancia
// en metros entre usuario y puesto usando fórmula Haversine.

//Obtiene las coordenas del usuario y calcula la distancia entre el usuario y el puesto
// -----------------------------------------------------------

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

//Obtiene el ID del usuario que se guarda en el navegador para guardar en la asistencia
async function obtenerId() {
    let local_user_id = localStorage.getItem("user_id");
    let user_id = parseInt(local_user_id);
    return user_id;
}

// -----------------------------------------------------------
// obtenerUbicacion()
// -----------------------------------------------------------
// Retorna la ubicación GPS actual del usuario usando Promises.
// Permite await para mayor control de errores.

// Le pedimos al usuario que permita saber su ubicación para poder comparar
function obtenerUbicacion() {
    return new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject);
    });
}

// -----------------------------------------------------------
// alertaError(text)
// -----------------------------------------------------------
// Muestra un popup Swal con el mensaje de error.
// Reutilizable para todos los errores en el flujo.

export function alertaError(text) {
    Swal.fire({
        title: "Error",
        text: text,
        icon: "error",
        confirmButtonColor: "#36be7f",
    }).then(() => {
        //window.location.href = "/dashboard"; // Redireccion despues de cerrar el alert
    });
}

// -----------------------------------------------------------
// calcularDistancia(lat1, lon1, lat2, lon2)
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

// -----------------------------------------------------------
// Iniciar escaneo automáticamente
// -----------------------------------------------------------
window.addEventListener("DOMContentLoaded", iniciarCamara);

/****************************************************************************************************************/
/*** Formato QR esperado:
{
  "codigo_qr": "GV-2025-001",
  "puesto_id": 5,
  "lat": -38.3725,
  "lng": -57.5734
}
**/
