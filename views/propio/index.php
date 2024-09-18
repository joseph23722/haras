<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal con estilo formal y masculino -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #005b99;">Registro de Servicio Propio</h1>

    <!-- Breadcrumb con fondo claro y detalles sutiles -->
    <ol class="breadcrumb mb-4 p-2 rounded" style="background-color: #e8f1f8;">
        <li class="breadcrumb-item active" style="color: #004085; font-weight: bold;">
            <i class="fas fa-info-circle" style="color: #007bff;"></i> Complete los datos del servicio propio
        </li>
    </ol>

    <!-- Tarjeta de formulario -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header" style="background-color: #d9edf7; border-bottom: 2px solid #005b99;">
            <h5 class="mb-0 text-uppercase" style="color: #005b99;">Datos del Servicio</h5>
        </div>

        <!-- Formulario -->
        <div class="card-body p-4 bg-white rounded">
            <form action="" id="form-registro-servicio-propio" autocomplete="off">
                <div class="row g-3">
                    <!-- Selección de Padrillo -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idEquino1" id="idPadrillo" class="form-select" required>
                                <option value="">Seleccione Padrillo</option>
                            </select>
                            <label for="idPadrillo"><i class="fas fa-horse-head" style="color: #007bff;"></i> Padrillo</label>
                        </div>
                    </div>

                    <!-- Selección de Yegua -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idEquino2" id="idYegua" class="form-select" required>
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

                    <!-- Botón de registro -->
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Registrar Servicio</button>
                        <button type="reset" class="btn btn-outline-secondary btn-lg"><i class="fas fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de servicios registrados -->
    <div class="card mt-4 shadow-sm border-0">
        <div class="card-header" style="background-color: #d9edf7; border-bottom: 2px solid #005b99;">
            <h5 class="mb-0" style="color: #005b99;"><i class="fas fa-list"></i> Servicios Propios Registrados</h5>
        </div>

        <!-- Tabla de datos -->
        <div class="card-body p-0">
            <table id="tablaServiciosPropios" class="table table-hover table-striped table-bordered mb-0">
                <thead class="bg-light text-center">
                    <tr>
                        <th>ID</th>
                        <th>Padrillo</th>
                        <th>Yegua</th>
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
    const formPropio = document.querySelector("#form-registro-servicio-propio");
    const idPadrilloSelect = document.querySelector("#idPadrillo");
    const idYeguaSelect = document.querySelector("#idYegua");
    const tablaServiciosPropios = document.querySelector("#tablaServiciosPropios tbody");

    // Función para cargar opciones de equinos por tipo (padrillo y yegua)
    async function loadOptions(url, selectElement, tipoEquino) {
        try {
            const response = await fetch(`${url}?tipoEquino=${tipoEquino}`);
            const data = await response.json();

            selectElement.innerHTML = '<option value="">Seleccione</option>';
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.idEquino;
                option.textContent = item.nombreEquino;
                selectElement.appendChild(option);
            });
        } catch (error) {
            console.error(`Error al cargar opciones: ${error}`);
        }
    }

    // Cargar opciones de padrillo y yegua
    loadOptions('../../controllers/propio.controller.php', idPadrilloSelect, 'padrillo');
    loadOptions('../../controllers/propio.controller.php', idYeguaSelect, 'yegua');

    // Manejar el envío del formulario
    formPropio.addEventListener("submit", async (event) => {
        event.preventDefault();

        const formData = new FormData(formPropio);
        const data = {};
        formData.forEach((value, key) => { data[key] = value; });

        try {
            const response = await fetch('../../controllers/propio.controller.php', {
                method: "POST",
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            alert(result.message);

            // Si la respuesta es exitosa, mostrar los datos en la tabla
            if (result.status === 'success') {
                const newRow = `
                    <tr>
                        <td>${result.id}</td>
                        <td>${data['idEquino1']}</td>
                        <td>${data['idEquino2']}</td>
                        <td>${data['fechaServicio']}</td>
                        <td>${data['horaEntrada']}</td>
                        <td>${data['horaSalida']}</td>
                        <td>${data['detalles']}</td>
                    </tr>
                `;
                tablaServiciosPropios.insertAdjacentHTML('beforeend', newRow);
                formPropio.reset();
            }
        } catch (error) {
            alert('Hubo un problema al registrar el servicio.');
            console.error('Error:', error);
        }
    });
});
</script>
