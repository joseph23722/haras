<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Registro de Bostas</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;"></div>

        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-registro-bostas" autocomplete="off">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="pesoaprox" id="pesoaprox"  placeholder="" class="form-control" required autofocus>
                            <label for="pesoaprox"><i class="fas fa-poop" style="color: #00b4d8;"></i> Cantidad de Bostas (kg)</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="cantidadsacos" id="cantidadsacos"  placeholder="" class="form-control" required>
                            <label for="cantidadsacos"><i class="fas fa-bag-shopping" style="color: #ffa500;"></i> Cantidad de Sacos</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fecha" id="fecha"  placeholder="" class="form-control" required>
                            <label for="fecha"><i class="fas fa-calendar-alt" style="color: #32cd32;"></i> Fecha de Registro</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="peso_diario" id="peso_diario"  placeholder=""class="form-control" disabled readonly>
                            <label for="peso_diario"><i class="fas fa-weight-hanging" style="color: #dc3545;"></i> Peso Diario (kg)</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="peso_semanal" id="peso_semanal" class="form-control" disabled readonly>
                            <label for="peso_semanal"><i class="fas fa-weight-hanging" style="color: #dc3545;"></i> Peso Semanal (kg)</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="peso_mensual" id="peso_mensual" class="form-control" disabled readonly>
                            <label for="peso_mensual"><i class="fas fa-weight-hanging" style="color: #dc3545;"></i> Peso Mensual (kg)</label>
                        </div>
                    </div>

                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="registrar-bostas" style="background-color: #0077b6; border: none;">
                            <i class="fas fa-save"></i> Registrar Bostas
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
<script src="../../JS/registrar-bostas.js"></script>