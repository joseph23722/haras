<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Registro de Equino</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Equino</h5>
        </div>

        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-registro-equino" autocomplete="off">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nombreEquino" id="nombreEquino" class="form-control" required>
                            <label for="nombreEquino"><i class="fas fa-horse-head" style="color: #00b4d8;"></i> Nombre del Equino</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idPropietario" id="idPropietario" class="form-select">
                                <option value="">Sin Propietario</option>
                            </select>
                            <label for="idPropietario"><i class="fas fa-home" style="color: #ffa500;"></i> Propietario (Opcional)</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fechaNacimiento" id="fechaNacimiento" class="form-control">
                            <label for="fechaNacimiento"><i class="fas fa-calendar-alt" style="color: #32cd32;"></i> Fecha de Nacimiento</label>
                        </div>
                    </div>

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

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idTipoEquino" id="TipoEquino" class="form-select" required>
                                <option value="">Seleccione Tipo Equino</option>
                            </select>
                            <label for="TipoEquino"><i class="fas fa-venus-mars" style="color: #ba55d3;"></i> Tipo Equino</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nacionalidad" id="nacionalidad" class="form-control">
                            <label for="nacionalidad"><i class="fas fa-flag" style="color: #6a5acd;"></i> Nacionalidad</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea name="detalles" id="detalles" class="form-control" style="height: 90px;"></textarea>
                            <label for="detalles"><i class="fas fa-info-circle" style="color: #1e90ff;"></i> Detalles</label>
                        </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-floating">
                            <input type="file" name="fotografia" id="fotografia" class="form-control" accept="image/*"> <!-- Campo para la fotografía -->
                            <label for="fotografia"><i class="fas fa-camera" style="color: #007bff;"></i> Fotografía del Equino</label>
                        </div>
                    </div>

                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="registrar-equino" style="background-color: #0077b6; border: none;">
                            <i class="fas fa-save"></i> Registrar Equino
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

<?php require_once '../../footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const idPropietarioSelect = document.querySelector("#idPropietario");
        const nacionalidadInput = document.querySelector("#nacionalidad");
        const fechaNacimientoInput = document.querySelector("#fechaNacimiento");
        const tipoEquinoSelect = document.querySelector("#TipoEquino");
        const formEquino = document.querySelector("#form-registro-equino");

        // Función para cargar propietarios
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
                idPropietarioSelect.innerHTML = '<option value="">Sin Propietario</option>';

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

        // Función para cargar tipos de equinos
        async function loadTipoEquinos() {
            try {
                const response = await fetch('../../controllers/registrarequino.controller.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        operation: 'listarTipoEquinos'
                    })
                });

                const data = await response.json();
                tipoEquinoSelect.innerHTML = '<option value="">Seleccione Tipo Equino</option>';

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(({
                        idTipoEquino,
                        tipoEquino
                    }) => {
                        const option = document.createElement('option');
                        option.value = idTipoEquino;
                        option.textContent = tipoEquino;
                        tipoEquinoSelect.appendChild(option);
                    });
                } else {
                    console.log('No se encontraron tipos de equinos disponibles.');
                }
            } catch (error) {
                console.error('Error al cargar los tipos de equinos:', error);
            }
        }

        // Función para manejar cambios en el propietario
        idPropietarioSelect.addEventListener("change", () => {
            if (idPropietarioSelect.value) {
                // Si hay un propietario seleccionado, desactivar los campos
                nacionalidadInput.disabled = true;
                fechaNacimientoInput.disabled = true;
                nacionalidadInput.value = null;
                fechaNacimientoInput.value = null;
            } else {
                // Si no hay propietario, habilitar los campos
                nacionalidadInput.disabled = false;
                fechaNacimientoInput.disabled = false;
            }
        });

        // Función para registrar un equino
        formEquino.addEventListener("submit", async (event) => {
            event.preventDefault();

            const nombreEquino = document.querySelector("#nombreEquino").value;
            const sexo = document.querySelector("#sexo").value;
            const idTipoEquino = tipoEquinoSelect.value;

            // Validación de campos obligatorios
            if (!nombreEquino || !sexo || !idTipoEquino || (!idPropietarioSelect.value && (!nacionalidadInput.value || !fechaNacimientoInput.value))) {
                alert('Los campos nombre, sexo, tipo de equino, y nacionalidad y fecha de nacimiento (si no hay propietario) son obligatorios.');
                console.log('Registro fallido: faltan campos obligatorios.');
                return; // Detiene el envío si falta algún campo
            }

            const formData = new FormData(formEquino);
            const data = {};
            formData.forEach((value, key) => {
                // Guardar como null si no hay propietario
                data[key] = value === "" && key === 'idPropietario' ? null : value;
            });

            console.log('Datos a enviar para registrar el equino:', data);

            try {
                const response = await fetch('../../controllers/registrarequino.controller.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        operation: 'registrarEquino',
                        ...data
                    })
                });

                const result = await response.json();
                if (result.status === "success") {
                    alert(result.message);
                    console.log('Registro exitoso:', result);
                    formEquino.reset();
                    nacionalidadInput.disabled = false; // Resetear el estado de los campos
                    fechaNacimientoInput.disabled = false; // Resetear el estado de los campos
                } else {
                    alert('Error al registrar el equino: ' + result.message);
                    console.log('Error en registro:', result.message);
                }
            } catch (error) {
                alert('Error al registrar el equino. Intente nuevamente.');
                console.error('Error al registrar el equino:', error);
            }
        });

        // Cargar datos al inicio
        loadPropietarios();
        loadTipoEquinos();
    });
</script>