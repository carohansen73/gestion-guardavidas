"use strict"

// TODO no andaa!!!!!

document.addEventListener('DOMContentLoaded', e => {
    let currentPlaya = 'all';
    let sortAsc = true;

    window.filterByPlaya = function(playaId) {
        currentPlaya = playaId;

        // TODO setear estilo activo que quede mientras esta filtrado
        // // Clase active
        // document.querySelectorAll('.playa-btn').forEach(btn => {
        //     btn.classList.remove('bg-gray-300');
        //     btn.classList.add('bg-gray-200', 'dark:bg-gray-600', 'dark:text-gray-200');
        // });
        // // poner "activo" al botón clickeado
        // const activeBtn = event.target;
        // activeBtn.classList.remove('bg-gray-200', 'dark:bg-gray-600', 'dark:text-gray-200');
        // activeBtn.classList.add('bg-gray-300','dark:bg-gray-500', 'dark:text-gray-200');
        applyFilters();
    }

    window.toggleSort = function() {
        sortAsc = !sortAsc;
        applyFilters();
    }

    window.applyFilters = function() {
        const search = document.getElementById('searchInput')?.value.toLowerCase() || '';
        const rows = document.querySelectorAll('tbody tr, .rounded.shadow-md');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            const playaId = row.dataset.playa;

            let visible = true;

            if (currentPlaya !== 'all' && playaId !== currentPlaya) {
                visible = false;
            }

            if (search && !text.includes(search)) {
                visible = false;
            }

            row.style.display = visible ? '' : 'none';
        });

        // ordenar visibles
         const tbody = document.querySelector('tbody');
        if(tbody) {
            const visibles = rows.filter(r => r.style.display !== 'none');

            visibles.sort((a, b) => {
                const da = new Date(a.dataset.fecha);
                const db = new Date(b.dataset.fecha);
                return sortAsc ? da - db : db - da;
            });


            visibles.forEach(r => tbody.appendChild(r));
        }

        // sorted.forEach(r => tbody.appendChild(r));
    }

 });
