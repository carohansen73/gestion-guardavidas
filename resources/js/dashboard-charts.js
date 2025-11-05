import Chart from 'chart.js/auto';

"use strict"

document.addEventListener('DOMContentLoaded', e => {

    let chartBanderas = null;

    async function cargarDashboard(playaId = '') {
        const url = `/api/dashboard?playa=${playaId}`;
        const res = await fetch(url);
        const data = await res.json();

        // Actualiza las cards
        document.getElementById('card-intervenciones').textContent = data.totalIntervenciones;
        document.getElementById('card-novedades').textContent = data.totalNovedadesMateriales;

        // Actualiza los porcentajes
        mostrarPorcentajes(data.intervencionesPorPlaya, playaId, "porcentajeIntervencionesPorPlaya");
        mostrarPorcentajes(data.novedadesMaterialesPorPlaya, playaId, "porcentajeNovedadesPorPlaya");

        // Actualiza el gráfico
        const ctx = document.getElementById('graficoBanderas');
        const labels = data.banderas.map(b => b.color);
        const valores = data.banderas.map(b => b.total);

         const colorPorBandera = {
            'bandera-bueno': '#0ea5e9',
            'bandera-dudoso': '#eab308',
            'bandera-peligroso': '#e93434ff',
            'bandera-rayos': '#000000',
            'bandera-prohibido': '#b91c1c',
            'bandera-perdido': '#f1f0f0ff',

        };

        const backgroundColors = labels.map(color => colorPorBandera[color] || '#9ca3af');

        if (chartBanderas) chartBanderas.destroy();

        chartBanderas = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: valores,
                    backgroundColor: backgroundColors
                }]
            },
            options: {
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }


    //Actualiza porcentajes por playa
    function mostrarPorcentajes(lista, playaId, contenedorId) {
        const contenedor = document.getElementById(contenedorId);
        contenedor.innerHTML = "";

        if (!lista || lista.length === 0) return;

        // Si no hay filtro, mostrar todas
        if (!playaId) {
            lista.forEach(item => {
                const div = crearBadge(item.playa.color, item.sigla, item.porcentaje);
                contenedor.appendChild(div);
            });
        } else {
            // Mostrar solo la playa seleccionada
            const playa = lista.find(i => i.playa_id == playaId);
            if (playa) {
                const div = crearBadge(playa.playa.color, playa.sigla, playa.porcentaje);
                contenedor.appendChild(div);
            }
        }
    }

    function crearBadge(colorClase, sigla, porcentaje) {
        const div = document.createElement('div');
        div.className = `flex items-center gap-1 ${colorClase} bg-gray-100 rounded-full bg-error-50 py-0.5 px-2 my-1 text-xs font-medium  dark:bg-error-500/15 dark:text-error-500`;
        div.innerHTML = `
            <span class="px-1 text-sm">${sigla}</span>
            ${porcentaje}%
        `;
        return div;
    }

   // Selecciona todos los botones de filtro
document.querySelectorAll('.btn-filtro').forEach(boton => {
    boton.addEventListener('click', e => {
        const playaId = e.target.getAttribute('data-playa');
        const playaNombre = e.target.getAttribute('data-nombre');
        cargarDashboard(playaId);
        actualizarBotonActivo(playaId);
        actualizarTituloPlaya(playaNombre);
    });
});

    // Marca visualmente el botón activo
    function actualizarBotonActivo(playaId) {
        document.querySelectorAll('.btn-filtro').forEach(btn => {
            if (btn.getAttribute('data-playa') === playaId) {
                btn.classList.add('bg-sky-600', 'text-white');
                btn.classList.remove('bg-gray-200', 'text-gray-800');
            } else {
                btn.classList.remove('bg-sky-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-800');
            }
        });
    }

    // Marca visualmente el botón activo
    function actualizarTituloPlaya(playaNombre) {
        document.querySelectorAll('.tituloPlayaSeleccionada').forEach(i => {
            if (playaNombre === 'Todas' || !playaNombre) {
            i.innerHTML = 'Todas las playas';
            } else{
                i.innerHTML = playaNombre;
            }
        });
    }

    // Carga inicial
    cargarDashboard();

});







// 'totalIntervenciones' => $totalIntervenciones,
//             'intervencionesPorPlaya' => $intervencionesPorPlaya,
//             'totalNovedadesMateriales' => $totalNovedadesMateriales,
//             'novedadesMaterialesPorPlaya' => $novedadesMaterialesPorPlaya,
//             'banderas' => $banderasQuery
