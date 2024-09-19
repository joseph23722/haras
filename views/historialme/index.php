<?php require_once '../../header.php'; ?>

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

                    <!-- Fecha -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fecha" id="fecha" class="form-control" required>
                            <label for="fecha"><i class="fas fa-calendar-alt" style="color: #ba55d3;"></i> Fecha</label>
                        </div>
                    </div>

                    <!-- Diagnóstico -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea name="diagnostico" id="diagnostico" class="form-control" style="height: 90px;" required></textarea>
                            <label for="diagnostico"><i class="fas fa-stethoscope" style="color: #1e90ff;"></i> Diagnóstico</label>
                        </div>
                    </div>

                    <!-- Tratamiento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea name="tratamiento" id="tratamiento" class="form-control" style="height: 90px;" required></textarea>
                            <label for="tratamiento"><i class="fas fa-pills" style="color: #ffa500;"></i> Tratamiento</label>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea name="observaciones" id="observaciones" class="form-control" style="height: 90px;"></textarea>
                            <label for="observaciones"><i class="fas fa-notes-medical" style="color: #ff6347;"></i> Observaciones</label>
                        </div>
                    </div>

                    <!-- Recomendaciones -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea name="recomendaciones" id="recomendaciones" class="form-control" style="height: 90px;"></textarea>
                            <label for="recomendaciones"><i class="fas fa-clipboard-check" style="color: #6a5acd;"></i> Recomendaciones</label>
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

    <!-- Tabla de Historial Médico Registrado -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5><i class="fas fa-database"></i> Historial Médico Registrado</h5>
        </div>
        <div class="card-body" style="background-color: #f9f9f9;">
            <table class="table table-striped table-hover table-bordered">
                <thead style="background-color: #caf0f8; color: #003366;">
                    <tr>
                        <th>ID</th>
                        <th>Equino</th>
                        <th>Fecha</th>
                        <th>Diagnóstico</th>
                        <th>Tratamiento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="historial-table">
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const equinoSelects = document.querySelectorAll(".equino-select");
    const formHistorial = document.querySelector("#form-historial-medico");
    const historialTable = document.querySelector("#historial-table");
    let selectedEquinoId = null;

    // Cargar listas de equinos para cada tipo
    equinoSelects.forEach(select => {
        const tipoEquino = select.dataset.tipo;
        loadOptions(tipoEquino, select);
    });

    // Cargar equinos por tipo
    async function loadOptions(tipo, selectElement) {
        try {
            const response = await fetch('../../controllers/historialme.controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ operation: 'listarEquinosPorTipo', tipoEquino: tipo })
            });

            const data = await response.json();

            if (Array.isArray(data)) {
                selectElement.innerHTML = `<option value="">Seleccione ${tipo.charAt(0).toUpperCase() + tipo.slice(1)}</option>`;
                data.forEach(equino => {
                    const option = document.createElement('option');
                    option.value = equino.idEquino;
                    option.textContent = equino.nombreEquino;
                    selectElement.appendChild(option);
                });
            } else {
                console.error(`No se encontraron equinos para el tipo: ${tipo}`);
            }
        } catch (error) {
            console.error(`Error al cargar opciones de ${tipo}:`, error);
        }
    }

    // Manejar la selección de un equino y bloquear los demás selects
    equinoSelects.forEach(select => {
        select.addEventListener('change', () => {
            if (select.value !== '') {
                equinoSelects.forEach(otherSelect => {
                    if (otherSelect !== select) {
                        otherSelect.disabled = true;
                    }
                });
                selectedEquinoId = select.value;
            } else {
                equinoSelects.forEach(otherSelect => {
                    otherSelect.disabled = false;
                });
                selectedEquinoId = null;
            }
        });
    });

    // Manejar el envío del formulario
    formHistorial.addEventListener("submit", async (event) => {
        event.preventDefault();

        const formData = new FormData(formHistorial);
        const data = {};

        formData.forEach((value, key) => { data[key] = value; });
        data['idEquino'] = selectedEquinoId;

        try {
            const response = await fetch('../../controllers/historialme.controller.php', {
                method: "POST",
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ operation: 'registrar', ...data })
            });

            const result = await response.json();
            alert(result.message);

            if (result.status === 'success') {
                formHistorial.reset();
                selectedEquinoId = null;
                equinoSelects.forEach(select => select.disabled = false); // Desbloquear todos los selects
                loadHistoriales();
            }
        } catch (error) {
            alert('Hubo un problema al registrar el historial médico.');
            console.error('Error en la solicitud:', error);
        }
    });

    // Cargar la tabla de historiales médicos registrados
    async function loadHistoriales() {
        try {
            const response = await fetch('../../controllers/historialme.controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ operation: 'listarHistoriales' })
            });

            const historiales = await response.json();

            historialTable.innerHTML = historiales.map(historial => `
                <tr>
                    <td>${historial.idHistorial}</td>
                    <td>${historial.nombreEquino}</td>
                    <td>${historial.fecha}</td>
                    <td>${historial.diagnostico}</td>
                    <td>${historial.tratamiento}</td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm" onclick="eliminarHistorial(${historial.idHistorial})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error al cargar los historiales:', error);
        }
    }

    // Inicializar la carga de historiales médicos
    loadHistoriales();
});
</script>
