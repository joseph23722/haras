<?php require_once '../header.php'; ?>
<link rel="stylesheet" href="../../css/vista-gerencia.css">

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #2c3e50;">
        <i class="fas fa-clipboard-list" style="color: #00712D;"></i> Reportes
    </h1>

    <div class="card shadow border-0 mt-4" style="border-radius: 15px; background-color: #ecf0f1;">
        <div class="card-header text-center" style="background: #00712D; color: white; border-radius: 15px 15px 0 0;">
            <h5 class="m-0" style="font-weight: bold;">Seleccione un reporte</h5>
        </div>

        <div class="card-body py-4">
            <div class="row gy-3 justify-content-center">
                <!-- Botón Alimentos -->
                <div class="col-md-4 text-center">
                    <a href="../inventarios/listar-alimento" class="btn btn-report w-100">
                        <i class="fas fa-database fa-2x"></i>
                        <p>Alimentos</p>
                    </a>
                </div>
                <!-- Botón Implementos de Caballo -->
                <div class="col-md-4 text-center">
                    <a href="../inventarios/listar-implemento-caballo" class="btn btn-report w-100">
                        <i class="fas fa-horse fa-2x"></i>
                        <p>Implementos de Caballo</p>
                    </a>
                </div>
                <!-- Botón Implementos de Campo -->
                <div class="col-md-4 text-center">
                    <a href="../inventarios/listar-implemento-campo" class="btn btn-report w-100">
                        <i class="fas fa-tractor fa-2x"></i>
                        <p>Implementos de Campo</p>
                    </a>
                </div>
                <!-- Botón Acciones del Herrero -->
                <div class="col-md-4 text-center">
                    <a href="../inventarios/listar-accion-herrero" class="btn btn-report w-100">
                        <i class="fas fa-tools fa-2x"></i>
                        <p>Acciones del Herrero</p>
                    </a>
                </div>
                <!-- Botón Diagnóstico Avanzado -->
                <div class="col-md-4 text-center">
                    <a href="../historialMedico/listar-diagnostico-avanzado" class="btn btn-report w-100">
                        <i class="fas fa-stethoscope fa-2x"></i>
                        <p>Diagnóstico Avanzado</p>
                    </a>
                </div>
                <!-- Botón Listado Medicamentos -->
                <div class="col-md-4 text-center">
                    <a href="../inventarios/listar-medicamento" class="btn btn-report w-100">
                        <i class="fas fa-pills fa-2x"></i>
                        <p>Listado Medicamentos</p>
                    </a>
                </div>
                <!-- Botón Medicamentos Usados -->
                <div class="col-md-4 text-center">
                    <a href="../servicios/listar-medicamento-usado" class="btn btn-report w-100">
                        <i class="fas fa-prescription-bottle-alt fa-2x"></i>
                        <p>Medicamentos Usados</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>