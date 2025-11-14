// Configurar CSRF token para peticiones AJAX
const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");
const form = document.querySelector(".profile-body");
const guardavidaId = parseInt(form.dataset.guardavidaId); // Esto te da el ID

const spanEditar = window.esAdmin === true; // 'Perfil de Guardavidas' o 'Mi Perfil'
let puedeEditar = false;
if (spanEditar === "Perfil de Guardavidas") {
    puedeEditar = true;
}

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
if (puedeEditar) {
    document
        .querySelector(".profile-body")
        .addEventListener("submit", async function(e) {
            e.preventDefault();

            const btnGuardar = document.getElementById("btnGuardar");
            const textoOriginal = btnGuardar.innerHTML;

            btnGuardar.innerHTML = '<span class="spinner"></span> Guardando...';
            btnGuardar.disabled = true;

            try {
                const formData = new FormData(this);
                formData.append("_method", "PUT"); // necesario para Laravel PUT

                const response = await fetch(this.action, {
                    method: "POST", // Laravel interpreta _method como PUT
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        "X-Requested-With": "XMLHttpRequest",
                    },
                    body: formData,
                });

                const result = await response.json();

                if (result.success) {
                    mostrarAlerta("success", result.titulo, result.detalle);
                    setTimeout(() => location.reload(), 2000);
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
                btnGuardar.innerHTML = textoOriginal;
                btnGuardar.disabled = false;
            }
        });
}

document.getElementById("selectPlaya").addEventListener("change", function() {
    let playaId = this.value;
    let puestoSelect = document.getElementById("selectPuesto");
    puestoSelect.innerHTML = '<option value="">Cargando...</option>';

    if (!playaId) {
        puestoSelect.innerHTML = '<option value="">Seleccionar puesto</option>';
        return;
    }

    fetch(`/puestos-por-playa/${playaId}`)
        .then((response) => response.json())
        .then((data) => {
            puestoSelect.innerHTML =
                '<option value="">Seleccionar puesto</option>';

            data.forEach((puesto) => {
                let option = document.createElement("option");
                option.value = puesto.id;
                option.textContent = puesto.nombre_puesto;
                puestoSelect.appendChild(option);
            });
        })
        .catch((error) => {
            console.error("Error cargando puestos:", error);
            puestoSelect.innerHTML =
                '<option value="">Error al cargar</option>';
        });
});