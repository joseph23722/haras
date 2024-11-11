document.addEventListener('DOMContentLoaded', function() {
    // Cargar los datos iniciales al cargar la página
    cargarTiposEquinos();
    cargarHerramientas();
    cargarHistorialHerrero();

    // Definir primero la función
    function cargarEquinosPorTipo(tipoEquinoId) {
        console.log(`Cargando equinos para el tipo con ID ${tipoEquinoId}`);
        fetch(`/haras/controllers/herrero.controller.php?operation=getEquinosByTipo&idTipoEquino=${tipoEquinoId}`)
            .then(response => response.json())
            .then(data => {
                console.log("Datos de equinos recibidos:", data);
                const equinoSelect = document.getElementById('equinoSelect');
                equinoSelect.innerHTML = '<option value="">Seleccione Equino</option>'; // Limpia las opciones anteriores
    
                if (data.status === 'success' && data.data.length > 0) {
                    data.data.forEach(equino => {
                        const option = document.createElement('option');
                        option.value = equino.idEquino;  // Asegúrate de que idEquino exista en los datos
                        option.textContent = equino.nombre;  // Asegúrate de que nombre exista en los datos
                        equinoSelect.appendChild(option);
                        console.log("Equino agregado al selector:", equino.nombre); // Log para confirmar
                    });
                } else {
                    console.log('No se encontraron equinos para este tipo.');
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No hay equinos disponibles para este tipo';
                    equinoSelect.appendChild(option);
                }
            })
            .catch(error => console.error('Error al cargar equinos:', error));
    }
    
    
    
    

    // Luego, agregar el evento DOMContentLoaded que la llame
    document.addEventListener('DOMContentLoaded', function() {
        // Llamadas a las funciones para cargar datos iniciales
        cargarTiposEquinos(); // Llamada a la función definida arriba
        cargarHerramientas();
        cargarHistorialHerrero();

        // Resto del código para manejar eventos
        document.getElementById('form-historial-herrero').addEventListener('submit', function(event) {
            event.preventDefault();
            registrarHistorialHerrero();
        });

        document.getElementById('tipoEquinoSelect').addEventListener('change', function() {
            const tipoEquinoId = this.value;
            console.log(`ID de tipo seleccionado: ${tipoEquinoId}`);
            cargarEquinosPorTipo(tipoEquinoId);
        });
        
    });


});






// Función para cargar herramientas disponibles
function cargarHerramientas() {
    fetch('/haras/controllers/herrero.controller.php?operation=getHerramientas')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const herramientasSelect = document.getElementById('herramientasUsadas');
                data.data.forEach(herramienta => {
                    const option = document.createElement('option');
                    option.value = herramienta.idHerramienta;
                    option.textContent = herramienta.nombre;
                    herramientasSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error al cargar herramientas:', error));
}

// Función para registrar un nuevo historial de herrero
function registrarHistorialHerrero() {
    const formData = new FormData(document.getElementById('form-historial-herrero'));
    formData.append('operation', 'insertarHistorialHerrero');
    
    const datos = {};
    formData.forEach((value, key) => datos[key] = value);

    fetch('/haras/controllers/herrero.controller.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire('Registrado', data.message, 'success');
            document.getElementById('form-historial-herrero').reset();
            cargarHistorialHerrero(); // Recargar el historial
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => console.error('Error al registrar historial:', error));
}

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
