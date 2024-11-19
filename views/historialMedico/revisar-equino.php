<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Revisión Básica</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos de la Revisión Básica</h5>
        </div>

        <div class="card-body p-1" style="background-color: #f9f9f9;">
            <div class="d-flex justify-content-start mt-1">
                <a href="./diagnosticar-equino" class="btn btn-warning btn-lg mx-3" style="font-size: 1.1em; padding: 12px 30px;">
                    Revisión Avanzada
                </a>
            </div>
        </div>

        <!-- Formulario -->
        <div class="card-body p-2" style="background-color: #f9f9f9;">
            <form id="formRevisionBasica" method="post" autocomplete="off">
                <div class="row g-3">

                    <!-- Selector para el ID del Propietario -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="idPropietario" name="idPropietario">
                                <option value="">Haras Rancho Sur</option>
                            </select>
                            <label for="idPropietario" class="form-label">Seleccione Propietario</label>
                        </div>
                    </div>

                    <!-- Selector para el Tipo de Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="idEquino" name="idEquino" required>
                                <option value="">Selecione un Equino</option>
                            </select>
                            <label for="idEquino"><i class="fas fa-horse" style="color: #00b4d8;"></i> Seleccione un Equino</label>
                        </div>
                    </div>

                    <!-- Tipo de Revisión -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="tiporevision" name="tiporevision" required>
                                <option value="">Seleccione un Tipo de Revisión</option>
                                <option value="Ecografia">Ecografía</option>
                                <option value="Examen ginecológico">Examen ginecológico</option>
                                <option value="Citología">Citología</option>
                                <option value="Cultivo bacteriológico">Cultivo bacteriológico</option>
                                <option value="Biopsia endometrial">Biopsia endometrial</option>
                            </select>
                            <label for="tiporevision"><i class="fas fa-stethoscope" style="color: #28a745;"></i> Tipo de Revisión</label>
                        </div>
                    </div>

                    <!-- Fecha de Revisión -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="fecharevision" name="fecharevision" required>
                            <label for="fecharevision"><i class="fas fa-calendar-alt" style="color: #007bff;"></i> Fecha de Revisión</label>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="col-md-6    ">
                        <div class="form-floating">
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="" required></textarea>
                            <label for="observaciones"><i class="fas fa-notes-medical" style="color: #007bff;"></i> Observaciones</label>
                        </div>
                    </div>

                    <!-- Costo de la Revisión -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="costorevision" name="costorevision" step="0.01" placeholder="">
                            <label for="costorevision"><i class="fas fa-dollar-sign" style="color: #28a745;"></i> Costo de la Revisión (USD)</label>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="registrarRevision" style="background-color: #0077b6; border: none;">
                            <i class="fas fa-save"></i> Registrar Revisión
                        </button>
                        <button type="reset" class="btn btn-secondary btn-lg shadow-sm" style="background-color: #adb5bd; border: none;">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Incluye SweetAlert -->
<script src="../../swalcustom.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const idPropietarioSelect = document.querySelector("#idPropietario");
        const idEquinoSelect = document.querySelector("#idEquino");

        // Cargar propietarios
        async function loadPropietarios() {
            try {
                const response = await fetch('../../controllers/registrarequino.controller.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        operation: 'listarPropietarios'
                    })
                });

                const data = await response.json();
                idPropietarioSelect.innerHTML = '<option value="">Haras Rancho Sur</option>';

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(({
                        idPropietario,
                        nombreHaras
                    }) => {
                        const option = document.createElement('option');
                        option.value = idPropietario;
                        option.textContent = nombreHaras;
                        idPropietarioSelect.appendChild(option);
                    });
                } else {
                    console.log('No se encontraron propietarios disponibles.');
                }
            } catch (error) {
                console.error('Error al cargar los propietarios:', error);
            }
        }

        // Cargar los equinos del propietario seleccionado
        async function loadEquinos(idPropietario = null) {
            try {
                const response = await fetch('../../controllers/revisionbasica.controller.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        operation: 'listarYeguasPorPropietario',
                        idPropietario: idPropietario
                    })
                });

                const data = await response.json();
                idEquinoSelect.innerHTML = '<option value="">Seleccione un Equino</option>';

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(({
                        idEquino,
                        nombreEquino
                    }) => {
                        const option = document.createElement('option');
                        option.value = idEquino;
                        option.textContent = nombreEquino;
                        idEquinoSelect.appendChild(option);
                    });
                } else {
                    console.log('No se encontraron yeguas disponibles.');
                }
            } catch (error) {
                console.error('Error al cargar los equinos:', error);
            }
        }

        // Escuchar el cambio en la selección del propietario
        idPropietarioSelect.addEventListener('change', (event) => {
            const idPropietario = event.target.value;
            if (idPropietario) {
                loadEquinos(idPropietario);
            } else {
                idEquinoSelect.innerHTML = '<option value="">Seleccione un Equino</option>';
                loadEquinos();
            }
        });

        // Enviar los datos del formulario para registrar la revisión del equino
        document.querySelector('#formRevisionBasica').addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = {
                operation: 'registrarRevisionEquino',
                idEquino: document.querySelector("#idEquino").value,
                idPropietario: document.querySelector("#idPropietario").value || null,
                tiporevision: document.querySelector("#tiporevision").value,
                fecharevision: document.querySelector("#fecharevision").value,
                observaciones: document.querySelector("#observaciones").value,
                costorevision: document.querySelector("#costorevision").value || null
            };

            // Validación de campos obligatorios
            if (!formData.idEquino || !formData.tiporevision || !formData.fecharevision || !formData.observaciones) {
                showToast('Por favor, complete todos los campos obligatorios.', 'WARNING', 4500);
                return;
            }

            const confirmation = await ask("¿Estás seguro de que deseas registrar la revisión del equino?", "Haras Rancho Sur");

            if (!confirmation) {
                showToast('La acción fue cancelada.', 'INFO', 4500);
                return;
            }

            try {
                const response = await fetch('../../controllers/revisionbasica.controller.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.status === 'success') {
                    showToast(result.message || 'Revisión registrada correctamente.', 'SUCCESS', 4500);
                    document.querySelector('#formRevisionBasica').reset();
                } else {
                    showToast(result.message || 'Hubo un error al registrar la revisión.', 'ERROR', 4500);
                }
            } catch (error) {
                console.error('Error al registrar la revisión:', error);
                showToast('Hubo un error al registrar la revisión.', 'ERROR', 4500);
            }
        });

        loadPropietarios();
        loadEquinos();
    });
</script>