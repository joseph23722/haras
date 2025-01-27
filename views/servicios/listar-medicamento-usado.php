<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #000;">Medicamentos Aplicados</h1>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-body">
            <table id="medicamentosAplicadosTable" class="table table-striped table-bordered table-hover">
                <thead class="text-center">
                    <tr>
                        <th>Equino</th>
                        <th>Medicamento</th>
                        <th>Dosis Aplicada</th>
                        <th>Diferencia</th> <!-- Diferencia de la cantidad de una unidad, Ejemplo: si es 500mg y uso 300mg la diferencia es 200 -->
                        <th>Aplicación Anterior</th> <!-- Aplicación en el registro anterior -->
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