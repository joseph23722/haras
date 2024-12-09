<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 36px; color: #005b99;">
        <i class="fas fa-horse-head" style="color: #a0ffb8;"></i> Listado de Equinos
    </h1>

    <!-- Filtro de Estado Monta -->
    <div class="row mb-4">
        <div class="col-md-12">
            <label for="estadoMonta" style="font-size: 16px; font-weight: bold; color: #003366;">Filtrar por Estado Monta:</label>
            <select id="estadoMonta" class="form-select" style="width: 200px;">
                <option value="">Todos</option>
            </select>
        </div>
    </div>

    <!-- Sección de Listado de Equinos -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow border-0 mt-4">
                <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #9be8e4); color: #003366;">
                    <h5 class="text-center m-0" style="font-weight: bold;">
                        <i class="fas fa-database" style="color: #3498db;"></i> Datos de los Equinos
                    </h5>
                </div>
                <div class="card-body p-4" style="background-color: #f9f9f9;">
                    <table id="tabla-equinos" class="table table-striped table-hover table-bordered">
                        <thead style="background-color: #caf0f8; color: #003366;">
                            <tr>
                                <th>#</th>
                                <th>Nombre Equino</th>
                                <th>Fecha Nacimiento</th>
                                <th>Sexo</th>
                                <th>Tipo Equino</th>
                                <th>Detalles</th>
                                <th>Estado Monta</th>
                                <th>Peso (kg)</th>
                                <th>Nacionalidad</th>
                                <th>Estado</th>
                                <th><i class="fas fa-ellipsis-v"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- .col-md-12 -->
    </div> <!-- .row -->

    <!-- Modal Historial -->
    <div class="modal fade" id="historialModal" tabindex="-1" aria-labelledby="historialModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historialModalLabel">Historial del Equino</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="historialModalBody">
                    <!-- Historial se cargará aquí -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../footer.php'; ?>
<script src="../../JS/obtenerHistorialEquino.js"></script>