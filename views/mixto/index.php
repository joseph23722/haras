<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal con un tono formal y masculino -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #005b99;">Registro de Servicio Mixto</h1>

    <!-- Breadcrumb con tonos claros y detalles sutiles -->
    <ol class="breadcrumb mb-4 p-2 rounded" style="background-color: #e8f1f8;">
        <li class="breadcrumb-item active" style="color: #004085; font-weight: bold;">
            <i class="fas fa-info-circle" style="color: #007bff;"></i> Complete los datos para registrar el servicio mixto
        </li>
    </ol>

    <div class="card mb-4 shadow-sm border-0">
        <!-- Título de la tarjeta con un tono claro y acento masculino -->
        <div class="card-header" style="background-color: #d9edf7; border-bottom: 2px solid #005b99;">
            <h5 class="mb-0 text-uppercase" style="color: #005b99;">Datos del Servicio Mixto</h5>
        </div>

        <!-- Formulario con colores claros y acentos sutiles -->
        <div class="card-body p-4 bg-white rounded">
            <form action="" id="form-registro-servicio-mixto" autocomplete="off">
                <div class="row g-3">
                    <!-- Selección de Padrillo -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idPadrillo" id="idPadrillo" class="form-select">
                                <option value="">Seleccione Padrillo</option>
                            </select>
                            <label for="idPadrillo"><i class="fas fa-horse-head" style="color: #007bff;"></i> Padrillo</label>
                        </div>
                    </div>

                    <!-- Selección de Yegua -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idYegua" id="idYegua" class="form-select">
                                <option value="">Seleccione Yegua</option>
                            </select>
                            <label for="idYegua"><i class="fas fa-horse" style="color: #007bff;"></i> Yegua</label>
                        </div>
                    </div>

                    <!-- Fecha del Servicio -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fechaServicio" id="fechaServicio" class="form-control" required>
                            <label for="fechaServicio"><i class="fas fa-calendar-alt" style="color: #007bff;"></i> Fecha del Servicio</label>
                        </div>
                    </div>

                    <!-- Hora de Entrada -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="time" name="horaEntrada" id="horaEntrada" class="form-control" required>
                            <label for="horaEntrada"><i class="fas fa-clock" style="color: #007bff;"></i> Hora de Entrada</label>
                        </div>
                    </div>

                    <!-- Hora de Salida -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="time" name="horaSalida" id="horaSalida" class="form-control" required>
                            <label for="horaSalida"><i class="fas fa-clock" style="color: #007bff;"></i> Hora de Salida</label>
                        </div>
                    </div>

                    <!-- Detalles del Servicio -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <textarea name="detalles" id="detalles" class="form-control" required style="height: 90px;"></textarea>
                            <label for="detalles"><i class="fas fa-info-circle" style="color: #007bff;"></i> Detalles</label>
                        </div>
                    </div>

                    <!-- Campo de Haras -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idHaras" id="idHaras" class="form-select">
                                <option value="">Seleccione Haras</option>
                            </select>
                            <label for="idHaras"><i class="fas fa-home" style="color: #007bff;"></i> Haras</label>
                        </div>
                    </div>

                    <!-- Campo para agregar un nuevo haras -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nuevoHaras" id="nuevoHaras" class="form-control" disabled>
                            <label for="nuevoHaras"><i class="fas fa-plus-circle" style="color: #007bff;"></i> Nuevo Haras</label>
                        </div>
                    </div>

                    <!-- Botón para habilitar el campo de nuevo haras -->
                    <div class="col-md-12 text-end mt-2">
                        <button type="button" id="nuevoHarasBtn" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus"></i> Agregar Nuevo Haras</button>
                    </div>

                    <!-- Campo para Nombre del Nuevo Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nombreNuevoEquino" id="nombreNuevoEquino" class="form-control" required>
                            <label for="nombreNuevoEquino"><i class="fas fa-horse-head" style="color: #007bff;"></i> Nombre del Nuevo Equino</label>
                        </div>
                    </div>

                    <!-- Campo para Género del Nuevo Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="genero" id="genero" class="form-select" required>
                                <option value="">Seleccione Género</option>
                                <option value="macho">Macho</option>
                                <option value="hembra">Hembra</option>
                            </select>
                            <label for="genero"><i class="fas fa-venus-mars" style="color: #007bff;"></i> Género del Nuevo Equino</label>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Registrar Servicio</button>
                        <button type="reset" class="btn btn-outline-secondary btn-lg"><i class="fas fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla para mostrar los datos registrados -->
    <div class="card mt-4 shadow-sm border-0">
        <div class="card-header" style="background-color: #d9edf7; border-bottom: 2px solid #005b99;">
            <h5 class="mb-0" style="color: #005b99;"><i class="fas fa-list"></i> Servicios Mixtos Registrados</h5>
        </div>
        <div class="card-body p-0">
            <table id="tablaServicios" class="table table-hover table-striped table-bordered mb-0">
                <thead class="bg-light text-center">
                    <tr>
                        <th>ID</th>
                        <th>Padrillo/Yegua</th>
                        <th>Nombre Equino</th>
                        <th>Fecha</th>
                        <th>Hora Entrada</th>
                        <th>Hora Salida</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <!-- Aquí se mostrarán los registros cargados -->
                </tbody>
            </table>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center p-2 bg-light">
                <span class="text-muted">Mostrando 1 a 10 de 50 registros</span>
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>


<script>
document.addEventListener("DOMContentLoaded", () => {
    const formMixto = document.querySelector("#form-registro-servicio-mixto");
    const idPadrilloSelect = document.querySelector("#idPadrillo");
    const idYeguaSelect = document.querySelector("#idYegua");
    const idHarasSelect = document.querySelector("#idHaras");
    const nuevoHarasInput = document.querySelector("#nuevoHaras");
    const nuevoHarasBtn = document.querySelector("#nuevoHarasBtn");
    const tablaServicios = document.querySelector("#tablaServicios tbody");

    // Manejar el botón de agregar nuevo Haras
    nuevoHarasBtn.addEventListener("click", () => {
        nuevoHarasInput.disabled = !nuevoHarasInput.disabled;
        idHarasSelect.disabled = !idHarasSelect.disabled;
    });

    // Función para cargar opciones de equinos (padrillo, yegua) y haras
    async function loadOptions(url, selectElement, tipoEquino = null) {
        try {
            let fetchUrl = url;
            if (tipoEquino) {
                fetchUrl += `?tipoEquino=${tipoEquino}`;
            }

            const response = await fetch(fetchUrl);
            const data = await response.json();

            selectElement.innerHTML = '<option value="">Seleccione</option>';
            if (Array.isArray(data)) {
                data.forEach(item => {
                    const option = document.createElement("option");
                    option.value = item.idEquino || item.idPropietario;
                    option.textContent = item.nombreEquino || item.nombreHaras;
                    selectElement.appendChild(option);
                });
            } else {
                console.error("La respuesta no es un array:", data);
            }
        } catch (error) {
            console.error(`Error al cargar opciones: ${error}`);
        }
    }

    // Cargar opciones de padrillo, yegua y haras
    loadOptions('../../controllers/mixto.controller.php', idPadrilloSelect, 'padrillo');
    loadOptions('../../controllers/mixto.controller.php', idYeguaSelect, 'yegua');
    loadOptions('../../controllers/mixto.controller.php?listarHaras=true', idHarasSelect);

    // Bloquear el select de yegua si se selecciona un padrillo, y viceversa
    idPadrilloSelect.addEventListener("change", () => {
        idYeguaSelect.disabled = !!idPadrilloSelect.value;
    });

    idYeguaSelect.addEventListener("change", () => {
        idPadrilloSelect.disabled = !!idYeguaSelect.value;
    });

    // Manejar el envío del formulario
    formMixto.addEventListener("submit", async (event) => {
        event.preventDefault();

        const formData = new FormData(formMixto);
        const data = {};

        formData.forEach((value, key) => { data[key] = value; });

        if (data['genero'] === 'macho') {
            data['idTipoEquino'] = 2;
        } else if (data['genero'] === 'hembra') {
            data['idTipoEquino'] = 1;
        }

        if (!nuevoHarasInput.disabled) {
            data['nombreHaras'] = nuevoHarasInput.value.trim();
            delete data['idHaras'];
        } else {
            delete data['nombreHaras'];
        }

        try {
            const response = await fetch('../../controllers/mixto.controller.php', {
                method: "POST",
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            alert(result.message);

            if (result.status === 'success') {
                const newRow = `
                    <tr>
                        <td>${result.id}</td>
                        <td>${data['idPadrillo'] ? 'Padrillo' : 'Yegua'}</td>
                        <td>${data['nombreNuevoEquino']}</td>
                        <td>${data['fechaServicio']}</td>
                        <td>${data['horaEntrada']}</td>
                        <td>${data['horaSalida']}</td>
                        <td>${data['detalles']}</td>
                    </tr>
                `;
                tablaServicios.insertAdjacentHTML('beforeend', newRow);
                formMixto.reset();
            }
        } catch (error) {
            alert('Hubo un problema al registrar el servicio.');
            console.error('Error en la solicitud:', error);
        }
    });
});
</script>
