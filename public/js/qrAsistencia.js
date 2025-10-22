import { guardarAsistenciaOffline } from "./baseDeDatosNavegador.js";

// scanner.js
const video = document.querySelector("#video");
const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content"); // Envia el csrf en la consulta para desencriptar el qr

const contenedorAnimacionCarga = document.getElementById("contenedorCarga");
const animacionCarga = document.getElementById("carga");

// Inicializa la cámara
async function iniciarCamara() {
    if (!("mediaDevices" in navigator)) {
        alert("Tu navegador no soporta acceso a la cámara.");
        return;
    }

    try {
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: "environment" }, // Cámara trasera en móviles
        });

        video.srcObject = stream;
        await video.play(); // Espera a que el video empiece
        console.log("Cámara iniciada correctamente.");

        // Verificamos compatibilidad del detector
        if (!("BarcodeDetector" in window)) {
            alert(
                "BarcodeDetector no está soportado en este navegador. Usa jsQR como alternativa."
            );
            return;
        }
        // Arranca la deteccion del QR
        detectarQR();
    } catch (error) {
        contenedorAnimacionCarga.style.display = "none";
        alertaError("Error al acceder a la cámara.");
    }
}

// Detecta códigos QR continuamente
async function detectarQR() {
    const barcodeDetector = new BarcodeDetector({ formats: ["qr_code"] });

    if (
        video.readyState < 2 ||
        video.videoWidth === 0 ||
        video.videoHeight === 0
    ) {
        console.log("Video no listo aún, reintentando...");
        return;
    }

    try {
        const barcodes = await barcodeDetector.detect(video);

        // Si encontró al menos un código (array con longitud > 0)
        if (barcodes.length > 0) {
            const valorQR = barcodes[0].rawValue;
            registrarAsistencia(valorQR);
        } else {
            // Si no encontró ningún QR en este frame, vuelve a llamar detectarQR en el siguiente frame
            // usando requestAnimationFrame para mantener la detección continua y eficiente.
            requestAnimationFrame(detectarQR);
        }
    } catch (error) {
        alertaError("Error al detectar QR.");
    }
}

// Si hay internet, desencriptamos el QR, comparamos el rango de distancia donde se escaneo y en caso de estar a menos de
// 200 mts, se guarda la asistencia
//Si llega a pasa que no hay internet, se comprueba la distancia donde leyo el QR y si esta en un rango menor a 200 mts
//La información pasa a guardarse en el IndexedDB (falta que, cuando vuelva a tener internet automaticamente lo detecte y guarde
//los datos en la base de datos)
async function registrarAsistencia(valorQR) {
    contenedorAnimacionCarga.style.display = "block";
    animacionCarga.classList.add("animacion");
    const user_id = await obtenerId();
    try {
        if (navigator.onLine) {
            fetch("/desencriptar-qr", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ encrypted: valorQR }),
            })
                .then((res) => res.json())
                .then((data) => {
                    console.log(data);
                    console.log(`QR leído: Puesto ${data.data.playa_id}`);
                    let idPlaya = data.data.playa_id;
                    let idPuesto = data.data.puesto_id;
                    let latitudPuesto = data.data.puesto_lat;
                    let longitudPuesto = data.data.puesto_lng;

                    //se pide la ubicacion del usurio desde el navegador
                    cargarDistancia(latitudPuesto, longitudPuesto).then(
                        (resultado) => {
                            if (resultado.distancia > 200) {
                                contenedorAnimacionCarga.style.display = "none";
                                animacionCarga.classList.remove("animacion");
                                Swal.fire({
                                    title: "Error",
                                    text: "Se encuentra fuera del rango permitido (200m). Vuelve a escanear cerca del puesto.",
                                    icon: "error",
                                    confirmButtonColor: "#36be7f",
                                }).then(() => {
                                    window.location.href = "/activeCamera"; // Redireccion despues de cerrar el alert
                                });
                                return;
                            } else {
                                //Cargar la asistencia para guardarla
                                perteneceQRAlPuesto(user_id, idPuesto).then(
                                    (puestoCorrecto) => {
                                        console.log(puestoCorrecto)
                                        if (puestoCorrecto && puestoCorrecto.success == true){
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
                                        }
                                        else{
                                            contenedorAnimacionCarga.style.display = "none";
                                            animacionCarga.classList.remove("animacion");
                                            alertaError("Debe escanear en el puesto que le corresponde.");
                                        }
                                    }
                                )
                                
                            }
                        }
                    );
                    //siempre que falle termina el escaneo
                    //scanner.stop();
                });
        } else {
            guardarDatosOffline(
                user_id,
                valorQR,
                resultado.userLat,
                resultado.userLng,
                resultado.userPrecision
            );
        }
    } catch (err) {
        console.log(err);
        contenedorAnimacionCarga.style.display = "none";
        animacionCarga.classList.remove("animacion");
        alertaError(
            "Ocurrió un error inesperado al registrar la asistencia. Por favor, intentá nuevamente."
        );
    }
}

//Obtenemos la fecha y hora Argentina para que se guarde en la base de datos
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

// Iniciar automáticamente al cargar
window.addEventListener("DOMContentLoaded", iniciarCamara);

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

// Le pedimos al usuario que permita saber su ubicación para poder comparar
function obtenerUbicacion() {
    return new Promise((resolve, reject) => {
        navigator.geolocation.getCurrentPosition(resolve, reject);
    });
}

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
        alertaError("Ocurrió un error inesperado al registrar la asistencia. Por favor, intentá nuevamente.");
        return null;
    }
}


async function guardarDatosOffline(user_id,valorQR,userLat,userLng,userPrecision) {
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
                    text: "¡Asistencia guardada para cuando vuelve el wifi se pueda registrar!.",
                    icon: "success",
                    confirmButtonColor: "#36be7f",
                }).then(() => {
                    window.location.href = "/dashboard";
                });
            } else {
                alertaError(
                    "No se pudo guardar la asistencia. Por favor, intentá nuevamente"
                );
            }
        } catch (error) {
            alertaError(
                "No se pudo guardar la asistencia. Por favor, intentá nuevamente"
            );
        }
    } else {
        registrarAsistencia(valorQR);
    }
}

//Obtiene las coordenas del usuario y calcula la distancia entre el usuario y el puesto
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

function alertaError(text) {
    Swal.fire({
        title: "Error",
        text: text,
        icon: "error",
        confirmButtonColor: "#36be7f",
    }).then(() => {
        //window.location.href = "/dashboard"; // Redireccion despues de cerrar el alert
    });
}

//Obtiene el ID del usuario que se guarda en el navegador para guardar en la asistencia
async function obtenerId() {
    let local_user_id = localStorage.getItem("user_id");
    let user_id = parseInt(local_user_id);
    return user_id;
}

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
            contenedorAnimacionCarga.style.display = "none";
            animacionCarga.classList.remove("animacion");
            alertaError(
                "No se pudo registrar asistencia. Por favor, intentá nuevamente"
            );
        }
    } catch (err) {
        contenedorAnimacionCarga.style.display = "none";
        animacionCarga.classList.remove("animacion");
        alertaError(
            "Ocurrió un error inesperado al registrar la asistencia. Por favor, intentá nuevamente."
        );
    }
}
