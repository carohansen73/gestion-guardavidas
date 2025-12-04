const html = document.documentElement;
const themeToggleBtn = document.getElementById('theme-toggle');
const lightIcon = document.getElementById('theme-toggle-light-icon');
const darkIcon = document.getElementById('theme-toggle-dark-icon');

// 1. Estado inicial
if (localStorage.getItem('theme') === 'dark' ||
   (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {

    html.classList.add('dark');
    darkIcon.classList.remove('hidden');

} else {
    html.classList.remove('dark');
    lightIcon.classList.remove('hidden');
}

// Transición suave
html.classList.add("transition-colors", "duration-300");

// 2. Evento del botón
themeToggleBtn.addEventListener('click', () => {
    html.classList.toggle('dark');

    if (html.classList.contains('dark')) {
        darkIcon.classList.remove('hidden');
        lightIcon.classList.add('hidden');
        localStorage.setItem('theme', 'dark');
    } else {
        lightIcon.classList.remove('hidden');
        darkIcon.classList.add('hidden');
        localStorage.setItem('theme', 'light');
    }
});
