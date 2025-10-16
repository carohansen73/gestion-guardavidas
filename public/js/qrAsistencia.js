// scanner.js
const video = document.querySelector("#video");
const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content"); // Envia el csrf en la consulta para desencriptar el qr

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
        console.error("Error al acceder a la cámara:", error);
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
        console.log("⏳ Video no listo aún, reintentando...");
        return;
    }

    try {
        const barcodes = await barcodeDetector.detect(video);
        if (barcodes.length > 0) {
            const valorQR = barcodes[0].rawValue;
            registrarAsistencia(valorQR);
        } else {
            requestAnimationFrame(detectarQR);
        }
    } catch (error) {
        console.error("⚠️ Error al detectar QR:", error);
        requestAnimationFrame(detectarQR);
    }
}



async function registrarAsistencia(valorQR) {
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
                                console.log("Estás fuera del rango permitido (200m). Vuelve a escanear cerca del puesto.");
                                return;
                            } else {
                                //Cargar la asistencia para guardarla
                                let datos = {
                                    idPlaya: idPlaya,
                                    userLat: resultado.userLat,
                                    userLng: resultado.userLng,
                                    userPrecision: resultado.userPrecision,
                                    user_id: user_id,
                                    idPuesto: idPuesto,
                                    fechayhora: Date.now(),
                                };
                                cargarDatos(datos);
                            }
                        }
                    );
                    //siempre que falle termina el escaneo
                    //scanner.stop();
                });
        } else {
            let lat, lng;
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    // éxito
                    lat = position.coords.latitude;
                    lng = position.coords.longitude;
                    console.log("Latitud:", lat);
                    console.log("Longitud:", lng);
                },
                function (error) {
                    console.error("Error obteniendo ubicación:", error);
                }
            );
        }
    } catch (err) {
      console.log(err);
        /*valoresUbiUsuario = cargarDistancia(null, null);
        console.log(valoresUbiUsuario);
        // Aquí falla por falta de conexión
        mensaje.innerText =
            "⚠️ Sin conexión, guardando asistencia localmente...";
        mensaje.style.color = "orange";

        // Guardar localmente en IndexedDB
        //Se guarda QR para ser procesado cuando vuelva el internet
        guardarAsistenciaOffline({
            encrypted: valorQR,
            lat: userLat,
            lng: userLng,
            precision: userPrecision,
            user_id: user_id,
            fecha_hora: Date.now(),
            csrfToken: csrfToken,
        });*/
    }
}

// Iniciar automáticamente al cargar
window.addEventListener("DOMContentLoaded", iniciarCamara);



function calcularDistancia(lat1, lon1, lat2, lon2) {
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


async function cargarDistancia(latitudPuesto, longitudPuesto) {
    try {
        const position = await new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject);
        });
        let resultadoDistancia = await success(
            position,
            latitudPuesto,
            longitudPuesto
        );

        return resultadoDistancia;
    } catch (err) {
        console.error("Error obteniendo ubicación:", err);
    }
}


async function success(position, latitudPuesto, longitudPuesto) {
    //obtenemos latitud y longitud del usuario
    let userLat = position.coords.latitude;
    let userLng = position.coords.longitude;
    let userPrecision = position.coords.accuracy; // en metros
    let distancia = calcularDistancia(
        userLat,
        userLng,
        latitudPuesto,
        longitudPuesto
    );

    return {
        distancia: distancia,
        userLat: userLat,
        userLng: userLng,
        userPrecision: userPrecision,
    };
}


async function obtenerId() {
    let local_user_id = localStorage.getItem("user_id");
    let user_id = parseInt(local_user_id);
    console.log(user_id);
    return user_id;
  }



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
                //fechayhora: datos.fechayhora,
            }),
        });

        let res = await response.json();
        if (res.success) {
            console.log("Asistencia registrada correctamente.");

        } else {
           console.log(res.error || "No se pudo registrar asistencia.");
        }
    } catch(err) {
      console.error("Error en fetch /cargarAsistencia:", err);
        console.log("Sin conexión, guardando asistencia localmente...");

        // Guardar localmente en IndexedDB
        //Se guarda QR para ser procesado cuando vuelva el internet
        /*guardarAsistenciaOffline({
                balneario_id: idPlaya,
                lat: userLat,
                lng: userLng,
                precision: userPrecision,
                user_id: user_id,
                puesto_id: idPuesto,
                fecha_hora: Date.now(),
                csrfToken: csrfToken,
            });*/
    }
}
