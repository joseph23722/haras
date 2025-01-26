document.addEventListener("DOMContentLoaded", () => {
    const estadoMontaSelect = document.getElementById("estadoMonta");

    function obtenerEstadosMonta() {
        fetch("../../controllers/registrarequino.controller.php?operation=listadoEstadoMonta")
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    data.forEach(estado => {
                        const option = document.createElement("option");
                        option.value = estado.idEstadoMonta;
                        option.textContent = estado.nombreEstado;
                        estadoMontaSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error("Error al cargar los estados de monta:", error);
            });
    }
    obtenerEstadosMonta();

    let datosEquinos = [];
    let simpleTable;

    // Obtener los datos de los equinos
    function obtenerDatos(estadoMonta = "") {
        const url = estadoMonta
            ? `../../controllers/registrarequino.controller.php?operation=getAll&estadoMonta=${estadoMonta}`
            : `../../controllers/registrarequino.controller.php?operation=getAll`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                datosEquinos = data;
                let tabla = document.querySelector('#tabla-equinos tbody');
                tabla.innerHTML = '';

                if (data.length > 0) {
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
                            <td>${element.idEquino}</td>
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
                                <button class="btn btn-sm btn-success historial" data-idequino="${element.idEquino}" title="Historial" data-bs-toggle="modal" data-bs-target="#historialModal">
                                    <i class="fas fa-file-alt" style="font-size: 12px;"></i> Historial.
                                </button>
                            </td>
                        </tr>`;
                        tabla.innerHTML += nuevaFila;
                    });

                    // Inicializar DataTable si aún no se ha inicializado
                    if (!simpleTable) {
                        simpleTable = new simpleDatatables.DataTable("#tabla-equinos", {
                            perPage: 10,
                            searchable: true,
                            sortable: true
                        });
                    } else {
                        // Si ya está inicializado, recargamos la tabla para mantener el buscador
                        simpleTable.update();
                    }

                    // Restaurar el color de la cabecera
                    const encabezado = document.querySelector("#tabla-equinos thead");
                    if (encabezado) {
                        encabezado.style.backgroundColor = '#caf0f8';
                        encabezado.style.color = '#fff';
                    }
                } else {
                    tabla.innerHTML = `<tr><td colspan="10" class="text-center">No se encontraron resultados</td></tr>`;
                    if (simpleTable) {
                        simpleTable.update();
                    }
                }
            })
            .catch(error => {
                console.error("Error al obtener los datos:", error);
            });
    }

    // Mostrar historial usando datos precargados
    function mostrarHistorial(idEquino) {
        const equinoData = datosEquinos.find(equino => equino.idEquino === parseInt(idEquino));
        const historialModalBody = document.getElementById('historialModalBody');

        if (!equinoData) {
            historialModalBody.innerHTML = '<p>No se encontró información para este equino.</p>';
            return;
        }

        let historialHtml = '<ul>';
        // Mostrar la descripción de historial
        if (equinoData.descripcion) {
            historialHtml += `<li><strong>Descripción:</strong> ${equinoData.descripcion}</li>`;
        }

        // Mostrar historial
        if (equinoData.historial && Array.isArray(equinoData.historial) && equinoData.historial.length > 0) {
            equinoData.historial.forEach(historial => {
                historialHtml += `<li>${historial.descripcion}</li>`;
            });
            historialHtml += '</ul>';
        } else {
            if (!equinoData.descripcion) {
                historialHtml += '<li>No hay historial disponible.</li>';
            }
            historialHtml += '</ul>';
        }

        // Mostrar foto del equino
        if (equinoData.fotografia) {
            historialHtml += `
                <div class="text-center mt-3">
                    <img src="https://res.cloudinary.com/dtbhq7drd/image/upload/${equinoData.fotografia}" alt="Foto del Equino" class="img-fluid rounded" style="max-width: 100%; height: auto;" />
                </div>`;
        } else {
            historialHtml += `
                <div class="text-center mt-3">
                    <img src="https://via.placeholder.com/150" alt="Imagen de Prueba" class="img-fluid rounded" style="max-width: 100%; height: auto;" />
                </div>`;
        }

        historialModalBody.innerHTML = historialHtml;
    }

    // Manejo del clic en el botón "Historial"
    document.querySelector('body').addEventListener('click', (event) => {
        if (event.target && event.target.matches('button.historial')) {
            const idEquino = event.target.getAttribute('data-idequino');

            mostrarHistorial(idEquino);
            const historialModal = new bootstrap.Modal(document.getElementById('historialModal'));
            historialModal.show();
        }
    });
    obtenerDatos();
});