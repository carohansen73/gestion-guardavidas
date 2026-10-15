// Configurar CSRF token para peticiones AJAX
const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
// Un admin sin ficha de guardavida no tiene este form (solo ve la card de
// "Datos de Usuario"), así que todo lo que depende de él se condiciona a que exista.
const form = document.querySelector(".profile-body");

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

    // Auto-ocultar después de 10 segundos
    setTimeout(() => {
        alertContainer.innerHTML = "";
    }, 10000);
}

// Manejar envío del formulario
if (form) {
    form.addEventListener("submit", async function(e) {
        e.preventDefault();

        const btnGuardar = document.getElementById("btnGuardar");
        const textoOriginal = btnGuardar.innerHTML;

        btnGuardar.innerHTML = '<span class="spinner"></span> Guardando...';
        btnGuardar.disabled = true;

        try {
            // El _method=PUT ya viene en el form (@method('PUT'))
            const response = await fetch(this.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
                body: new FormData(this),
            });

            const result = await response.json();

            if (result.success) {
                mostrarAlerta("success", result.titulo, result.detalle);
                setTimeout(() => location.reload(), 2000);
            } else if (response.status === 422 && result.errors) {
                // Errores de validación de Laravel: { message, errors: { campo: [msg] } }
                mostrarAlerta(
                    "error",
                    "Revisá los datos",
                    Object.values(result.errors).flat().join("<br>")
                );
            } else {
                mostrarAlerta(
                    "error",
                    result.titulo ?? "¡Error!",
                    result.detalle ?? result.message ?? "No se pudo actualizar el perfil."
                );
            }
        } catch (error) {
            console.error("Error:", error);
            mostrarAlerta(
                "error",
                "¡Error!",
                "Ocurrió un error al actualizar el perfil. Por favor, intente nuevamente."
            );
        } finally {
            btnGuardar.innerHTML = textoOriginal;
            btnGuardar.disabled = false;
        }
    });
}
