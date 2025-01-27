<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #000;">Registro de Servicio Propio</h1>

    <div class="card mb-4 shadow-sm border-0" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header" style="background: linear-gradient(to left, #123524, #356C56);">
            <h5 class="mb-0 text-uppercase"  style="color: #EFE3C2;">Datos del Servicio Propio</h5>
        </div>
        <div class="card-body p-4 bg-light rounded" style="border: 1px solid #ddd;">
            <form action="" id="form-registro-servicio-propio" autocomplete="off">
                <div class="row g-4">
                    <!-- Seleccionar Padrillo -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idEquinoMacho" id="idPadrillo" class="form-select" style="border-radius: 5px;">
                                <option value="">Seleccione Padrillo</option>
                            </select>
                            <label for="idPadrillo" style="color: #001F3F; font-weight: bold;">
                                <i class="fas fa-horse-head me-2"></i>Padrillo
                            </label>
                        </div>
                    </div>

                    <!-- Seleccionar Yegua -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idEquinoHembra" id="idYegua" class="form-select" style="border-radius: 5px;">
                                <option value="">Seleccione Yegua</option>
                            </select>
                            <label for="idYegua" style="color: #001F3F; font-weight: bold;">
                                <i class="fas fa-horse me-2"></i>Yegua
                            </label>
                        </div>
                    </div>

                    <!-- Fecha del Servicio -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fechaServicio" id="fechaServicio" class="form-control" required style="border-radius: 5px;">
                            <label for="fechaServicio" style="color: #001F3F; font-weight: bold;">
                                <i class="fas fa-calendar-alt me-2"></i>Fecha del Servicio
                            </label>
                        </div>
                    </div>

                    <!-- Seleccionar Medicamento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idMedicamento" id="idDetalleMed" class="form-select" style="border-radius: 5px;">
                                <option value="">Seleccione Medicamento</option>
                            </select>
                            <label for="idDetalleMed" style="color: #001F3F; font-weight: bold;">
                                <i class="fas fa-pills me-2"></i>Detalle Medicamento
                            </label>
                        </div>
                    </div>

                    <!-- Campos Relacionados con Medicamento -->
                    <div id="medicamentoCampos" style="display: none; border-top: 2px solid #001F3F; padding-top: 15px; margin-top: 20px;">
                        <!-- Seleccionar Uso del Medicamento -->
                        <div class="col-md-12">
                            <label class="form-label mb-3" style="font-weight: bold; color: #001F3F;">¿El medicamento se usará para?</label>
                            <div class="form-check form-check-inline" style="margin-right: 20px;">
                                <input class="form-check-input" type="radio" name="usoMedicamento" id="usoPadrillo" value="padrillo" style="margin-top: 5px;">
                                <label class="form-check-label" for="usoPadrillo" style="color: #001F3F; font-weight: bold;">
                                    <i class="fas fa-horse-head me-2"></i>Padrillo
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="usoMedicamento" id="usoYegua" value="yegua" style="margin-top: 5px;">
                                <label class="form-check-label" for="usoYegua" style="color: #001F3F; font-weight: bold;">
                                    <i class="fas fa-horse me-2"></i>Yegua
                                </label>
                            </div>
                        </div>

                        <!-- Seleccionar Unidad -->
                        <div class="col-md-6 mt-3">
                            <div class="form-floating">
                                <select name="unidad" id="unidad" class="form-select" style="border-radius: 5px;">
                                    <option value="">Seleccione Unidad</option>
                                </select>
                                <label for="unidad" style="color: #001F3F; font-weight: bold;">
                                    <i class="fas fa-ruler me-2"></i>Unidad
                                </label>
                            </div>
                        </div>

                        <!-- Cantidad Aplicada -->
                        <div class="col-md-6 mt-3">
                            <div class="form-floating">
                                <input type="number" name="cantidadAplicada" id="cantidadAplicada" class="form-control" step="0.01" style="border-radius: 5px;">
                                <label for="cantidadAplicada" style="color: #001F3F; font-weight: bold;">
                                    <i class="fas fa-balance-scale me-2"></i>Cantidad Aplicada (mg)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles -->
                    <div class="col-md-12 mt-4">
                        <div class="form-floating">
                            <textarea name="detalles" id="detalles" placeholder="" class="form-control" style="height: 80px; border-radius: 5px;"></textarea>
                            <label for="detalles" style="color: #001F3F; font-weight: bold;">
                                <i class="fas fa-info-circle me-2"></i>Detalles
                            </label>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="col-md-12 text-end mt-4">
                        <a href="./listar-medicamento-usado" class="btn btn-primary btn-lg" style="background-color: #001F3F; color: #EFE3C2;">
                            <i class="fas fa-save"></i> Medicamentos Aplicados
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg" style="background-color: #123524; border-color: #123524; color: #EFE3C2;">
                            <i class="fas fa-save me-2"></i>Registrar Servicio
                        </button>
                        <button type="reset" class="btn btn-outline-secondary btn-lg" style="border-radius: 8px; font-weight: bold;">
                            <i class="fas fa-times me-2"></i>Cancelar
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
<script src="../../JS/servicioPropio.js"></script>