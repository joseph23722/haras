<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Historial Equino</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;"></div>

        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-historial-equino" autocomplete="off">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="buscarEquino" placeholder="Buscar Equino" autofocus>
                                <label for="buscarEquino" class="form-label">Buscar Equino</label>
                            </div>
                            <button type="button" id="buscar-equino" class="btn btn-sm btn-outline-success"><i
                                    class="fa-solid fa-magnifying-glass"></i></button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="fechaNacimiento" disabled>
                            <label for="fechaNacimiento" class="form-label">Fecha Nacimiento</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nacionalidad" disabled>
                            <label for="nacionalidad" class="form-label">Nacionalidad</label>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="idPropietario" disabled>
                            <label for="idPropietario" class="form-label">Propietario</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="sexo" disabled>
                            <label for="sexo" class="form-label">Sexo</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="tipoEquino" disabled>
                            <label for="tipoEquino" class="form-label">Tipo de Equino</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="idEstadoMonta" disabled>
                            <label for="idEstadoMonta" class="form-label">Estado Monta</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="pesokg" disabled>
                            <label for="pesokg" class="form-label">Peso(kg)</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="fotografia" disabled>
                            <label for="fotografia" class="form-label">Fotografía</label>
                        </div>
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="carrerasGanadas">
                            <label for="carrerasGanadas" class="form-label">Carreras Ganadas</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nombreCarrerasGanadas">
                            <label for="nombreCarrerasGanadas" class="form-label">Nombre Carreras Ganadas</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="valorCarrera">
                            <label for="valorCarrera" class="form-label">Valor de la Carrera</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="valorMercado">
                            <label for="valorMercado" class="form-label">Valor en el mercado $</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="ranking">
                            <label for="ranking" class="form-label">Ranking Mundial</label>
                        </div>
                    </div>
                </div>
                <div class="row g-3">
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