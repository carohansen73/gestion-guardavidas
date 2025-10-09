"use strict"

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
    //Ocultar/Mostrar svg de busqueda
    // const icon = document.getElementById('searchIcon');
    // document.getElementById('searchInput').addEventListener('input', toggleSearchSvg)


    window.applyFilters = function() {
        const search = document.getElementById('searchInput')?.value.toLowerCase() || '';

        //Filtro Lista para mobile
        const cards = document.querySelectorAll('.registro-item-lista');

          cards.forEach(card => {
            const text = card.innerText.toLowerCase();
            const playaId = card.dataset.playa;
            let visible = true;

            if (currentPlaya !== 'all' && playaId !== currentPlaya) {
                visible = false;
            }

            if (search && !text.includes(search)) {
                visible = false;
            }

            card.style.display = visible ? '' : 'none';
        });


        //Filtro para table
        const rows = document.querySelectorAll('.registro-item-tabla');
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

        // ordenar tabla
        const tbody = document.querySelector('tbody');
        if(tbody) {
            const visibles = Array.from(rows).filter(r => r.style.display !== 'none');

            visibles.sort((a, b) => {
                const da = new Date(a.dataset.fecha);
                const db = new Date(b.dataset.fecha);
                return sortAsc ? da - db : db - da;
            });


            visibles.forEach(r => tbody.appendChild(r));
        }

        // sorted.forEach(r => tbody.appendChild(r));
    }

    // function toggleSearchSvg(event){
    //     if (event.target.value.trim() === ''){
    //         icon.classList.remove('hidden');
    //     } else{
    //         icon.classList.add('hidden');
    //     }

    // }



 });
