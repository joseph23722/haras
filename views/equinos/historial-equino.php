<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Historial Equino</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;"></div>

        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-registro-bostas" autocomplete="off">
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="buscarEquino" placeholder="Buscar Equino" autofocus>
                                <label for="buscarEquino" class="form-label">Buscar Equino</label>
                            </div>
                            <button type="button" id="buscar-equino" class="btn btn-sm btn-outline-success"><i class="fa-solid fa-magnifying-glass"></i></button>
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
                            <input type="text" class="form-control" id="fotografia" disabled>
                            <label for="fotografia" class="form-label">Fotografía</label>
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
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('buscar-equino').addEventListener('click', function() {
            const nombreEquino = document.getElementById('buscarEquino').value;

            fetch('../../controllers/registrarequino.controller.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        operation: 'buscarEquinoPorNombre',
                        nombreEquino: nombreEquino
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        const equino = data[0];
                        document.getElementById('fechaNacimiento').value = equino.fechaNacimiento || '';
                        document.getElementById('nacionalidad').value = equino.nacionalidad || '';
                        document.getElementById('idPropietario').value = equino.idPropietario || '';
                        document.getElementById('sexo').value = equino.sexo || '';
                        document.getElementById('tipoEquino').value = equino.tipoEquino || '';
                        document.getElementById('idEstadoMonta').value = equino.idEstadoMonta || '';
                        document.getElementById('fotografia').value = equino.fotografia || '';
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'No encontrado',
                            text: 'No se encontraron equinos con ese nombre.',
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al buscar el equino.',
                    });
                });
        });
    });
</script>