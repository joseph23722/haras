<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #ff6347;">Registro de Equino</h1>

    <div class="card mb-4 shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0 text-uppercase">Datos del Equino</h5>
        </div>

        <!-- Formulario -->
        <div class="card-body p-4 bg-light rounded">
            <form action="" id="form-registro-equino" autocomplete="off">
                <div class="row g-3">
                    <!-- Nombre del Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nombreEquino" id="nombreEquino" class="form-control" required>
                            <label for="nombreEquino"><i class="fas fa-horse-head" style="color: #ff7f50;"></i> Nombre del Equino</label>
                        </div>
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fechaNacimiento" id="fechaNacimiento" class="form-control" required>
                            <label for="fechaNacimiento"><i class="fas fa-calendar-alt" style="color: #32cd32;"></i> Fecha de Nacimiento</label>
                        </div>
                    </div>

                    <!-- Sexo del Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="sexo" id="sexo" class="form-select" required>
                                <option value="">Seleccione Sexo</option>
                                <option value="macho">Macho</option>
                                <option value="hembra">Hembra</option>
                            </select>
                            <label for="sexo"><i class="fas fa-venus-mars" style="color: #ba55d3;"></i> Sexo</label>
                        </div>
                    </div>

                    <!-- Detalles del Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea name="detalles" id="detalles" class="form-control" style="height: 90px;"></textarea>
                            <label for="detalles"><i class="fas fa-info-circle" style="color: #1e90ff;"></i> Detalles</label>
                        </div>
                    </div>

                    <!-- Propietario del Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idPropietario" id="idPropietario" class="form-select" required>
                                <option value="">Seleccione Propietario</option>
                            </select>
                            <label for="idPropietario"><i class="fas fa-home" style="color: #ffa500;"></i> Propietario</label>
                        </div>
                    </div>

                    <!-- Generación del Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="generacion" id="generacion" class="form-control" required>
                            <label for="generacion"><i class="fas fa-layer-group" style="color: #ff6347;"></i> Generación</label>
                        </div>
                    </div>

                    <!-- Nacionalidad del Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nacionalidad" id="nacionalidad" class="form-control" required>
                            <label for="nacionalidad"><i class="fas fa-flag" style="color: #6a5acd;"></i> Nacionalidad</label>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-lg" id="registrar-equino"><i class="fas fa-save"></i> Registrar Equino</button>
                        <button type="reset" class="btn btn-secondary btn-lg shadow-sm"><i class="fas fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla para mostrar los equinos registrados -->
    <div class="card mt-4 shadow-lg border-0">
        <div class="card-header bg-gradient-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list"></i> Equinos Registrados</h5>
            <i class="fas fa-table fa-2x" style="color: #32cd32;"></i>
        </div>
        <div class="card-body p-0">
            <table id="tablaEquinos" class="table table-hover table-striped table-bordered shadow-sm mb-0">
                <thead class="bg-primary text-white text-center">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Fecha Nacimiento</th>
                        <th>Sexo</th>
                        <th>Propietario</th>
                        <th>Generación</th>
                        <th>Nacionalidad</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <!-- Aquí se mostrarán los registros cargados -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const idPropietarioSelect = document.querySelector("#idPropietario");
    const formEquino = document.querySelector("#form-registro-equino");
    const tablaEquinos = document.querySelector("#tablaEquinos tbody");

    // Función para cargar la lista de propietarios
    async function loadPropietarios() {
        try {
            const response = await fetch('../../controllers/registrarequino.controller.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ operation: 'listarPropietarios' })
            });

            const data = await response.json();

            idPropietarioSelect.innerHTML = '<option value="">Seleccione Propietario</option>';
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(propietario => {
                    const option = document.createElement('option');
                    option.value = propietario.idPropietario;
                    option.textContent = propietario.nombreHaras;
                    idPropietarioSelect.appendChild(option);
                });
            } else {
                console.error('No se encontraron propietarios o el formato de datos es incorrecto.');
            }
        } catch (error) {
            console.error('Error al cargar los propietarios:', error);
        }
    }

    // Llamar a la función para cargar los propietarios al iniciar la página
    loadPropietarios();

    // Manejar el envío del formulario
    formEquino.addEventListener("submit", async (event) => {
        event.preventDefault();

        const formData = new FormData(formEquino);
        const data = {};

        formData.forEach((value, key) => { data[key] = value; });

        try {
            const response = await fetch('../../controllers/registrarequino.controller.php', {
                method: "POST",
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ operation: 'add', ...data })
            });

            const result = await response.json();
            alert(result.message);

            if (result.status === 'success') {
                const newRow = `
                    <tr>
                        <td>${result.idEquino}</td>
                        <td>${data['nombreEquino']}</td>
                        <td>${data['fechaNacimiento']}</td>
                        <td>${data['sexo']}</td>
                        <td>${idPropietarioSelect.options[idPropietarioSelect.selectedIndex].text}</td>
                        <td>${data['generacion']}</td>
                        <td>${data['nacionalidad']}</td>
                        <td>${data['detalles']}</td>
                    </tr>
                `;
                tablaEquinos.insertAdjacentHTML('beforeend', newRow);
                formEquino.reset();
            }
        } catch (error) {
            alert('Hubo un problema al registrar el equino.');
            console.error('Error en la solicitud:', error);
        }
    });
});
</script>
