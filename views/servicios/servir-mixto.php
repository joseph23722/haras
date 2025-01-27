<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #000;">Registro de Servicio Mixto</h1>
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header" style="background: linear-gradient(to left, #123524, #356C56);">
            <h5 class="mb-0 text-uppercase" style="color: #EFE3C2;">Datos del Servicio Mixto</h5>
        </div>
        <div class="card-body p-4 bg-white rounded">
            <div id="mensaje" class="alert alert-info" style="display: none;"></div>
            <form action="" id="form-registro-servicio-mixto" autocomplete="off">
                <!-- Grupo: Selección de Equinos -->
                <fieldset class="border p-3 mb-4">
                    <legend class="w-auto px-3" style="color: #001F3F; font-size: 1.2em;">Seleccionar Equinos</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="idEquinoMacho" id="idEquinoMacho" class="form-select" autofocus>
                                    <option value="">Seleccione Padrillo</option>
                                </select>
                                <label for="idEquinoMacho"><i class="fas fa-horse-head" style="color: #001F3F;"></i> Padrillo</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="idEquinoHembra" id="idEquinoHembra" class="form-select">
                                    <option value="">Seleccione Yegua</option>
                                </select>
                                <label for="idEquinoHembra"><i class="fas fa-horse" style="color: #001F3F;"></i> Yegua</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="idPropietario" id="idPropietario" class="form-select">
                                    <option value="">Seleccione Propietario</option>
                                </select>
                                <label for="idPropietario"><i class="fas fa-home" style="color: #001F3F;"></i> Propietario</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="idEquinoExterno" id="idEquinoExterno" class="form-select">
                                    <option value="">Seleccione Equino Externo</option>
                                </select>
                                <label for="idEquinoExterno"><i class="fas fa-horse" style="color: #001F3F;"></i> Equino Externo</label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- Grupo: Información del Servicio -->
                <fieldset class="border p-3 mb-4">
                    <legend class="w-auto px-3" style="color: #001F3F; font-size: 1.2em;">Información del Servicio</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" name="fechaServicio" id="fechaServicio" class="form-control" required>
                                <label for="fechaServicio"><i class="fas fa-calendar-alt" style="color: #001F3F;"></i> Fecha del Servicio</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="time" name="horaEntrada" id="horaEntrada" min="06:00" max="18:00" class="form-control" required>
                                <label for="horaEntrada"><i class="fas fa-clock" style="color: #001F3F;"></i> Hora de Entrada</label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="time" name="horaSalida" id="horaSalida" min="06:00" max="18:00" class="form-control" required>
                                <label for="horaSalida"><i class="fas fa-clock" style="color: #001F3F;"></i> Hora de Salida</label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- Grupo: Medicamentos -->
                <fieldset class="border p-3 mb-4">
                    <legend class="w-auto px-3" style="color: #001F3F; font-size: 1.2em;">Información del Medicamento</legend>
                    <div class="row g-3">
                        <!-- Selección de Medicamento -->
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="idMedicamento" id="idDetalleMed" class="form-select">
                                    <option value="">Seleccione Medicamento (Opcional)</option>
                                </select>
                                <label for="idDetalleMed"><i class="fas fa-pills" style="color: #001F3F;"></i> Detalle Medicamento</label>
                            </div>
                        </div>

                        <!-- Campos Relacionados con Medicamento -->
                        <div id="medicamentoCampos" style="display: none; margin-top: 20px;">
                            <div class="row g-3">
                                <!-- ¿El medicamento se usará para? -->
                                <div class="col-md-12">
                                    <label class="form-label" style="font-weight: bold; color: #001F3F;">¿El medicamento se usará para?</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="usoMedicamento" id="usoPadrillo" value="padrillo">
                                        <label class="form-check-label" for="usoPadrillo">
                                            <i class="fas fa-horse-head" style="color: #001F3F;"></i> Padrillo
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="usoMedicamento" id="usoYegua" value="yegua">
                                        <label class="form-check-label" for="usoYegua">
                                            <i class="fas fa-horse" style="color: #001F3F;"></i> Yegua
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="usoMedicamento" id="usoExterno" value="externo">
                                        <label class="form-check-label" for="usoExterno">
                                            <i class="fas fa-horse" style="color: #001F3F;"></i> Equino Externo
                                        </label>
                                    </div>
                                </div>

                                <!-- Unidad -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select name="unidad" id="unidad" class="form-select">
                                            <option value="">Seleccione Unidad</option>
                                        </select>
                                        <label for="unidad"><i class="fas fa-ruler" style="color: #001F3F;"></i> Unidad</label>
                                    </div>
                                </div>

                                <!-- Cantidad Aplicada -->
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="number" name="cantidadAplicada" id="cantidadAplicada" class="form-control" step="0.01">
                                        <label for="cantidadAplicada"><i class="fas fa-balance-scale" style="color: #001F3F;"></i> Cantidad Aplicada (mg)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>


                <!-- Grupo: Costos y Detalles -->
                <fieldset class="border p-3 mb-4">
                    <legend class="w-auto px-3" style="color: #001F3F; font-size: 1.2em;">Costos y Detalles</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" name="costoServicio" id="costoServicio" placeholder="" class="form-control">
                                <label for="costoServicio"><i class="fas fa-money-bill-wave" style="color: #001F3F;"></i> Costo Servicio</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <textarea name="detalles" id="detalles" placeholder="" class="form-control"></textarea>
                                <label for="detalles"><i class="fas fa-info-circle" style="color: #001F3F;"></i> Detalles</label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- Botones -->
                <div class="text-end mt-3">
                    <a href="./listar-medicamento-usado" class="btn btn-primary btn-lg" style="background-color: #001F3F; color: #EFE3C2;">
                        <i class="fas fa-save"></i> Medicamentos Aplicados
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg" style="background-color: #123524; border-color: #123524; color: #EFE3C2;">
                        <i class="fas fa-save"></i> Registrar Servicio
                    </button>
                    <button type="reset" class="btn btn-secondary btn-lg"><i class="fas fa-times"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>


</div>

<?php require_once '../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../swalcustom.js"></script>
<script src="../../JS/servicioMixto.js"></script>