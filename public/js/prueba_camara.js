"use script"

document.addEventListener('DOMContentLoaded',  () => {

    
const btnScan = document.getElementById("escanearQR");
const qrReaderDiv = document.getElementById("qr-reader");
const mensaje = document.getElementById("mensaje");

let html5QrcodeScanner;

btnScan.addEventListener("click", () => {
    html5QrcodeScanner = new Html5Qrcode("qr-reader");

    Html5Qrcode.getCameras().then(cameras => {
        if (cameras && cameras.length) {
            const cameraId = cameras[0].id; // usa la primera cámara disponible
            html5QrcodeScanner.start(
                cameraId,
                {
                    fps: 10,    // frames por segundo
                    qrbox: 250  // tamaño del cuadro de escaneo
                },
                (decodedText, decodedResult) => {
                    // Esto se ejecuta cuando se escanea un QR
                    mensaje.innerText = `QR leído: ${decodedText}`;
                    html5QrcodeScanner.stop(); // detener la cámara después de leer
                    qrReaderDiv.style.display = "none";
                },
                (errorMessage) => {
                    // errores de escaneo, opcional
                    console.warn("QR scan error:", errorMessage);
                }
            );
        }
    }).catch(err => {
        console.error("No se pudo acceder a la cámara:", err);
    });
});
})