<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <div class="card-body p-1" style="background-color: #f9f9f9;">
        <div class="d-flex justify-content-center align-items-center mt-1" style="position: relative; width: 100%;">
            <h1 class="text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #000; margin: 0; flex-grow: 1; text-align: center; margin-left: 170px;">
                Revisión Básica
            </h1>
            <a href="./diagnosticar-equino" class="btn btn-warning btn-lg" style="font-size: 1.1em; padding: 6px 20px;">
                Revisión Avanzada
            </a>
        </div>
    </div>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to left, #123524, #356C56); color: #EFE3C2;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos de la Revisión Básica</h5>
        </div>

        <!-- Formulario -->
        <div class="card-body p-2" style="background-color: #f9f9f9;">
            <form id="formRevisionBasica" method="post" autocomplete="off">
                <div class="row g-3">

                    <!-- Selector para el ID del Propietario -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="idPropietario" name="idPropietario">
                                <option value="">Haras Rancho Sur</option>
                            </select>
                            <label for="idPropietario" class="form-label"><i class="fas fa-home" style="color: #001F3F;"></i> Seleccione Propietario</label>
                        </div>
                    </div>

                    <!-- Selector para el Tipo de Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="idEquino" name="idEquino" required>
                                <option value="">Selecione un Equino</option>
                            </select>
                            <label for="idEquino"><i class="fas fa-horse" style="color: #001F3F;"></i> Seleccione un Equino</label>
                        </div>
                    </div>

                    <!-- Tipo de Revisión -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="tiporevision" name="tiporevision" required>
                                <option value="">Seleccione un Tipo de Revisión</option>
                                <option value="Ecografia">Ecografía</option>
                                <option value="Examen ginecológico">Examen ginecológico</option>
                                <option value="Citología">Citología</option>
                                <option value="Cultivo bacteriológico">Cultivo bacteriológico</option>
                                <option value="Biopsia endometrial">Biopsia endometrial</option>
                            </select>
                            <label for="tiporevision"><i class="fas fa-stethoscope" style="color: #001F3F;"></i> Tipo de Revisión</label>
                        </div>
                    </div>

                    <!-- Fecha de Revisión -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="fecharevision" name="fecharevision" required>
                            <label for="fecharevision"><i class="fas fa-calendar-alt" style="color: #001F3F;"></i> Fecha de Revisión</label>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="col-md-6    ">
                        <div class="form-floating">
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3" placeholder="" required></textarea>
                            <label for="observaciones"><i class="fas fa-notes-medical" style="color: #001F3F;"></i> Observaciones</label>
                        </div>
                    </div>

                    <!-- Costo de la Revisión -->
                    <div class="col-md-6" id="divCostoRevision" style="display: none;">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="costorevision" name="costorevision" step="0.01" placeholder="">
                            <label for="costorevision"><i class="fas fa-dollar-sign" style="color: #001F3F;"></i> Costo de la Revisión (USD)</label>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="col-md-12 text-end mt-3">
                        <a href="./listar-diagnostico-basico" class="btn btn-warning btn-lg" style="font-size: 1.4em; padding: 6px 20px; background-color: #123524; color: #EFE3C2;">
                            Listado Revisión
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="registrarRevision" style="background-color: #001F3F; border: none;">
                            <i class="fas fa-save"></i> Registrar Revisión
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Incluye SweetAlert -->
<script src="../../swalcustom.js"></script>

<script src="../../JS/registrar-revision.js"></script>