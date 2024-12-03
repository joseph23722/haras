<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <div class="card-body p-1" style="background-color: #f9f9f9;">
        <div class="d-flex justify-content-center align-items-center mt-1" style="position: relative; width: 100%;">
            <h1 class="text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3; margin: 0; flex-grow: 1; text-align: center; margin-left: 170px;">
                Registro de Historial Médico
            </h1>
            <a href="./revisar-equino" class="btn btn-warning btn-lg" style="font-size: 1.1em; padding: 6px 20px;">
                Revisión Básica
            </a>
        </div>
    </div>
    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Historial Médico</h5>
            <!-- Botón para abrir el modal de agregar vías de administración -->
            <button type="button" class="btn btn-success btn-sm"
                style="background-color: #28a745; border: none; position: absolute; right: 2px; top: 2px; padding: 10px 15px; font-size: 1.2em;"
                id="btnAgregarVia"
                data-bs-toggle="modal"
                data-bs-target="#modalAgregarViaAdministracion">
                <i class="fas fa-plus"></i>
            </button>

            <!-- Botón para abrir el modal de ver vías de administración -->
            <button type="button" class="btn btn-info btn-sm"
                style="background-color: #17a2b8; border: none; position: absolute; right: 60px; top: 2px; padding: 10px 15px; font-size: 1.2em; color: white; font-weight: bold;"
                id="btnVerViasAdministracion"
                data-bs-toggle="modal"
                data-bs-target="#modalVerViasAdministracion">
                <i class="fas fa-eye"></i>
            </button>
        </div>

        <!-- Formulario -->
        <div class="card-body p-2" style="background-color: #f9f9f9;">
            <form action="" id="form-historial-medico" autocomplete="off">
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
                    <!-- Peso Equino (opcional) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input name="pesokg" id="pesokg" class="form-control" readonly disabled>
                            <label for="pesokg"><i class="fas fa-weight" style="color: #ff6f61;"></i> Peso Equino (kg)</label>
                        </div>
                    </div>

                    <!-- Select de Medicamentos -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="selectMedicamento" class="form-select" name="idMedicamento" required>
                                <option value="">Seleccione Medicamento</option>
                            </select>
                            <label for="selectMedicamento"><i class="fas fa-pills" style="color: #ffa500;"></i> Medicamento</label>
                        </div>
                    </div>

                    <!-- Tipo de Tratamiento (Primario o Complementario) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="tipoTratamiento" id="tipoTratamiento" class="form-select" required>
                                <option value="">Seleccione Tipo de Tratamiento</option>
                                <option value="Primario">Primario</option>
                                <option value="Complementario">Complementario</option>
                            </select>
                            <label for="tipoTratamiento"><i class="fas fa-list-alt" style="color: #ff8c00;"></i> Tipo de Tratamiento</label>
                        </div>
                    </div>

                    <!-- Fecha de Inicio -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fechaInicio" id="fechaInicio" class="form-control" required>
                            <label for="fechaInicio"><i class="fas fa-calendar-alt" style="color: #ba55d3;"></i> Fecha de Inicio</label>
                        </div>
                    </div>

                    <!-- Fecha Fin -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fechaFin" id="fechaFin" class="form-control" required>
                            <label for="fechaFin"><i class="fas fa-calendar-alt" style="color: #ba55d3;"></i> Fecha de Fin</label>
                        </div>
                    </div>

                    <!-- Dosis -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="dosis" id="dosis" placeholder="" class="form-control" required>
                            <label for="dosis"><i class="fas fa-syringe" style="color: #ff6347;"></i> Dosis</label>
                        </div>
                    </div>

                    <!-- Frecuencia de Administración -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="frecuenciaAdministracion" id="frecuenciaAdministracion" class="form-control" required>
                            <label for="frecuenciaAdministracion"><i class="fas fa-stopwatch" style="color: #6a5acd;"></i> Frecuencia de Administración</label>
                        </div>
                    </div>

                    <!-- Vía de Administración -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="viaAdministracion" id="viaAdministracion" class="form-select" required>
                                <option value="">Seleccione Vía de Administración</option>
                                <!-- Las opciones se cargarán dinámicamente -->
                            </select>
                            <label for="viaAdministracion">
                                <i class="fas fa-route" style="color: #00b4d8;"></i> Vía de Administración
                            </label>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <textarea name="observaciones" id="observaciones" class="form-control" style="height: 100px;"></textarea>
                            <label for="observaciones"><i class="fas fa-notes-medical" style="color: #007bff;"></i> Observaciones</label>
                        </div>
                    </div>

                    <!-- Reacciones Adversas (opcional) -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <textarea name="reaccionesAdversas" id="reaccionesAdversas" class="form-control" style="height: 100px;"></textarea>
                            <label for="reaccionesAdversas"><i class="fas fa-exclamation-circle" style="color: #dc3545;"></i> Reacciones Adversas</label>
                        </div>
                    </div>

                    <div id="mensaje" style="margin-top: 10px; color: green; font-weight: bold;"></div>

                    <!-- Botones -->
                    <div class="col-md-12 text-end mt-3">
                        <a href="./listar-diagnostico-avanzado" class="btn btn-primary btn-lg" style="background-color: #3498db; border-color: #3498db;">
                            <i class="fas fa-save"></i> Listado Diagnóstico
                        </a>
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

    <!-- Modal para agregar vías de administración -->
    <div class="modal fade" id="modalAgregarViaAdministracion" tabindex="-1" aria-labelledby="labelAgregarViaAdministracion" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #28a745; color: white;">
                    <h5 class="modal-title" id="labelAgregarViaAdministracion">
                        <i class="fas fa-plus-circle"></i> Agregar Nueva Vía de Administración
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formNuevaViaAdministracion" autocomplete="off">
                        <div class="mb-3">
                            <label for="inputNombreVia" class="form-label fw-bold">Nombre de la Vía</label>
                            <input type="text" class="form-control" id="inputNombreVia" name="inputNombreVia" placeholder="Ejemplo: Oral, Intramuscular" required>
                        </div>
                        <div class="mb-3">
                            <label for="inputDescripcionVia" class="form-label fw-bold">Descripción (Opcional)</label>
                            <textarea class="form-control" id="inputDescripcionVia" name="inputDescripcionVia" placeholder="Breve descripción de la vía (opcional)"></textarea>
                        </div>
                        <div id="mensajeModalVia" class="text-center mt-3"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnGuardarViaAdministracion">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar la lista de Vías de Administración -->
    <div class="modal fade" id="modalVerViasAdministracion" tabindex="-1" aria-labelledby="modalVerViasAdministracionLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="modalVerViasAdministracionLabel">Listado de Vías de Administración</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table id="tablaViasAdministracion" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar una vía de administración -->
    <div class="modal fade" id="modalEditarViaAdministracion" tabindex="-1" aria-labelledby="modalEditarViaAdministracionLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="modalEditarViaAdministracionLabel">Editar Vía de Administración</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarViaAdministracion">
                        <input type="hidden" id="editarIdVia">
                        <div class="mb-3">
                            <label for="editarNombreVia" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="editarNombreVia" required>
                        </div>
                        <div class="mb-3">
                            <label for="editarDescripcionVia" class="form-label">Descripción</label>
                            <textarea class="form-control" id="editarDescripcionVia" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>

<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- jsPDF-AutoTable -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.1/jspdf.plugin.autotable.min.js"></script>



<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="../../JS/historialmedico.js"></script>