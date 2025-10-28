// Función para ver el perfil
function verPerfil(id) {
    window.location.href = `/guardavidas/perfil/${id}`;
}

// Sistema de búsqueda y filtrado
const searchInput = document.getElementById("searchInput");
const filterBalneario = document.getElementById("filterBalneario");
const filterTurno = document.getElementById("filterTurno");
const cards = document.querySelectorAll(".guardavida-card");

function filtrarGuardavidas() {
    const searchTerm = searchInput.value.toLowerCase();
    const balnearioFilter = filterBalneario.value.toLowerCase();
    const turnoFilter = filterTurno.value.toLowerCase();

    let visibleCount = 0;

    cards.forEach((card) => {
        const nombre = card.dataset.nombre;
        const dni = card.dataset.dni;
        const balneario = card.dataset.balneario;
        const turno = card.dataset.turno;

        const matchSearch =
            nombre.includes(searchTerm) || dni.includes(searchTerm);
        const matchBalneario =
            !balnearioFilter || balneario === balnearioFilter;
        const matchTurno = !turnoFilter || turno === turnoFilter;

        if (matchSearch && matchBalneario && matchTurno) {
            card.style.display = "block";
            visibleCount++;
        } else {
            card.style.display = "none";
        }
    });

    // Mostrar mensaje si no hay resultados
    const grid = document.getElementById("guardavidasGrid");
    let noResults = document.getElementById("noResults");

    if (visibleCount === 0 && cards.length > 0) {
        if (!noResults) {
            noResults = document.createElement("div");
            noResults.id = "noResults";
            noResults.className = "empty-state";
            noResults.style.gridColumn = "1 / -1";
            noResults.innerHTML = `
                        <i class="fas fa-search"></i>
                        <h3>No se encontraron resultados</h3>
                        <p>Intenta con otros criterios de búsqueda</p>
                    `;
            grid.appendChild(noResults);
        }
    } else if (noResults) {
        noResults.remove();
    }
}

searchInput.addEventListener("input", filtrarGuardavidas);
filterBalneario.addEventListener("change", filtrarGuardavidas);
filterTurno.addEventListener("change", filtrarGuardavidas);

/*document.addEventListener("DOMContentLoaded", () => {
    const botones = document.querySelectorAll(".btn-filtro");
    const guardavidas = document.querySelectorAll(".lista-guardavidas li");

    // Estado inicial de los filtros
    const filtrosActivos = {
        balneario: "todos",
        puesto: "todos",
        turno: "todos",
    };

    // Función principal de filtrado
    function filtrarGuardavidas() {
        guardavidas.forEach((item) => {
            const balneario = item.dataset.balneario ?
                item.dataset.balneario.toLowerCase() :
                "";
            const puesto = item.dataset.puesto ?
                item.dataset.puesto.toLowerCase() :
                "";
            const turno = item.dataset.turno ?
                item.dataset.turno.toLowerCase() :
                "";

            const coincideBalneario =
                filtrosActivos.balneario === "todos" ||
                balneario === filtrosActivos.balneario;

            const coincidePuesto =
                filtrosActivos.puesto === "todos" ||
                puesto === filtrosActivos.puesto;

            const coincideTurno =
                filtrosActivos.turno === "todos" ||
                turno === filtrosActivos.turno;

            if (coincideBalneario && coincidePuesto && coincideTurno) {
                item.style.display = "flex";
            } else {
                item.style.display = "none";
            }
        });
    }

    // Escuchar clics en todos los botones de filtro
    botones.forEach((btn) => {
        btn.addEventListener("click", () => {
            const tipo = btn.dataset.tipo; // balneario, puesto o turno
            const valor = btn.dataset.valor; // valor específico

            // Quitar el estado activo a todos los botones del mismo tipo
            document
                .querySelectorAll(`.btn-filtro[data-tipo="${tipo}"]`)
                .forEach((b) => b.classList.remove("activo"));

            // Activar el botón clickeado
            btn.classList.add("activo");

            // Actualizar el filtro activo
            filtrosActivos[tipo] = valor.toLowerCase();

            // Aplicar el filtrado
            filtrarGuardavidas();
        });
    });
});
*/
