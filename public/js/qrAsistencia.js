let resultadoDistancia;

window.addEventListener("load", () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content"); // Envia el csrf en la consulta para desencriptar el qr

    const inputFile = document.getElementById("archivoQR");
    const mensaje = document.getElementById("mensaje");
    const html5Qrcode = new Html5Qrcode("leer");

    inputFile.addEventListener("change", () => {
        registrarAsistencia();
    });

    async function registrarAsistencia() {
            const user_id = await obtenerId();
            const file = inputFile.files[0];
            try {
                html5Qrcode.scanFile(file, true)
                    .then((decodedText) => {
                        const encrypted = decodeURIComponent(decodedText);
                        if(window.ononline){
                            fetch("/desencriptar-qr", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": csrfToken,
                                },
                                body: JSON.stringify({ encrypted: encrypted }),
                            })
                            .then((res) => res.json())
                            .then((data) => {
                                mensaje.innerText = `✅ QR leído: Puesto ${data.data.balneario_id}`;

                                let idPlaya = data.data.balneario_id;
                                let idPuesto = data.data.puesto_id;
                                let latitudPuesto = data.data.playa_lat;
                                let longitudPuesto = data.data.playa_lng;
                                //se pide la ubicacion del usurio desde el navegador
                                cargarDistancia(latitudPuesto, longitudPuesto).then(resultado => {
                                    if (resultado.distancia > 200) {
                                        mensaje.innerText = "❌ Estás fuera del rango permitido (200m). Vuelve a escanear cerca del puesto.";

                                        //scanner.stop(); //se fuerza a parar el escaneo ya que no se cumple con uno de los requisitos
                                        return;
                                    }
                                    else {
                                        //Cargar la asistencia para guardarla
                                        let datos = {
                                            'idPlaya': idPlaya,
                                            'userLat': resultado.userLat,
                                            'userLng': resultado.userLng,
                                            'userPrecision': resultado.userPrecision,
                                            'user_id': user_id,
                                            'idPuesto': idPuesto,
                                            'fechayhora': Date.now(),
                                        }
                                        cargarDatos(datos);
                                    }
                                })
                                //siempre que falle termina el escaneo
                                //scanner.stop();            
                            });
                        }
                        else{
                            let lat, lng;
                            navigator.geolocation.getCurrentPosition(
                                function(position) { // éxito
                                    lat = position.coords.latitude;
                                    lng = position.coords.longitude;
                                    console.log('Latitud:', lat);
                                    console.log('Longitud:', lng);
                                },
                                function(error) { // error
                                    console.error('Error obteniendo ubicación:', error);
                                }
                            );
                            //Deberia guardar el qr en el indexed para luego procesarlo? o no?
                        }
                    })
                    .catch((err) => {
                        mensaje.innerHTML = "❌ Aca muestra error: " + err;
                    });        
            }
            catch (err) {
                valoresUbiUsuario = cargarDistancia(null, null);
                console.log(valoresUbiUsuario);
                // Aquí falla por falta de conexión
                mensaje.innerText =
                    "⚠️ Sin conexión, guardando asistencia localmente...";
                mensaje.style.color = "orange";

                // Guardar localmente en IndexedDB
                //Se guarda QR para ser procesado cuando vuelva el internet
                guardarAsistenciaOffline({
                    encrypted: encrypted,
                    lat: userLat,
                    lng: userLng,
                    precision: userPrecision,
                    user_id: user_id,
                    fecha_hora: Date.now(),
                    csrfToken: csrfToken,
                });
            }
    }


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
            let resultadoDistancia = await success(position, latitudPuesto, longitudPuesto);
            return resultadoDistancia;
        } catch (err) {
            console.error('Error obteniendo ubicación:', err);
        }
    }
    

    async function success(position, latitudPuesto, longitudPuesto) {
        //obtenemos latitud y longitud del usuario
        let userLat = position.coords.latitude;
        let userLng = position.coords.longitude;
        let userPrecision = position.coords.accuracy; // en metros
        let distancia = calcularDistancia(userLat, userLng, latitudPuesto, longitudPuesto);

        return {"distancia":distancia, "userLat":userLat, "userLng":userLng, "userPrecision":userPrecision};
    }


    async function obtenerId() {
        if (window.ononline) {
            try {
                const res = await fetch("/guardavidas/logueado");
                if (!res.ok) {
                    const text = await res.text();
                    console.error("Error al verificar login:", res.status, text);
                    return null;
                }
                const data = await res.json();
                return data.id; // ahora sí devuelve el id
            } catch (err) {
                let local_user_id = localStorage.getItem("user_id");
                return local_user_id;
            }
        }
        else {
            let local_user_id = localStorage.getItem("user_id");
            return local_user_id;
        }
    }


    async function cargarDatos(datos) {
        try {
            let response = await fetch(
                "/cargarAsistencia",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({
                        balneario_id: datos.idPlaya,
                        lat: datos.userLat,
                        lng: datos.userLng,
                        precision: datos.userPrecision,
                        user_id: datos.user_id,
                        puesto_id: datos.idPuesto,
                        //fechayhora: datos.fechayhora, 
                    }),
                }
            );

            let res = await response.json();
            if (res.success) {
                mensaje.innerText ="Asistencia registrada correctamente.";
                mensaje.style.color = "green"; // color verde para éxito
            } else {
                mensaje.innerText ="⚠️ " +(res.error || "No se pudo registrar asistencia.");
                mensaje.style.color = "red"; // color rojo para error
            }
        } catch {
            mensaje.innerText = "⚠️ Sin conexión, guardando asistencia localmente...";
            mensaje.style.color = "orange";

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

});