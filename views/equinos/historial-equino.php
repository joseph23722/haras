<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 36px; color: #0056b3;">
        <i class="fas fa-horse-head" style="color: #a0ffb8;"></i> Historial Equino
    </h1>

    <div class="card mb-4 shadow-lg border-0">
        <div class="card-header text-center" style="background: linear-gradient(90deg, #a0ffb8, #9be8e4); color: #003366;">
            <h5 class="m-0" style="font-weight: bold;">
                <i class="fas fa-info-circle" style="color: #3498db;"></i> Información del Equino
            </h5>
        </div>

        <div class="card-body p-5" style="background-color: #f7f9fc;">
            <form action="" id="form-historial-equino" autocomplete="off">
                <!-- Primera fila: Búsqueda y Datos Básicos -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="form-floating flex-grow-1">
                                <input type="text" class="form-control" id="buscarEquino" placeholder="Buscar Equino" autofocus>
                                <label for="buscarEquino"><i class="fas fa-search" style="color: #3498db;"></i> Buscar Equino</label>
                            </div>
                            <button type="button" id="buscar-equino" class="btn btn-outline-success" title="Buscar">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control bg-light" id="fechaNacimiento" disabled>
                            <label for="fechaNacimiento"><i class="fas fa-calendar-alt" style="color: #3498db;"></i> Fecha Nacimiento</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control bg-light" id="nacionalidad" disabled>
                            <label for="nacionalidad"><i class="fas fa-flag" style="color: #3498db;"></i> Nacionalidad</label>
                        </div>
                    </div>
                </div>

                <!-- Segunda fila: Información del Propietario y Características del Equino -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control bg-light" id="idPropietario" disabled>
                            <label for="idPropietario"><i class="fas fa-user" style="color: #3498db;"></i> Propietario</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control bg-light" id="sexo" disabled>
                            <label for="sexo"><i class="fas fa-venus-mars" style="color: #3498db;"></i> Sexo</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control bg-light" id="tipoEquino" disabled>
                            <label for="tipoEquino"><i class="fas fa-horse" style="color: #3498db;"></i> Tipo de Equino</label>
                        </div>
                    </div>
                </div>

                <!-- Tercera fila: Información adicional del Equino -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="form-floating">
<<<<<<< HEAD
                            <input type="text" class="form-control" id="ranking" placeholder="">
                            <label for="ranking" class="form-label">Ranking Mundial</label>
=======
                            <input type="text" class="form-control bg-light" id="idEstadoMonta" disabled>
                            <label for="idEstadoMonta"><i class="fas fa-chart-line" style="color: #3498db;"></i> Estado Monta</label>
>>>>>>> 53306d9424c4faaba82e51699c42f57f07a7805a
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
<<<<<<< HEAD
                            <input type="number" class="form-control" id="valorMercado" placeholder="">
                            <label for="valorMercado" class="form-label">Valor en el mercado $</label>
=======
                            <input type="text" class="form-control bg-light" id="pesokg" disabled>
                            <label for="pesokg"><i class="fas fa-weight" style="color: #3498db;"></i> Peso(kg)</label>
>>>>>>> 53306d9424c4faaba82e51699c42f57f07a7805a
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
<<<<<<< HEAD
                            <input type="number" class="form-control" id="carrerasCorridas" placeholder="">
                            <label for="carrerasCorridas" class="form-label">Carreras Corridas</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <textarea class="form-control" id="nombreCarrerasCorridas" placeholder="" rows="3"></textarea>
                            <label for="nombreCarrerasCorridas" class="form-label">Nombre Carreras Corridas</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="valorCarrera" placeholder="">
                            <label for="valorCarrera" class="form-label">Valor de la Carrera</label>
=======
                            <input type="text" class="form-control bg-light" id="fotografia" disabled>
                            <label for="fotografia"><i class="fas fa-camera" style="color: #3498db;"></i> Fotografía</label>
>>>>>>> 53306d9424c4faaba82e51699c42f57f07a7805a
                        </div>
                    </div>
                </div>

                <!-- Cuarta fila: Datos de Competencias y Valoración -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="carrerasCorridas" placeholder="0">
                            <label for="carrerasCorridas"><i class="fas fa-trophy" style="color: #3498db;"></i> Carreras Corridas</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nombreCarrerasCorridas" placeholder="Nombre de Carrera">
                            <label for="nombreCarrerasCorridas"><i class="fas fa-award" style="color: #3498db;"></i> Nombre Carreras Corridas</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="valorCarrera" placeholder="0">
                            <label for="valorCarrera"><i class="fas fa-money-bill-wave" style="color: #3498db;"></i> Valor de la Carrera</label>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="valorMercado" placeholder="0">
                            <label for="valorMercado"><i class="fas fa-dollar-sign" style="color: #3498db;"></i> Valor en el mercado $</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="ranking" placeholder="Ranking">
                            <label for="ranking"><i class="fas fa-medal" style="color: #3498db;"></i> Ranking Mundial</label>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="row g-3">
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm me-2" id="registrar-historial" style="background-color: #0056b3; border: none;">
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





<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../swalcustom.js"></script>
<script>
    document.querySelector("#buscar-equino").addEventListener("click", async function() {
        const nombreEquino = document.getElementById("buscarEquino").value;

        // Realiza la búsqueda del equino
        const response = await fetch('../../controllers/registrarequino.controller.php', {
            method: 'POST',
            body: JSON.stringify({
                operation: 'buscarEquinoPorNombre',
                nombreEquino: nombreEquino
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.length === 0) {
            showToast("No se encontró ningún equino con ese nombre.", 'WARNING');

            // Limpiar los campos del formulario
            document.getElementById("fechaNacimiento").value = '';
            document.getElementById("nacionalidad").value = '';
            document.getElementById("idPropietario").value = '';
            document.getElementById("sexo").value = '';
            document.getElementById("tipoEquino").value = '';
            document.getElementById("idEstadoMonta").value = '';
            document.getElementById("pesokg").value = '';
            document.getElementById("fotografia").value = '';

        } else {
            // Rellena los campos con la información del equino
            const equino = data[0];
            document.getElementById("fechaNacimiento").value = equino.fechaNacimiento || '';
            document.getElementById("nacionalidad").value = equino.nacionalidad || '';
            document.getElementById("idPropietario").value = equino.idPropietario || 'Haras Rancho Sur';
            document.getElementById("sexo").value = equino.sexo || '';
            document.getElementById("tipoEquino").value = equino.tipoEquino || '';
            document.getElementById("idEstadoMonta").value = equino.estadoMonta || '';
            document.getElementById("pesokg").value = equino.pesokg || 'Por pesar';
            document.getElementById("fotografia").value = equino.fotografia || '';
        }
    });

    document.querySelector("#form-historial-equino").addEventListener("submit", async function(event) {
        event.preventDefault();

        const confirmar = await ask("¿Está seguro de que desea registrar el historial?");
        if (confirmar) {
            showToast("Funcionalidad de registrar historial aún no implementada.", 'INFO');
        }
    });
</script>