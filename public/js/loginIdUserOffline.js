
boton = document.getElementById("botonEnviarLogin");
boton.addEventListener('click', (e) =>{
    e.preventDefault();
    loginOffline();
})


async function loginOffline() {
    const email = document.querySelector('#email').value;
    const password = document.querySelector('#password').value;

    try {
        const res = await fetch('api/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email, password })
        });

        const data = await res.json();
        if (data.success) {
            // Guardás el user_id en localStorage para usarlo offline
            localStorage.setItem('user_id', data.user.id);
             window.location.href = '/dashboard'; // redirigir manualmente
        } 
    } catch (err) {
        console.error('Error al hacer login:', err);
    }
}
