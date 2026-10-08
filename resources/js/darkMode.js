// Modo oscuro. Puede haber más de un botón en la página (barra de escritorio y menú del celular):
// todos llevan [data-theme-toggle], y lo que depende del tema actual lleva [data-theme-show="light|dark"]
// (se muestra solo cuando el tema actual es ese).
const html = document.documentElement;

// 1. Estado inicial
if (localStorage.getItem('theme') === 'dark' ||
   (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    html.classList.add('dark');
} else {
    html.classList.remove('dark');
}

function mostrarSegunTema() {
    const oscuro = html.classList.contains('dark');
    document.querySelectorAll('[data-theme-show]').forEach((el) => {
        el.classList.toggle('hidden', (el.dataset.themeShow === 'dark') !== oscuro);
    });
}
mostrarSegunTema();

// Transición suave
html.classList.add("transition-colors", "duration-300");

// 2. Evento de los botones
document.querySelectorAll('[data-theme-toggle]').forEach((boton) => {
    boton.addEventListener('click', () => {
        html.classList.toggle('dark');
        localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
        mostrarSegunTema();
    });
});
