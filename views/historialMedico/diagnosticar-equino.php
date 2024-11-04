<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Registro de Historial Médico</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Historial Médico</h5>
        </div>

        <!-- Formulario -->
        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-historial-medico" autocomplete="off">
                <div class="row g-3">
                    <!-- Selector para el Tipo de Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="tipoEquinoSelect" class="form-select" name="tipoEquino" required>
                                <option value="">Seleccione Tipo de Equino</option>
                                <option value="1">Yegua</option>
                                <option value="2">Padrillo</option>
                                <option value="3">Potranca</option>
                                <option value="4">Potrillo</option>
                            </select>
                            <label for="tipoEquinoSelect"><i class="fas fa-horse" style="color: #00b4d8;"></i> Tipo de Equino</label>
                        </div>
                    </div>
                    
                    <!-- Selector para el nombre del Tipo de Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="equinoSelect" class="form-select" name="idEquino" required>
                                <option value="">Seleccione Equino</option>
                            </select>
                            <label for="equinoSelect"><i class="fas fa-horse" style="color: #00b4d8;"></i> Equino</label>
                        </div>
                    </div>


                    

                    <!-- Select de Medicamentos -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="selectMedicamento" class="form-select" name="idMedicamento" required>
                                <option value="">Seleccione Medicamento</option>
                            </select>
                            <label for="selectMedicamento"><i class="fas fa-pills" style="color: #ffa500;"></i> Medicamento</label>
                        </div>
                    </div>

                    <!-- Tipo de Tratamiento (Primario o Complementario) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="tipoTratamiento" id="tipoTratamiento" class="form-select" required>
                                <option value="">Seleccione Tipo de Tratamiento</option>
                                <option value="Primario">Primario</option>
                                <option value="Complementario">Complementario</option>
                            </select>
                            <label for="tipoTratamiento"><i class="fas fa-list-alt" style="color: #ff8c00;"></i> Tipo de Tratamiento</label>
                        </div>
                    </div>

                    <!-- Fecha Fin -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fechaFin" id="fechaFin" class="form-control" required>
                            <label for="fechaFin"><i class="fas fa-calendar-alt" style="color: #ba55d3;"></i> Fecha de Fin</label>
                        </div>
                    </div>

                    <!-- Dosis -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="dosis" id="dosis" placeholder="" class="form-control" required>
                            <label for="dosis"><i class="fas fa-syringe" style="color: #ff6347;"></i> Dosis</label>
                        </div>
                    </div>


                    <!-- Peso Equino (opcional) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" step="0.01" name="pesoEquino" id="pesoEquino" class="form-control">
                            <label for="pesoEquino"><i class="fas fa-weight" style="color: #ff6f61;"></i> Peso Equino (kg)</label>
                        </div>
                    </div>

                    <!-- Frecuencia de Administración -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="frecuenciaAdministracion" id="frecuenciaAdministracion" class="form-control" required>
                            <label for="frecuenciaAdministracion"><i class="fas fa-stopwatch" style="color: #6a5acd;"></i> Frecuencia de Administración</label>
                        </div>
                    </div>

                    <!-- Vía de Administración -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="viaAdministracion" id="viaAdministracion" class="form-select" required>
                                <option value="">Seleccione Vía de Administración</option>
                                <option value="Oral">Oral - Administración por la boca</option>
                                <option value="Intramuscular">Intramuscular - Inyección en el músculo</option>
                                <option value="Intravenosa">Intravenosa - Inyección en la vena</option>
                                <option value="Subcutánea">Subcutánea - Inyección debajo de la piel</option>
                                <option value="Intranasal">Intranasal - Administración en las fosas nasales</option>
                                <option value="Tópica">Tópica - Aplicación en la piel o mucosa</option>
                            </select>
                            <label for="viaAdministracion"><i class="fas fa-route" style="color: #00b4d8;"></i> Vía de Administración</label>
                        </div>
                    </div>


                    <!-- Observaciones -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <textarea name="observaciones" id="observaciones" class="form-control" style="height: 100px;"></textarea>
                            <label for="observaciones"><i class="fas fa-notes-medical" style="color: #007bff;"></i> Observaciones</label>
                        </div>
                    </div>

                    <!-- Reacciones Adversas (opcional) -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <textarea name="reaccionesAdversas" id="reaccionesAdversas" class="form-control" style="height: 100px;"></textarea>
                            <label for="reaccionesAdversas"><i class="fas fa-exclamation-circle" style="color: #dc3545;"></i> Reacciones Adversas</label>
                        </div>
                    </div>

                    <div id="mensaje" style="margin-top: 10px; color: green; font-weight: bold;"></div>

                    <!-- Botones -->
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="registrar-historial" style="background-color: #0077b6; border: none;">
                            <i class="fas fa-save"></i> Registrar Historial
                        </button>
                        <button type="reset" class="btn btn-secondary btn-lg shadow-sm" style="background-color: #adb5bd; border: none;">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Tabla para DataTable -->
    <div class="card mt-4">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Historiales Médicos</h5>
        </div>
        <div class="card-body">
            <table id="historialTable" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th>Equino</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Medicamento</th>
                    <th>Dosis</th>
                    <th>Frecuencia</th>
                    <th>Vía</th>
                    <th>Peso (kg)</th>
                    <th>Registro</th>
                    <th>Fin</th>
                    <th>Observaciones</th>
                    <th>Reacciones</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                    <!-- Se llenará dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Incluye SweetAlert -->
<script src="../../swalcustom.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        let historialTable;

        let selectedEquinoId = null;
        const tipoEquinoSelect = document.getElementById("tipoEquino");
        const equinoSelect = document.getElementById("equino");
        const medicamentoSelect = document.querySelector("#selectMedicamento");
        const fechaFinInput = document.querySelector("#fechaFin");

        const mensajeDiv = document.querySelector("#mensaje");  // Div para mostrar los mensajes dinámicos

        // **Función para mostrar notificaciones en el div `mensaje`**
        // **Función para mostrar notificaciones en el div `mensaje`**
        const mostrarMensajeDinamico = (mensaje, tipo = 'INFO') => {
            const mensajeDiv = document.getElementById('mensaje'); // Asegúrate de tener un div con el id 'mensaje'
            
            if (mensajeDiv) {
                // Colores y iconos según el tipo de mensaje
                const estilos = {
                    'INFO': { color: '#3178c6', bgColor: '#e7f3ff', icon: 'ℹ️' },
                    'SUCCESS': { color: '#3c763d', bgColor: '#dff0d8', icon: '✅' },
                    'ERROR': { color: '#a94442', bgColor: '#f2dede', icon: '❌' },
                    'WARNING': { color: '#8a6d3b', bgColor: '#fcf8e3', icon: '⚠️' }
                };

                // Obtener los estilos correspondientes al tipo de mensaje
                const estilo = estilos[tipo] || estilos['INFO'];

                // Aplicar estilos al contenedor del mensaje
                mensajeDiv.style.color = estilo.color;
                mensajeDiv.style.backgroundColor = estilo.bgColor;
                mensajeDiv.style.fontWeight = 'bold';
                mensajeDiv.style.padding = '15px';
                mensajeDiv.style.marginBottom = '15px';
                mensajeDiv.style.border = `1px solid ${estilo.color}`;
                mensajeDiv.style.borderRadius = '8px';
                mensajeDiv.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
                mensajeDiv.style.display = 'flex';
                mensajeDiv.style.alignItems = 'center';

                // Mostrar el mensaje con un icono
                mensajeDiv.innerHTML = `<span style="margin-right: 10px; font-size: 1.2em;">${estilo.icon}</span>${mensaje}`;

                // Eliminar el mensaje después de 5 segundos
                setTimeout(() => {
                    mensajeDiv.innerHTML = '';
                    mensajeDiv.style.border = 'none';
                    mensajeDiv.style.boxShadow = 'none';
                    mensajeDiv.style.backgroundColor = 'transparent';
                }, 5000);
            } else {
                console.warn('El contenedor de mensajes no está presente en el DOM.');
            }
        };

        

        // Restringir la fecha de fin a hoy y futuras
        fechaFinInput.min = new Date().toISOString().split("T")[0];

        // Función para cargar la tabla de historial médico
        const loadHistorialTable = async () => {
            if (!$.fn.DataTable.isDataTable('#historialTable')) {
                historialTable = $('#historialTable').DataTable({
                    ajax: {
                        url: '../../controllers/historialme.controller.php',
                        type: 'GET',
                        data: { operation: 'consultarHistorialMedico' },
                        dataSrc: 'data',
                        complete: function() {
                            console.log("Datos cargados exitosamente en DataTable de historial.");
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error("Error en DataTable AJAX:", textStatus, errorThrown);
                        }
                    },
                    columns: [
                        { data: 'nombreEquino' },
                        { data: 'tipoTratamiento' },
                        { data: 'estadoTratamiento' },
                        { data: 'nombreMedicamento' },
                        { data: 'dosis' },
                        { data: 'frecuenciaAdministracion' },
                        { data: 'viaAdministracion' },
                        { data: 'pesoEquino' },
                        { data: 'fechaInicio' },
                        { data: 'fechaFin' },
                        { 
                            data: 'observaciones', 
                            render: function(data) {
                                return data ? data : 'Ninguna';
                            }
                        },
                        { 
                            data: 'reaccionesAdversas', 
                            render: function(data) {
                                return data ? data : 'Ninguna';
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            render: function(data, type, row) {
                                return `
                                    <div class="btn-group" role="group" aria-label="Acciones">
                                        <button class="btn btn-sm btn-warning" onclick="pausarRegistro(${row.idRegistro})" title="Pausar">
                                            <i class="fas fa-pause-circle"></i>
                                        </button>
                                        <button class="btn btn-sm btn-success" onclick="continuarRegistro(${row.idRegistro})" title="Continuar">
                                            <i class="fas fa-play-circle"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="eliminarRegistro(${row.idRegistro})" title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    ],
                    language: {
                        url: '/haras/data/es_es.json'
                    }
                });
            } else {
                historialTable.ajax.reload(); // Recargar los datos si la tabla ya está inicializada
            }
        };

        // Función genérica para enviar la solicitud al servidor
        const sendRequest = async (idRegistro, accion) => {
            const data = {
                operation: 'gestionarTratamiento',
                idRegistro: idRegistro,
                accion: accion
            };

            console.log("Enviando datos al servidor:", JSON.stringify(data));

            try {
                const response = await fetch('../../controllers/historialme.controller.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                console.log(`Respuesta del servidor (${accion}):`, result);

                if (result.status === "success") {
                    mostrarMensajeDinamico(`Registro ${accion} exitosamente`);
                    $('#historialTable').DataTable().ajax.reload();
                } else {
                    mostrarMensajeDinamico(`Error al ${accion} el registro: ` + (result.message || "Error desconocido"));
                }
            } catch (error) {
                console.error(`Error al ${accion} el registro:`, error);
            }
        };

        // Llamadas para cada acción
        const pausarRegistro = (idRegistro) => sendRequest(idRegistro, 'pausar');
        const continuarRegistro = (idRegistro) => sendRequest(idRegistro, 'continuar');
        const eliminarRegistro = (idRegistro) => sendRequest(idRegistro, 'eliminar');


        // Adjuntar las funciones a botones
        window.pausarRegistro = pausarRegistro;
        window.continuarRegistro = continuarRegistro;
        window.eliminarRegistro = eliminarRegistro;


       
        // Función para cargar la lista de equinos
        // Función para cargar los equinos según el tipo seleccionado
        async function loadEquinosPorTipo(tipoEquino) {
            const equinoSelect = document.getElementById("equinoSelect");

            try {
                const response = await fetch(`../../controllers/historialme.controller.php?operation=listarEquinosPorTipo`, {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });

                const data = await response.json();

                // Limpiar el selector de equinos
                equinoSelect.innerHTML = '<option value="">Seleccione Equino</option>';

                // Verificar si se obtuvieron datos
                if (data.data && Array.isArray(data.data)) {
                    data.data.forEach(equino => {
                        if (equino.idTipoEquino == tipoEquino) {
                            const option = document.createElement('option');
                            option.value = equino.idEquino;
                            option.textContent = equino.nombreEquino;
                            equinoSelect.appendChild(option);
                        }
                    });
                } else {
                    showToast("No se encontraron equinos para el tipo seleccionado.", "WARNING");
                }
            } catch (error) {
                showToast("Error al cargar los equinos", "ERROR");
                console.error("Error:", error);
            }
        }

        // Evento para actualizar el select de equinos cuando cambia el tipo de equino
        document.getElementById("tipoEquinoSelect").addEventListener("change", function() {
            const tipoEquino = this.value;
            if (tipoEquino) {
                loadEquinosPorTipo(tipoEquino);
            } else {
                // Limpiar si no hay tipo seleccionado
                document.getElementById("equinoSelect").innerHTML = '<option value="">Seleccione Equino</option>';
            }
        });


        // Función para cargar la lista de medicamentos
        async function loadMedicamentos() {
            try {
                const response = await fetch('../../controllers/historialme.controller.php?operation=getAllMedicamentos', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });

                const data = await response.json();
                console.log("Respuesta del servidor:", data);

                if (data.data && Array.isArray(data.data)) {
                    medicamentoSelect.innerHTML = '<option value="">Seleccione Medicamento</option>';
                    data.data.forEach(medicamento => {
                        const option = document.createElement('option');
                        option.value = medicamento.idMedicamento;
                        option.textContent = medicamento.nombreMedicamento;
                        medicamentoSelect.appendChild(option);
                    });
                } else {
                    showToast("No se encontraron medicamentos.", "WARNING");
                }
            } catch (error) {
                console.error("Error al cargar los medicamentos:", error);
                showToast("Error al cargar los medicamentos", "ERROR");
            }
        }

        // Función para enviar el formulario de registro de historial médico
        document.querySelector("#form-historial-medico").addEventListener("submit", async (event) => {
            event.preventDefault();

            // Convertir el formulario a un objeto JavaScript
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData.entries());
            data.operation = "registrarHistorialMedico";

            console.log("Datos enviados al servidor:", JSON.stringify(data, null, 2));

            try {
                const response = await fetch('../../controllers/historialme.controller.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data) // Enviar los datos como JSON
                });

                const result = await response.json();
                console.log("Respuesta JSON parseada:", result);

                if (result.status === "success") {
                    showToast(result.message || "Historial médico registrado correctamente", "SUCCESS");
                    mostrarMensajeDinamico(result.message || "Historial médico registrado correctamente", "SUCCESS");
                    event.target.reset();
                    $('#historialTable').DataTable().ajax.reload();

                    [selectYegua, selectPadrillo, selectPotrillo, selectPotranca].forEach(select => select.disabled = false);
                } else {
                    mostrarMensajeDinamico("Error al registrar el historial: " + (result.message || "Desconocido"), "ERROR");
                }
            } catch (error) {
                console.error("Error al registrar el historial médico:", error);
                mostrarMensajeDinamico("Error al registrar el historial médico: Error de conexión o error inesperado", "ERROR");
            }
        });


        // Función para limpiar los selects de equinos
        function clearSelect(selectElement) {
            selectElement.innerHTML = `<option value="">Seleccione ${selectElement.id.replace('select', '')}</option>`;
        }

        // Cargar equinos, medicamentos y la tabla de historial médico al iniciar
        loadMedicamentos();
        loadHistorialTable();
    });
</script>


