const telefono = "5492983XXXXXX"; // CAMBIAR NUMERO

async function hayInternet() {
    if (!navigator.onLine) return false;

    try {
        const res = await fetch('/ping', {
            method: 'GET',
            cache: 'no-store'
        });
        return res.status === 204;
    } catch {
        return false;
    }
}

function obtenerUbicacion() {
  if (!navigator.geolocation) {
    alert("Tu dispositivo no soporta geolocalización");
    return;
  }

  const opciones = {
    enableHighAccuracy: true, 
    timeout: 60000,           
    maximumAge: 0             
  };

  navigator.geolocation.getCurrentPosition(
    successUbicacion,
    errorUbicacion,
    opciones
  );
}

function successUbicacion(position) {
  const lat = position.coords.latitude;
  const lng = position.coords.longitude;


  const nombre = document.getElementById('nombre').value;
  const fecha = new Date().toLocaleString();

    const mensaje =
        `Registro de guardavidas:
        Nombre: ${nombre}
        Fecha y hora: ${fecha}
        Ubicación:
        Lat: ${lat}
        Lng: ${lng}`;

 
    const url = `https://wa.me/${telefono}?text=${encodeURIComponent(mensaje)}`;
    window.open(url, '_blank');
}

function errorUbicacion(error) {
    let mensaje = "";

  switch (error.code) {
    case error.PERMISSION_DENIED:
      mensaje = "Tenés que permitir el uso del GPS.";
      break;
    case error.POSITION_UNAVAILABLE:
      mensaje = "No se pudo obtener la ubicación. Activá el GPS.";
      break;
    case error.TIMEOUT:
      mensaje = "El GPS tardó demasiado. Probá moverte o salir al aire libre.";
      break;
    default:
      mensaje = "Error desconocido al obtener la ubicación.";
  }

  alert(mensaje);
}


document.addEventListener('DOMContentLoaded', async () => {
    const btn = document.getElementById('btnWhatsapp');
    const estado = document.getElementById('estado');
    const contenedorOffline = document.getElementById('contenedorOffline');
    const contenedorOnline = document.getElementById('formOnline');
    const online = await hayInternet();

    btn.addEventListener('click', obtenerUbicacion);

    if (!online) {
        btn.style.display = 'block';
        contenedorOffline.style.display = 'block';
        contenedorOnline.style.display = 'none';
        estado.textContent = 'Sin conexión. Enviar registro por WhatsApp.';
        btn.addEventListener('click', obtenerUbicacion);
    } else {
        estado.textContent = 'Con conexión a internet.';
    }
});