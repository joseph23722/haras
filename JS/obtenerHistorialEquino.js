document.addEventListener("DOMContentLoaded", () => {
    // Variable para almacenar los datos de los equinos cargados
    let datosEquinos = [];

    async function obtenerDatos() {
        try {
            const response = await fetch(`../../controllers/registrarequino.controller.php?operation=getAll`, {
                method: 'GET'
            });
            const data = await response.json();
            console.log(data);

            datosEquinos = data;

            if (data.length > 0) {
                let numeroFila = 1;
                let tabla = $('#tabla-equinos tbody');
                tabla.empty();

                // Agregar filas a la tabla
                data.forEach(element => {
                    let colorTexto = '';
                    let estado = element.estadoDescriptivo || '';
                    if (estado === 'Vivo') {
                        colorTexto = 'color: #28a745;';
                    } else if (estado === 'Muerto') {
                        colorTexto = 'color: #dc3545;';
                    } else {
                        colorTexto = 'color: #6c757d;';
                    }

                    const nuevaFila = `
                <tr data-idequino="${element.idEquino}">
                    <td>${numeroFila}</td>
                    <td>${element.nombreEquino}</td>
                    <td>${element.fechaNacimiento}</td>
                    <td>${element.sexo}</td>
                    <td>${element.tipoEquino}</td>
                    <td>${element.detalles || 'Sin detalles'}</td>
                    <td>${element.nombreEstado || 'Sin estado'}</td>
                    <td>${element.pesokg}</td>
                    <td>${element.nacionalidad}</td>
                    <td style="${colorTexto}">${estado}</td>
                    <td>
                        <button class="btn btn-sm btn-primary edit" data-idequino="${element.idEquino}" title="Editar">
                            <i class="fas fa-edit" style="font-size: 16px;"></i>
                        </button>
                        <button class="btn btn-sm btn-success historial" data-idequino="${element.idEquino}" title="Historial" data-bs-toggle="modal" data-bs-target="#historialModal">
                            <i class="fas fa-file-alt" style="font-size: 16px;"></i>
                        </button>
                    </td>
                </tr>`;
                    numeroFila++;
                    tabla.append(nuevaFila);
                });

                $('#tabla-equinos').DataTable();
            }
        } catch (error) {
            console.error("Error al obtener los datos:", error);
        }
    }

    // Función para obtener el historial del equino
    async function obtenerHistorial(idEquino) {
        try {
            const historialModalBody = document.getElementById('historialModalBody');
            historialModalBody.innerHTML = '<p>Cargando...</p>';

            // Buscar el historial desde los datos precargados
            const equinoData = datosEquinos.find(equino => equino.idEquino === parseInt(idEquino));
            if (!equinoData) {
                historialModalBody.innerHTML = '<p>No se encontró información para este equino.</p>';
                return;
            }

            // Cargar historial desde el servidor
            const response = await fetch(`../../controllers/registrarequino.controller.php?operation=getHistorial&idEquino=${idEquino}`);
            const result = await response.json();

            // Mostrar historial
            if (result && Array.isArray(result) && result.length > 0) {
                let historialHtml = '<ul>';
                result.forEach(historial => {
                    historialHtml += `<li>${historial.descripcion}</li>`;
                });
                historialHtml += '</ul>';

                // Foto del equino
                if (equinoData.fotografia) {
                    historialHtml += `
                    <div class="text-center mt-3">
                        <img src="${equinoData.fotografia}" alt="Foto del Equino" class="img-fluid rounded" style="max-width: 100%; height: auto;" />
                    </div>
                `;
                } else {
                    historialHtml += `
                    <div class="text-center mt-3">
                        <img src="https://via.placeholder.com/150" alt="Imagen de Prueba" class="img-fluid rounded" style="max-width: 100%; height: auto;" />
                    </div>
                `;
                }

                historialModalBody.innerHTML = historialHtml;
            } else {
                historialModalBody.innerHTML = '<p>No hay historial disponible para este equino.</p>';
            }
        } catch (error) {
            console.error("Error al obtener el historial:", error);
            const historialModalBody = document.getElementById('historialModalBody');
            historialModalBody.innerHTML = '<p>Error al cargar el historial. Intente nuevamente.</p>';
        }
    }

    // Manejar clic en el botón de historial
    document.querySelector('body').addEventListener('click', (event) => {
        if (event.target && event.target.matches('button.historial')) {
            const idEquino = event.target.getAttribute('data-idequino');
            console.log('ID Equino:', idEquino);

            const historialModalBody = document.getElementById('historialModalBody');
            historialModalBody.innerHTML = '';

            obtenerHistorial(idEquino);

            const historialModal = new bootstrap.Modal(document.getElementById('historialModal'));
            historialModal.show();
        }
    });

    // Limpiar el modal y recargar los datos cuando se cierra el modal
    const historialModal = document.getElementById('historialModal');
    historialModal.addEventListener('hidden.bs.modal', () => {
        const historialModalBody = document.getElementById('historialModalBody');
        historialModalBody.innerHTML = '';

        obtenerDatos();
    });

    obtenerDatos();
});