
boton = document.getElementById("botonEnviarLogin");
const errorDiv = document.querySelector('#error'); //Muestra mensaje de error

boton.addEventListener('click', (e) =>{
    e.preventDefault();
    errorDiv.textContent = '';
    loginOffline();
})

async function loginOffline() {
    const email = document.querySelector('#email').value;
    const password = document.querySelector('#password').value;

    try {
        const res = await fetch('/loginIdUser', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email, password })
        });

        const data = await res.json();
        console.log(data);

        if (data.success) {
            localStorage.setItem('user_id', data.user.id);
            localStorage.setItem('token', data.token);
            window.location.href = '/dashboard';
        } else {
            // Mostrar el mensaje del backend
            errorDiv.textContent = data.message || 'Error al iniciar sesión';
        }

    } catch (err) {
        console.error('Error al hacer login:', err);
        errorDiv.textContent = 'Error de conexión con el servidor.';
    }
}

