<?php require_once '../header.php'; ?>
<!-- Historial de Movimientos de Implementos -->
<div class="container-fluid px-4">
    <!-- Título de la página -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">
        Historial Movimiento de Implementos
    </h1>

    <!-- Contenido de las Pestañas -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5 class="text-center mb-0"><i class="fas fa-box" style="color: #3498db;"></i> Movimientos Registrados</h5>
        </div>

        <!-- Pestañas para Entrada y Salida -->
        <ul class="nav nav-tabs mb-3" id="historialTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="entradas-tab" data-bs-toggle="tab" data-bs-target="#entradas" type="button" role="tab" aria-controls="entradas" aria-selected="true">Entradas</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="salidas-tab" data-bs-toggle="tab" data-bs-target="#salidas" type="button" role="tab" aria-controls="salidas" aria-selected="false">Salidas</button>
            </li>
        </ul>

        <!-- Contenido de las Pestañas -->
        <div class="tab-content">
            <!-- Tabla de Entradas de Implementos -->
            <div class="tab-pane fade show active" id="entradas" role="tabpanel" aria-labelledby="entradas-tab">
                <div class="table-responsive">
                    <table id="tabla-entradas" class="table table-bordered table-hover table-striped">
                        <thead class="table-primary">
                            <tr class="text-center">
                                <th>ID Historial</th>
                                <th>Nombre Producto</th>
                                <th>Precio U.</th>
                                <th>Cantidad</th>
                                <th>Descripcion</th>
                                <th>Fecha Movimiento</th>
                                <th>Nombre Inventario</th>
                            </tr>
                        </thead>
                        <tbody id="historial-entradas-table">
                            <!-- Los datos se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabla de Salidas de Implementos -->
            <div class="tab-pane fade" id="salidas" role="tabpanel" aria-labelledby="salidas-tab">
                <div class="table-responsive">
                    <table id="tabla-salidas" class="table table-bordered table-hover table-striped">
                        <thead class="table-danger">
                            <tr class="text-center">
                                <th>ID Historial</th>
                                <th>Nombre Producto</th>
                                <th>Precio U.</th>
                                <th>Cantidad</th>
                                <th>Descripcion</th>
                                <th>Fecha</th>
                                <th>Nombre Inventario</th>
                            </tr>
                        </thead>
                        <tbody id="historial-salidas-table">
                            <!-- Los datos se cargarán dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>
<script src="../../JS/listar-historial-I-campo.js"></script>