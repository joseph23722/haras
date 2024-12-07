<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #005b99;">Medicamentos Aplicados</h1>
    <ol class="breadcrumb mb-4 p-2 rounded" style="background-color: #e8f1f8;">
        <li class="breadcrumb-item active" style="color: #004085; font-weight: bold;">
            <i class="fas fa-info-circle" style="color: #007bff;"></i> Listado de medicamentos aplicados
        </li>
    </ol>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <table id="medicamentosAplicadosTable" class="table table-striped table-bordered table-hover">
                <thead class="text-center">
                    <tr>
                        <th>Equino</th>
                        <th>Medicamento</th>
                        <th>Dosis Aplicada</th>
                        <th>Stock Restante</th>
                        <th>Stock Antes</th>
                        <th>Stock Actual</th>
                        <th>Estado</th>
                        <th>Fecha Aplicación</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Datos dinámicos se llenarán aquí -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>
<script src="../../JS/lista-medicamentos-aplicados.js"></script>