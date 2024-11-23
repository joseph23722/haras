<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Reportes</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #9be8e4); color: #003366;">
            <h5 class="text-center m-0" style="font-weight: bold;">
                <i class="fas fa-file" style="color: #3498db;"></i> Seleccione un reporte
            </h5>
        </div>

        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                <a href="../inventarios/listar-alimento" class="btn btn-primary btn-lg" role="button" style="width: 200px;">
                    <i class="fas fa-database"></i> Alimentos
                </a>
                <a href="../inventarios/listar-implemento-caballo" class="btn btn-success btn-lg" role="button" style="width: 200px;">
                    <i class="fas fa-horse"></i> Implementos de Caballo
                </a>
                <a href="../inventarios/listar-implemento-campo" class="btn btn-success btn-lg" role="button" style="width: 200px;">
                    <i class="fas fa-horse"></i> Implementos de Campo
                </a>
                <a href="../inventarios/listar-accion-herrero" class="btn btn-warning btn-lg" role="button" style="width: 200px;">
                    <i class="fas fa-tools"></i> Acciones del Herrero
                </a>
                <a href="../historialMedico/listar-diagnostico-avanzado" class="btn btn-warning btn-lg" role="button" style="width: 200px;">
                    <i class="fas fa-tools"></i> Diagnóstico Avanzado
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>