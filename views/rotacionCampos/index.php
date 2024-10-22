<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Registro de Rotación de Campos</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Campo</h5>
        </div>

        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-rotacion-campos" autocomplete="off">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select name="campos" id="campos" class="form-select">
                                <option value="">Seleccione un campo</option>
                                <!-- Opciones se llenarán dinámicamente -->
                            </select>
                            <label for="campos"><i class="fas fa-home" style="color: #ffa500;"></i> Nro de potreros</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <textarea name="ultimaAccionRealizada" id="ultimaAccionRealizada" class="form-control" style="height: 50px;" readonly>Ultima Acción</textarea>
                            <label for="ultimaAccionRealizada"><i class="fas fa-info-circle" style="color: #1e90ff;"></i> Ultima Acción</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <select name="tipoRotacion" id="tipoRotacion" class="form-select">
                                <option value="">Seleccione un tipo de Rotación</option>
                            </select>
                            <label for="tipoRotacion"><i class="fa-solid fa-arrow-rotate-left" style="color: #ffa500;"></i> Tipo de Rotación</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="date" name="fechaRotacion" id="fechaRotacion" class="form-control">
                            <label for="fechaRotacion"><i class="fas fa-calendar-alt" style="color: #32cd32;"></i> Fecha de Rotación</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <textarea name="detalles" id="detalles" class="form-control" style="height: 50px;"></textarea>
                            <label for="detalles"><i class="fas fa-info-circle" style="color: #1e90ff;"></i> Detalles</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="file" name="fotografia" id="fotografia" class="form-control" accept="image/*">
                            <label for="fotografia"><i class="fas fa-camera" style="color: #007bff;"></i> Fotografía del campo</label>
                        </div>
                    </div>

                    <div class="col-md-12 text-end mt-3">
                        <button type="button" class="btn btn-primary btn-lg shadow-sm" style="background-color: #0077b6; border: none;" data-bs-toggle="modal" data-bs-target="#registerFieldModal">
                            <i class="fas fa-plus-circle"></i> Registrar Nuevo Campo
                        </button>
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="registrar-rotacion" style="background-color: #0077b6; border: none;">
                            <i class="fas fa-save"></i> Registrar Rotación
                        </button>
                        <button type="reset" class="btn btn-secondary btn-lg shadow-sm" style="background-color: #adb5bd; border: none;">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de rotaciones -->
    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Lista de Rotaciones</h5>
        </div>
        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <table id="tabla-campos" class="table table-striped table-bordered" style="width: 100%;">
                <thead>
                    <tr>
                        <th>ID CAMPO</th>
                        <th>Número de Campo</th>
                        <th>Tamaño campo</th>
                        <th>Tipo de suelo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se llenarán dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal para registrar un nuevo campo -->
<div class="modal fade" id="registerFieldModal" tabindex="-1" aria-labelledby="registerFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerFieldModalLabel">Registrar Nuevo Campo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-nuevo-campo">
                    <div class="mb-3">
                        <label for="numeroCampo" class="form-label">Número del Campo</label>
                        <input type="number" class="form-control" id="numeroCampo" name="numeroCampo" required>
                    </div>
                    <div class="mb-3">
                        <label for="tamanoCampo" class="form-label">Tamaño del Campo</label>
                        <input type="number" class="form-control" id="tamanoCampo" name="tamanoCampo" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipoSuelo" class="form-label">Tipo de Campo</label>
                        <input type="text" class="form-control" id="tipoSuelo" name="tipoSuelo" required>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-control" name="estado" id="estado" required>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarCampo">Guardar Campo</button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cargar campos
        function recargarCampos() {
            fetch('../../controllers/campos.controller.php?operation=getCampos')
                .then(response => response.json())
                .then(data => {
                    const camposSelect = document.getElementById('campos');
                    camposSelect.innerHTML = ''; // Limpia antes de cargar
                    if (data.status !== "error") {
                        data.forEach(campo => {
                            const option = document.createElement('option');
                            option.value = campo.idCampo;
                            option.textContent = campo.numeroCampo;
                            camposSelect.appendChild(option);
                        });
                    } else {
                        console.error(data.message);
                    }
                })
                .catch(error => console.error('Error fetching campos:', error));
        }

        // Cargar tipos de rotación
        fetch('../../controllers/campos.controller.php?operation=getTiposRotaciones')
            .then(response => response.json())
            .then(data => {
                const tipoRotacionSelect = document.getElementById('tipoRotacion');
                if (data.status !== "error") {
                    data.forEach(tipo => {
                        const option = document.createElement('option');
                        option.value = tipo.idTipoRotacion;
                        option.textContent = tipo.nombreRotacion;
                        tipoRotacionSelect.appendChild(option);
                    });
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error('Error fetching tipos de rotación:', error));

        // Inicializar DataTable
        function inicializarDataTable() {
            if ($.fn.DataTable.isDataTable('#tabla-campos')) {
                $('#tabla-campos').DataTable().destroy();
            }

            $('#tabla-campos').DataTable({
                ajax: {
                    url: '../../controllers/campos.controller.php?operation=getCampos',
                    dataSrc: ''
                },
                columns: [{
                        data: 'idCampo'
                    },
                    {
                        data: 'numeroCampo'
                    },
                    {
                        data: 'tamanoCampo'
                    },
                    {
                        data: 'tipoSuelo'
                    },
                    {
                        data: 'estado'
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json'
                }
            });
        }
        inicializarDataTable();

        // Guardar nuevo campo
        document.getElementById('guardarCampo').addEventListener('click', function() {
            const nuevoCampoForm = document.getElementById('form-nuevo-campo');
            const formData = new FormData(nuevoCampoForm);
            formData.append('operation', 'registrarCampo'); // Agregar la operación aquí

            fetch('../../controllers/campos.controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status !== "error") {
                        $('#registerFieldModal').modal('hide');
                        alert("Campo registrado exitosamente.");
                        recargarCampos();
                        inicializarDataTable();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error registrando campo:', error));
        });

        // Registrar rotación
        document.getElementById('form-rotacion-campos').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            formData.append('operation', 'rotacionCampos'); // Asegurarse de agregar la operación

            fetch('../../controllers/campos.controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.idRotacion) {
                        alert('Rotación registrada con éxito. ID Rotación: ' + data.idRotacion);
                    } else {
                        alert('Error registrando rotación.');
                    }
                })
                .catch(error => console.error('Error registrando rotación:', error));
        });
    });
</script>