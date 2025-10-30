const colaMensajes = [];
let mostrando = false;

if ('serviceWorker' in navigator) {
  navigator.serviceWorker.addEventListener('message', event => {
    colaMensajes.push(event.data);
    mostrarSiguiente();
  });
}

function mostrarSiguiente() {
  if (mostrando || colaMensajes.length === 0) return;
  mostrando = true;

  const { status, message } = colaMensajes.shift();

  Swal.fire({
    icon: status,
    title: status === 'success' ? 'Éxito' : 'Error',
    text: message,
    confirmButtonColor: "#36be7f",
  }).then(() => {
    mostrando = false;
    mostrarSiguiente(); //Muestra la siguiente notificación solo cuando se confirma
  });
}
