// "use strict"

// document.addEventListener('DOMContentLoaded', e => {

//     let guardavidas = [];
//     getAllGuardavidas().then(data => {
//         if (data) {
//             guardavidas = data;
//             console.log("Guardavidas cargados:", guardavidas);
//         }
//     });

//     async function getAllGuardavidas() {
//         try {
//             const response = await fetch('/get-all-guardavidas', {
//                 method: 'get'
//             });
//             const data = await response.json();
//             return data;
//         } catch(error){
//             console.log(error);
//             return null;
//         }
//     }



/* con Alpine - NO ANDA"!*/
// function multiSelect() {
//     return {
//         open: false,
//         selected: [],
//         opciones: [],

//         async init() {
//             try {
//                 const response = await fetch("{{ url('get-all-guardavidas') }}");
//                 this.opciones = await response.json();
//             } catch (error) {
//                 console.error("Error cargando guardavidas", error);
//                 this.opciones = [];
//             }
//         },

//         toggle(item) {
//             let exists = this.selected.find(s => s.id === item.id);
//             if (!exists) {
//                 this.selected.push(item);
//             } else {
//                 this.selected = this.selected.filter(s => s.id !== item.id);
//             }
//         },

//         remove(index) {
//             this.selected.splice(index, 1);
//         }
//     }
// }

// // 👇 Esto lo expone al navegador
// window.multiSelect = multiSelect;


document.addEventListener('DOMContentLoaded', () => {
    const playaSelect = document.getElementById('playa_id');
    const puestoSelect = document.getElementById('puesto_id');
    const allPuestos = Array.from(puestoSelect.options);

    function filterPuestos() {
        const selectedPlaya = playaSelect.value;
        const currentPuesto = puestoSelect.dataset.current; // ← lo agregamos abajo

        // Limpiar select
        puestoSelect.innerHTML = '';

        // Agregar solo los puestos de la playa seleccionada
        allPuestos.forEach(option => {
            if (option.dataset.playa == selectedPlaya) {
                puestoSelect.appendChild(option);
            }
        });

        // Mantener seleccionado el puesto actual si existe
        const currentOption = Array.from(puestoSelect.options).find(opt => opt.value === currentPuesto);
        if (currentOption) {
            currentOption.selected = true;
        } else if (puestoSelect.options.length > 0) {
            puestoSelect.selectedIndex = 0;
        }
    }

    // Guardar el puesto actual en un atributo data
    puestoSelect.dataset.current = puestoSelect.value;

    // Filtrar al cargar y al cambiar la playa
    filterPuestos();
    playaSelect.addEventListener('change', filterPuestos);
});
