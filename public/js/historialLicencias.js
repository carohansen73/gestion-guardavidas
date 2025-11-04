document.addEventListener("DOMContentLoaded", () => {
    const btn = document.getElementById("verLicenciasBtn");
    const modal = document.getElementById("modal-licencias");
    const cerrar = document.getElementById("cerrar-modal");

    // --- Mostrar y ocultar modal ---
    btn.addEventListener("click", (e) => {
        e.preventDefault();
        modal.style.display = "flex";
        setTimeout(() => modal.classList.add("show"), 10);
    });

    cerrar.addEventListener("click", () => {
        modal.classList.remove("show");
        setTimeout(() => (modal.style.display = "none"), 400);
    });

    modal.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.classList.remove("show");
            setTimeout(() => (modal.style.display = "none"), 400);
        }
    });

    // --- Paginación ---
    const lista = Array.from(document.querySelectorAll("#lista-licencias li"));
    const itemsPorPagina = 4;
    let paginaActual = 1;

    const pageNum = document.getElementById("page-num");
    const prevBtn = document.getElementById("prev-page");
    const nextBtn = document.getElementById("next-page");

    function mostrarPagina(num) {
        lista.forEach((item, index) => {
            item.style.display =
                index >= (num - 1) * itemsPorPagina &&
                index < num * itemsPorPagina ?
                "flex" :
                "none";
        });
        pageNum.textContent = num;
    }

    mostrarPagina(paginaActual);

    nextBtn.addEventListener("click", () => {
        const totalPaginas = Math.ceil(lista.length / itemsPorPagina);
        if (paginaActual < totalPaginas) {
            paginaActual++;
            mostrarPagina(paginaActual);
        }
    });

    prevBtn.addEventListener("click", () => {
        if (paginaActual > 1) {
            paginaActual--;
            mostrarPagina(paginaActual);
        }
    });
});