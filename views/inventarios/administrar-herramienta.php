<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Registro de Historial de Herrero</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #ffcc80, #ffb74d); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Historial de Herrero</h5>
            <!-- Botón para abrir el modal de agregar trabajo o herramienta -->
            <!-- Botón para abrir el modal de agregar trabajo o herramienta -->
            <button type="button" class="btn btn-success btn-sm" 
                    style="background-color: #28a745; border: none; position: absolute; right: 1px; top: 1px; padding: 6px 1px; font-size: 1.2em;"
                    id="btnAgregarTrabajoHerramienta" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalAgregarTrabajoHerramienta">
                <i class="fas fa-plus"></i> Agregar Trabajo o Herramienta
            </button>

        </div>

        <!-- Formulario -->
        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-historial-herrero" autocomplete="off">
                <div class="row g-3">

                    <!-- Selector para el Tipo de Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="tipoEquinoSelect" class="form-select" name="tipoEquino" required>
                                <option value="">Seleccione Tipo de Equino</option>
                                <option value="1">Yegua</option>
                                <option value="2">Padrillo</option>
                                <option value="3">Potranca</option>
                                <option value="4">Potrillo</option>
                            </select>
                            <label for="tipoEquinoSelect"><i class="fas fa-horse" style="color: #00b4d8;"></i> Tipo de Equino</label>
                        </div>
                    </div>

                    <!-- Selector para el nombre del Tipo de Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="equinoSelect" class="form-select" name="idEquino" required>
                                <option value="">Seleccione Equino</option>
                            </select>
                            <label for="equinoSelect"><i class="fas fa-horse" style="color: #00b4d8;"></i> Equino</label>
                        </div>
                    </div>

                    <!-- Selector para Trabajo Realizado -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="trabajoRealizado" class="form-select" name="idTrabajo" required>
                                <option value="">Seleccione Trabajo Realizado</option>
                            </select>
                            <label for="trabajoRealizado"><i class="fas fa-tools" style="color: #ff8c00;"></i> Trabajo Realizado</label>
                        </div>
                    </div>

                    <!-- Selector para Herramienta Usada -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="herramientaUsada" class="form-select" name="idHerramienta" required>
                                <option value="">Seleccione Herramienta Usada</option>
                            </select>
                            <label for="herramientaUsada"><i class="fas fa-wrench" style="color: #00b4d8;"></i> Herramienta Usada</label>
                        </div>
                    </div>


                    <!-- Campo de fecha -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" id="fecha" name="fecha" class="form-control" required>
                            <label for="fecha"><i class="fas fa-calendar-alt" style="color: #007bff;"></i> Fecha</label>
                        </div>
                    </div>


                    <!-- Observaciones -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <textarea name="observaciones" id="observaciones" class="form-control" style="height: 100px;"></textarea>
                            <label for="observaciones"><i class="fas fa-notes-medical" style="color: #007bff;"></i> Observaciones</label>
                        </div>
                    </div>

                    <div id="mensaje" style="margin-top: 10px; color: green; font-weight: bold;"></div>

                    <!-- Botones -->
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="registrar-historial-herrero" style="background-color: #0077b6; border: none;">
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


    <!-- Modal para agregar trabajo o herramienta -->
    <div class="modal fade" id="modalAgregarTrabajoHerramienta" tabindex="-1" aria-labelledby="labelAgregarTrabajoHerramienta" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="labelAgregarTrabajoHerramienta">Agregar Nuevo Trabajo o Herramienta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevoTrabajoHerramienta" autocomplete="off">
                        <!-- Campo para el nombre del trabajo o herramienta -->
                        <div class="mb-3">
                            <label for="inputNombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="inputNombre" placeholder="Ejemplo: Ajuste de herradura o Lima para cascos" required>
                        </div>

                        <!-- Campo para el tipo de trabajo o herramienta -->
                        <div class="mb-3">
                            <label for="selectTipo" class="form-label">Tipo</label>
                            <select id="selectTipo" class="form-select" required>
                                <option value="">Seleccione Tipo</option>
                                <option value="trabajo">Trabajo</option>
                                <option value="herramienta">Herramienta</option>
                            </select>
                        </div>

                        <!-- Campo para seleccionar trabajo si es tipo trabajo -->
                        <div class="mb-3" id="campoTrabajo" style="display:none;">
                            <label for="selectTrabajo" class="form-label">Seleccionar Trabajo</label>
                            <select id="selectTrabajo" class="form-select">
                                <!-- Las opciones de trabajos se cargarán dinámicamente -->
                            </select>
                        </div>

                        <!-- Campo para seleccionar herramienta si es tipo herramienta -->
                        <div class="mb-3" id="campoHerramienta" style="display:none;">
                            <label for="selectHerramienta" class="form-label">Seleccionar Herramienta</label>
                            <select id="selectHerramienta" class="form-select">
                                <!-- Las opciones de herramientas se cargarán dinámicamente -->
                            </select>
                        </div>

                        <!-- Mensajes de error o éxito -->
                        <div id="mensajeModal" class="text-center mt-3"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnGuardarTrabajoHerramienta">Guardar</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Tabla para DataTable -->
    <div class="card mt-4">
        <div class="card-header" style="background: linear-gradient(to right, #ffcc80, #ffb74d); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Historiales de Herrero</h5>
        </div>
        <div class="card-body">
            <!-- No es necesario llenar tbody manualmente, DataTables lo hace -->
            <table id="historialHerreroTable" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre del Equino</th>
                        <th>Tipo de Equino</th>
                        <th>Fecha</th>
                        <th>Trabajo Realizado</th>
                        <th>Herramienta Usada</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>

            </table>
        </div>
    </div>



</div>

<?php require_once '../footer.php'; ?>

<script src="/haras/vendor/herrero/herrero.js" defer></script>


<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Incluye SweetAlert -->
<script src="../../swalcustom.js"></script>
<script src="../../JS/administrar-herramienta.js"></script>

