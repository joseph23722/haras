<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #003300;">Registro de Rotación de Campos</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #c9ffd6); color: #003300;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Campo</h5>
        </div>

        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-rotacion-campos" autocomplete="off">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating">
                            <select name="campos" id="campos" class="form-select" autofocus>
                                <option value="">Seleccione un campo</option>
                                <!-- Opciones se llenarán dinámicamente -->
                            </select>
                            <label for="campos"><i class="fas fa-home" style="color: #003300;"></i> Nro de potreros</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <textarea name="ultimaAccionRealizada" id="ultimaAccionRealizada" class="form-control" style="height: 50px;" disabled>Ultima Acción</textarea>
                            <label for="ultimaAccionRealizada"><i class="fas fa-sticky-note" style="color: #003300;"></i> Ultima Acción</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <select name="tipoRotacion" id="tipoRotacion" class="form-select">
                                <option value="">Seleccione un tipo de Rotación</option>
                            </select>
                            <label for="tipoRotacion"><i class="fa-solid fa-arrow-rotate-left" style="color: #003300;"></i> Tipo de Rotación</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="date" name="fechaRotacion" id="fechaRotacion" class="form-control">
                            <label for="fechaRotacion"><i class="fas fa-calendar-alt" style="color: #003300;"></i> Fecha de Rotación</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <textarea name="detalleRotacion" id="detalleRotacion"  placeholder="" class="form-control" style="height: 50px;"></textarea>
                            <label for="detalleRotacion"><i class="fas fa-info-circle" style="color: #003300;"></i> Detalles</label>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-floating">
                            <input type="file" name="fotografia" id="fotografia" class="form-control" accept="image/*">
                            <label for="fotografia"><i class="fas fa-camera" style="color: #003300;"></i> Fotografía del campo</label>
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
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #c9ffd6); color: #003300;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Lista de Rotaciones</h5>
        </div>
        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <table id="tabla-campos" class="table table-striped table-bordered" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Número de Campo</th>
                        <th>Tamaño campo (m)</th>
                        <th>Tipo de suelo</th>
                        <th>Ultima accion</th>
                        <th>Fecha Ultima Accion</th>
                        <th>Estado</th>
                        <th>Acciones</th>
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
<div class="modal fade" id="registerFieldModal" tabindex="-1" aria-labelledby="registerFieldModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false"
>
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
                        <input type="number" class="form-control" id="numeroCampo" name="numeroCampo" min="" max="99"
                            required oninput="this.value = Math.max(0, Math.min(99, this.value));">
                        <small class="form-text text-muted">Ingrese un número entre 1 y 99.</small>
                    </div>
                    <div class="mb-3">
                        <label for="tamanoCampo" class="form-label">Tamaño del Campo (m)</label>
                        <input type="text" class="form-control" id="tamanoCampo" name="tamanoCampo"
                            pattern="^\d+(\.\d{1,2})?$" required
                            title="Ingrese un número. Ejemplo: 1.5 o 2.00"
                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^(\d*\.?\d{0,2}).*/, '$1');">
                        <small class="form-text text-muted">Ingrese un número expresado en metros</small>
                    </div>
                    <div class="mb-3">
                        <label for="tipoSuelo" class="form-label">Tipo de Suelo</label>
                        <select class="form-control" name="tipoSuelo" id="tipoSuelo" required>
                            <option value="">Seleccione un tipo de suelo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-control" name="estado" id="estado" required>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
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

<!-- Modal para Editar Campo -->
<div class="modal fade" id="editarCampoModal" tabindex="-1" role="dialog" aria-labelledby="editarCampoModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarCampoModalLabel">Editar Campo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-editar-campo">
                    <input type="hidden" id="idCampoEdit" name="idCampo">
                    <div class="mb-3">
                        <label for="numeroCampoEdit" class="form-label">Número de Campo</label>
                        <input type="number" class="form-control" id="numeroCampoEdit" name="numeroCampo" required
                            oninput="this.value = Math.max(0, Math.min(99, this.value));">
                        <small class="form-text text-muted">Ingrese un número entre 1 y 99.</small>
                    </div>
                    <div class="mb-3">
                        <label for="tamanoCampoEdit" class="form-label">Tamaño de Campo</label>
                        <input type="text" class="form-control" id="tamanoCampoEdit" name="tamanoCampo" required
                            oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^(\d*\.?\d{0,2}).*/, '$1');">
                        <small class="form-text text-muted">Ingrese un número expresado en metros</small>
                    </div>
                    <div class="mb-3">
                        <label for="tipoSueloEdit" class="form-label">Tipo de Suelo</label>
                        <select class="form-control" id="tipoSueloEdit" name="tipoSuelo" required>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="estadoEdit" class="form-label">Estado</label>
                        <select class="form-control" id="estadoEdit" name="estado" required>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="guardarCampoEditar">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../swalcustom.js"></script>
<script src="../../JS/rotacionCampos.js"></script>