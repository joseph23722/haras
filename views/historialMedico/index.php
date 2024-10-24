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

                    <!-- Fecha Inicio -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fechaInicio" id="fechaInicio" class="form-control" required>
                            <label for="fechaInicio"><i class="fas fa-calendar-alt" style="color: #ba55d3;"></i> Fecha de Inicio</label>
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
                            <input type="text" name="viaAdministracion" id="viaAdministracion" class="form-control" required>
                            <label for="viaAdministracion"><i class="fas fa-route" style="color: #00b4d8;"></i> Vía de Administración</label>
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
</div>

<?php require_once '../footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
    const selectYegua = document.querySelector("#selectYegua");
    const selectPadrillo = document.querySelector("#selectPadrillo");
    const selectPotrillo = document.querySelector("#selectPotrillo");
    const selectPotranca = document.querySelector("#selectPotranca");
    const medicamentoSelect = document.querySelector("#selectMedicamento");
    const formHistorial = document.querySelector("#form-historial-medico");

    // Cargar equinos para los diferentes tipos
    loadEquinos();

    // Cargar medicamentos
    loadMedicamentos();

    // Cargar equinos y asignar a los selects correspondientes
    async function loadEquinos() {
        try {
            const response = await fetch('../../controllers/historialme.controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ operation: 'listarEquinosPorTipo' })
            });

            const data = await response.json();

            if (data.status === 'success' && Array.isArray(data.data)) {
                // Limpiar los selects antes de agregar nuevos valores
                clearSelect(selectYegua);
                clearSelect(selectPadrillo);
                clearSelect(selectPotrillo);
                clearSelect(selectPotranca);

                // Iterar sobre los equinos y asignarlos al select correspondiente
                data.data.forEach(equino => {
                    console.log(equino); // Para depuración
                    const option = document.createElement('option');
                    option.value = equino.idEquino;
                    option.textContent = equino.nombreEquino;

                    // Convertir idTipoEquino a número antes de usarlo en el switch
                    switch (parseInt(equino.idTipoEquino)) {
                        case 1: // Yegua
                            selectYegua.appendChild(option);
                            break;
                        case 2: // Padrillo
                            selectPadrillo.appendChild(option);
                            break;
                        case 3: // Potranca
                            selectPotranca.appendChild(option);
                            break;
                        case 4: // Potrillo
                            selectPotrillo.appendChild(option);
                            break;
                        default:
                            console.error("Tipo de equino no reconocido:", equino.idTipoEquino);
                    }
                });

            } else {
                console.error('No se encontraron equinos.');
            }
        } catch (error) {
            console.error('Error al cargar los equinos:', error);
        }
    }

    // Cargar lista de medicamentos
    async function loadMedicamentos() {
        try {
            const response = await fetch('../../controllers/historialme.controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ operation: 'listarMedicamentos' })
            });

            const data = await response.json();

            if (data.status === 'success' && Array.isArray(data.data)) {
                medicamentoSelect.innerHTML = '<option value="">Seleccione Medicamento</option>';
                data.data.forEach(medicamento => {
                    const option = document.createElement('option');
                    option.value = medicamento.idMedicamento;
                    option.textContent = medicamento.nombreMedicamento;
                    medicamentoSelect.appendChild(option);
                });
            } else {
                console.error('No se encontraron medicamentos.');
            }
        } catch (error) {
            console.error('Error al cargar los medicamentos:', error);
        }
    }

    // Limpiar select antes de agregar nuevas opciones
    function clearSelect(selectElement) {
        selectElement.innerHTML = `<option value="">Seleccione ${selectElement.id.replace('select', '')}</option>`;
    }
});

</script>
