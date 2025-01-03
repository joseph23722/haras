<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #005b99;">Registro de Servicio Mixto</h1>
    <ol class="breadcrumb mb-4 p-2 rounded" style="background-color: #e8f1f8;">
        <li class="breadcrumb-item active" style="color: #004085; font-weight: bold;">
            <i class="fas fa-info-circle" style="color: #007bff;"></i> Complete los datos para registrar el servicio mixto
        </li>
    </ol>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header" style="background-color: #a0ffb8; border-bottom: 2px solid #005b99;">
            <h5 class="mb-0 text-uppercase" style="color: #005b99;">Datos del Servicio Mixto</h5>
        </div>
        <div class="card-body p-4 bg-white rounded">
            <div id="mensaje" class="alert alert-info" style="display: none;"></div>
            <form action="" id="form-registro-servicio-mixto" autocomplete="off">
                <!-- Grupo: Selección de Equinos -->
                <fieldset class="border p-3 mb-4">
                    <legend class="w-auto px-3" style="color: #005b99; font-size: 1.2em;">Seleccionar Equinos</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="idEquinoMacho" id="idEquinoMacho" class="form-select" autofocus>
                                    <option value="">Seleccione Padrillo</option>
                                </select>
                                <label for="idEquinoMacho"><i class="fas fa-horse-head" style="color: #007bff;"></i> Padrillo</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="idEquinoHembra" id="idEquinoHembra" class="form-select">
                                    <option value="">Seleccione Yegua</option>
                                </select>
                                <label for="idEquinoHembra"><i class="fas fa-horse" style="color: #007bff;"></i> Yegua</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="idPropietario" id="idPropietario" class="form-select">
                                    <option value="">Seleccione Propietario</option>
                                </select>
                                <label for="idPropietario"><i class="fas fa-home" style="color: #007bff;"></i> Propietario</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="idEquinoExterno" id="idEquinoExterno" class="form-select">
                                    <option value="">Seleccione Equino Externo</option>
                                </select>
                                <label for="idEquinoExterno"><i class="fas fa-horse" style="color: #007bff;"></i> Equino Externo</label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- Grupo: Información del Servicio -->
                <fieldset class="border p-3 mb-4">
                    <legend class="w-auto px-3" style="color: #005b99; font-size: 1.2em;">Información del Servicio</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" name="fechaServicio" id="fechaServicio" class="form-control" required>
                                <label for="fechaServicio"><i class="fas fa-calendar-alt" style="color: #007bff;"></i> Fecha del Servicio</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="time" name="horaEntrada" id="horaEntrada" min="06:00" max="18:00" class="form-control" required>
                                <label for="horaEntrada"><i class="fas fa-clock" style="color: #007bff;"></i> Hora de Entrada</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="time" name="horaSalida" id="horaSalida" min="06:00" max="18:00" class="form-control" required>
                                <label for="horaSalida"><i class="fas fa-clock" style="color: #007bff;"></i> Hora de Salida</label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- Grupo: Medicamentos -->
                <fieldset class="border p-3 mb-4">
                    <legend class="w-auto px-3" style="color: #005b99; font-size: 1.2em;">Información del Medicamento</legend>
                    <div class="row g-3">
                        <!-- Selección de Medicamento -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="idMedicamento" id="idDetalleMed" class="form-select">
                                    <option value="">Seleccione Medicamento (Opcional)</option>
                                </select>
                                <label for="idDetalleMed"><i class="fas fa-pills" style="color: #007bff;"></i> Detalle Medicamento</label>
                            </div>
                        </div>

                        <!-- Campos Relacionados con Medicamento -->
                        <div id="medicamentoCampos" style="display: none; margin-top: 20px;">
                            <div class="row g-3">
                                <!-- ¿El medicamento se usará para? -->
                                <div class="col-md-12">
                                    <label class="form-label" style="font-weight: bold; color: #007bff;">¿El medicamento se usará para?</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="usoMedicamento" id="usoPadrillo" value="padrillo">
                                        <label class="form-check-label" for="usoPadrillo">
                                            <i class="fas fa-horse-head" style="color: #007bff;"></i> Padrillo
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="usoMedicamento" id="usoYegua" value="yegua">
                                        <label class="form-check-label" for="usoYegua">
                                            <i class="fas fa-horse" style="color: #007bff;"></i> Yegua
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="usoMedicamento" id="usoExterno" value="externo">
                                        <label class="form-check-label" for="usoExterno">
                                            <i class="fas fa-horse" style="color: #007bff;"></i> Equino Externo
                                        </label>
                                    </div>
                                </div>

                                <!-- Unidad -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="unidad" id="unidad" class="form-select">
                                            <option value="">Seleccione Unidad</option>
                                        </select>
                                        <label for="unidad"><i class="fas fa-ruler" style="color: #007bff;"></i> Unidad</label>
                                    </div>
                                </div>

                                <!-- Cantidad Aplicada -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" name="cantidadAplicada" id="cantidadAplicada" class="form-control" step="0.01">
                                        <label for="cantidadAplicada"><i class="fas fa-balance-scale" style="color: #007bff;"></i> Cantidad Aplicada (mg)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>


                <!-- Grupo: Costos y Detalles -->
                <fieldset class="border p-3 mb-4">
                    <legend class="w-auto px-3" style="color: #005b99; font-size: 1.2em;">Costos y Detalles</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" name="costoServicio" id="costoServicio" placeholder="" class="form-control">
                                <label for="costoServicio"><i class="fas fa-money-bill-wave" style="color: #007bff;"></i> Costo Servicio</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <textarea name="detalles" id="detalles" placeholder="" class="form-control"></textarea>
                                <label for="detalles"><i class="fas fa-info-circle" style="color: #007bff;"></i> Detalles</label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- Botones -->
                <div class="text-end mt-3">
                    <a href="./listar-medicamento-usado" class="btn btn-primary btn-lg" style="background-color: #3498db; border-color: #3498db;">
                        <i class="fas fa-save"></i> Medicamentos Aplicados
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Registrar Servicio</button>
                    <button type="reset" class="btn btn-outline-secondary btn-lg"><i class="fas fa-times"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>


</div>

<?php require_once '../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../swalcustom.js"></script>
<script src="../../JS/servicioMixto.js"></script>