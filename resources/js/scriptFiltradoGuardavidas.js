// scriptFiltradoGuardavidas.js

document.addEventListener("DOMContentLoaded", () => {
    const filtros = document.querySelectorAll(".btn-filtro");
    const guardavidas = document.querySelectorAll(".lista-guardavidas li");

    filtros.forEach((btn) => {
        btn.addEventListener("click", () => {
            const tipo = btn.dataset.tipo;

            // Quitar clase activo de todos los botones del mismo grupo
            btn.parentElement
                .querySelectorAll(".btn-filtro")
                .forEach((b) => b.classList.remove("activo"));
            btn.classList.add("activo");

            // Obtener filtros activos
            // const filtroBalneario = document
            //     .querySelector('.btn-filtro.activo[data-tipo="balneario"]')
            //     .dataset.valor.toLowerCase();
            const filtroPuesto = document
                .querySelector('.btn-filtro.activo[data-tipo="puesto"]')
                .dataset.valor.toLowerCase();
            const filtroTurnoElement = document.querySelector(
                '.btn-filtro.activo[data-tipo="turno"]'
            );
            const filtroTurno = filtroTurnoElement
                ? filtroTurnoElement.dataset.valor.toLowerCase()
                : "todos";

            // Iterar guardavidas
            guardavidas.forEach((li) => {
                let mostrar = true;

                // Balneario
                if (
                    filtroBalneario !== "todos" &&
                    li.dataset.balneario !== filtroBalneario
                ) {
                    mostrar = false;
                }

                // Puesto
                if (
                    filtroPuesto !== "todos" &&
                    li.dataset.puesto !== filtroPuesto
                ) {
                    mostrar = false;
                }

                // Turno (si lo agregás después)
                if (
                    filtroTurno !== "todos" &&
                    li.dataset.turno &&
                    li.dataset.turno !== filtroTurno
                ) {
                    mostrar = false;
                }

                li.style.display = mostrar ? "block" : "none";
            });
        });
    });
});
