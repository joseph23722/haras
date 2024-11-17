

// Función para cargar el historial del herrero en la tabla
function cargarHistorialHerrero() {
    fetch('/haras/controllers/herrero.controller.php?operation=consultarHistorialEquino&idEquino=1') // Cambia "1" por el ID dinámico del equino si es necesario
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const historialTable = document.querySelector('#historialHerreroTable tbody');
                historialTable.innerHTML = '';

                data.data.forEach(historial => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${historial.nombreEquino}</td>
                        <td>${historial.trabajoRealizado}</td>
                        <td>${historial.herramientasUsadas}</td>
                        <td>${historial.estadoInicio}</td>
                        <td>${historial.estadoFin}</td>
                        <td>${historial.observaciones}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="actualizarEstadoFinal(${historial.idHistorialHerrero})">Actualizar Estado</button>
                        </td>
                    `;
                    historialTable.appendChild(row);
                });
            }
        })
        .catch(error => console.error('Error al cargar historial de herrero:', error));
}

// Función para actualizar el estado final de una herramienta
function actualizarEstadoFinal(idHistorialHerrero) {
    Swal.fire({
        title: 'Actualizar Estado Final',
        input: 'select',
        inputOptions: {
            1: 'En buen estado',
            2: 'Desgastada',
            3: 'Necesita reparación'
        },
        inputPlaceholder: 'Seleccione el estado final',
        showCancelButton: true,
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/haras/controllers/herrero.controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    operation: 'actualizarEstadoFinalHerramientaUsada',
                    idHerramientasUsadas: idHistorialHerrero,
                    estadoFin: result.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Actualizado', data.message, 'success');
                    cargarHistorialHerrero();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => console.error('Error al actualizar estado final:', error));
        }
    });
}
