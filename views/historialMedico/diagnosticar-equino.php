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
                    <!-- Selectores de Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="selectYegua" class="form-select equino-select" data-tipo="yegua" name="idEquino" required>
                                <option value="">Seleccione Yegua</option>
                            </select>
                            <label for="selectYegua"><i class="fas fa-horse" style="color: #00b4d8;"></i> Yegua</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="selectPadrillo" class="form-select equino-select" data-tipo="padrillo" name="idEquino">
                                <option value="">Seleccione Padrillo</option>
                            </select>
                            <label for="selectPadrillo"><i class="fas fa-horse" style="color: #00b4d8;"></i> Padrillo</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="selectPotrillo" class="form-select equino-select" data-tipo="potrillo" name="idEquino">
                                <option value="">Seleccione Potrillo</option>
                            </select>
                            <label for="selectPotrillo"><i class="fas fa-horse" style="color: #00b4d8;"></i> Potrillo</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="selectPotranca" class="form-select equino-select" data-tipo="potranca" name="idEquino">
                                <option value="">Seleccione Potranca</option>
                            </select>
                            <label for="selectPotranca"><i class="fas fa-horse" style="color: #00b4d8;"></i> Potranca</label>
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
                            <input type="text" name="dosis" id="dosis" class="form-control" required>
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
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Historiales Médicos Registrados</h5>
        </div>
        <div class="card-body">
            <table id="historialTable" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Equino</th>
                        <th>Medicamento</th>
                        <th>Dosis</th>
                        <th>Frecuencia</th>
                        <th>Vía</th>
                        <th>Observaciones</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha de Fin</th>
                        <th>Peso Equino</th>
                        <th>Reacciones Adversas</th>
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
        let selectedEquinoId = null;
        const selectYegua = document.querySelector("#selectYegua");
        const selectPadrillo = document.querySelector("#selectPadrillo");
        const selectPotrillo = document.querySelector("#selectPotrillo");
        const selectPotranca = document.querySelector("#selectPotranca");
        const medicamentoSelect = document.querySelector("#selectMedicamento");
        const fechaFinInput = document.querySelector("#fechaFin");

        // Restringir la fecha de fin a hoy y futuras
        fechaFinInput.min = new Date().toISOString().split("T")[0];

        // Función para cargar la tabla de historial médico
        const loadHistorialTable = async () => {
            // Destruir la tabla si ya existe
            if ($.fn.dataTable.isDataTable('#historialTable')) {
                $('#historialTable').DataTable().destroy();
            }

            // Configurar el DataTable
            $('#historialTable').DataTable({
                ajax: {
                    url: '../../controllers/historialme.controller.php',
                    type: 'GET',
                    data: { operation: 'consultarHistorialMedico' },  // Sin filtro de idEquino
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
                    { data: 'nombreMedicamento' },
                    { data: 'dosis' },
                    { data: 'frecuenciaAdministracion' },
                    { data: 'viaAdministracion' },
                    { data: 'observaciones' },
                    { data: 'fechaInicio' },
                    { data: 'fechaFin' },
                    { data: 'pesoEquino' },
                    { data: 'reaccionesAdversas' },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-primary" onclick="editarRegistro(${row.idRegistro})">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="eliminarRegistro(${row.idRegistro})">
                                    <i class="fas fa-trash-alt"></i> Eliminar
                                </button>
                            `;
                        }
                    }
             
                ],
                language: {
                    url: '/haras/data/es_es.json'
                }
            });
        };

        function editarRegistro(idRegistro) {
            // Lógica para editar el registro, por ejemplo, abrir un modal de edición
            console.log("Editar registro con ID:", idRegistro);
            // Aquí puedes agregar la lógica para cargar los datos en el formulario de edición
        }

        function eliminarRegistro(idRegistro) {
            if (confirm("¿Estás seguro de que deseas eliminar este registro?")) {
                fetch(`../../controllers/historialme.controller.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        operation: 'deleteHistorialMedico',
                        idRegistro: idRegistro
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === "success") {
                        alert("Registro eliminado exitosamente");
                        historialTable.ajax.reload(); // Recargar la tabla después de eliminar
                    } else {
                        alert("Error al eliminar el registro: " + data.message);
                    }
                })
                .catch(error => console.error("Error al eliminar el registro:", error));
            }
        }


        // Evento para actualizar selectedEquinoId y recargar DataTable
        document.querySelectorAll(".equino-select").forEach(select => {
            select.addEventListener("change", (event) => {
                selectedEquinoId = event.target.value || null; // Actualiza el ID del equino
                $('#historialTable').DataTable().ajax.reload(); // Recarga la tabla con el nuevo ID
            });
        });

        // Bloquear los selects de equinos al seleccionar uno
        function handleEquinoSelect(selectedSelect) {
            const selects = [selectYegua, selectPadrillo, selectPotrillo, selectPotranca];
            selects.forEach(select => {
                select.disabled = select !== selectedSelect && selectedSelect.value !== "";
            });
        }

        [selectYegua, selectPadrillo, selectPotrillo, selectPotranca].forEach(select => {
            select.addEventListener('change', () => handleEquinoSelect(select));
        });

        // Función para cargar la lista de equinos
        async function loadEquinos() {
            try {
                const response = await fetch('../../controllers/historialme.controller.php?operation=listarEquinosPorTipo', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' }
                });

                const data = await response.json();

                if (data.data && Array.isArray(data.data)) {
                    clearSelect(selectYegua);
                    clearSelect(selectPadrillo);
                    clearSelect(selectPotrillo);
                    clearSelect(selectPotranca);

                    data.data.forEach(equino => {
                        const option = document.createElement('option');
                        option.value = equino.idEquino;
                        option.textContent = equino.nombreEquino;

                        switch (parseInt(equino.idTipoEquino)) {
                            case 1: selectYegua.appendChild(option); break;
                            case 2: selectPadrillo.appendChild(option); break;
                            case 3: selectPotranca.appendChild(option); break;
                            case 4: selectPotrillo.appendChild(option); break;
                        }
                    });
                    showToast("Equinos cargados exitosamente", "SUCCESS");
                } else {
                    showToast("No se encontraron equinos.", "WARNING");
                }
            } catch (error) {
                showToast("Error al cargar los equinos", "ERROR");
                console.error("Error:", error);
            }
        }

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
                    showToast("Medicamentos cargados exitosamente", "SUCCESS");
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

            const formData = new FormData(event.target);
            formData.append("operation", "registrarHistorialMedico");

            console.log("Datos enviados al servidor:", Object.fromEntries(formData.entries())); // Log para verificar los datos enviados

            try {
                const response = await fetch('../../controllers/historialme.controller.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                console.log("Respuesta JSON parseada:", result); // Log de la respuesta

                if (result.status === "success") {
                    showToast(result.message || "Historial médico registrado correctamente", "SUCCESS");

                    event.target.reset();
                    $('#historialTable').DataTable().ajax.reload();

                    [selectYegua, selectPadrillo, selectPotrillo, selectPotranca].forEach(select => select.disabled = false);
                } else {
                    showToast("Error al registrar el historial: " + (result.message || "Desconocido"), "ERROR");
                }
            } catch (error) {
                console.error("Error al registrar el historial médico:", error);
                showToast("Error al registrar el historial médico: Error de conexión o error inesperado", "ERROR");
            }
        });

        // Función para limpiar los selects de equinos
        function clearSelect(selectElement) {
            selectElement.innerHTML = `<option value="">Seleccione ${selectElement.id.replace('select', '')}</option>`;
        }

        // Cargar equinos, medicamentos y la tabla de historial médico al iniciar
        loadEquinos();
        loadMedicamentos();
        loadHistorialTable();
    });
</script>


