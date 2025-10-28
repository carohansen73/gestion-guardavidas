// Configurar CSRF token para peticiones AJAX
const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

// Función para mostrar alertas
function mostrarAlerta(tipo, titulo, mensaje) {
    const alertContainer = document.getElementById("alertContainer");
    const iconClass =
        tipo === "success" ? "fa-check-circle" : "fa-exclamation-circle";

    const alertHTML = `
                <div class="alert alert-${tipo}">
                    <i class="fas ${iconClass}"></i>
                    <div>
                        <strong>${titulo}</strong><br>
                        ${mensaje}
                    </div>
                </div>
            `;

    alertContainer.innerHTML = alertHTML;

    // Scroll suave hacia arriba
    window.scrollTo({ top: 0, behavior: "smooth" });

    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        alertContainer.innerHTML = "";
    }, 5000);
}

// Manejar envío del formulario
if ($puedeEditar)
    document
        .getElementById("profileForm")
        .addEventListener("submit", async function (e) {
            e.preventDefault();

            const btnGuardar = document.getElementById("btnGuardar");
            const textoOriginal = btnGuardar.innerHTML;

            // Mostrar spinner de carga
            btnGuardar.innerHTML = '<span class="spinner"></span> Guardando...';
            btnGuardar.disabled = true;

            try {
                const formData = new FormData(this);
                const data = {};
                formData.forEach((value, key) => {
                    if (key !== "_token" && key !== "_method") {
                        data[key] = value;
                    }
                });

                const response = await fetch(
                    '{{ route("guardavidas.actualizar", $guardavida->id) }}',
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            Accept: "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                        },
                        body: JSON.stringify({
                            ...data,
                            _method: "PUT",
                        }),
                    }
                );

                const result = await response.json();

                if (result.success) {
                    mostrarAlerta("success", result.titulo, result.detalle);

                    // Opcional: recargar la página después de 2 segundos
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    mostrarAlerta("error", result.titulo, result.detalle);
                }
            } catch (error) {
                console.error("Error:", error);
                mostrarAlerta(
                    "error",
                    "¡Error!",
                    "Ocurrió un error al actualizar el perfil. Por favor, intente nuevamente."
                );
            } finally {
                // Restaurar botón
                btnGuardar.innerHTML = textoOriginal;
                btnGuardar.disabled = false;
            }
        });
