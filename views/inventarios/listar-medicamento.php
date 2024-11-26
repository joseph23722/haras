<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título de la página -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">
        Gestionar Medicamentos
    </h1>

    <!-- Tabla de Medicamentos Registrados -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
            <h5 class="text-center mb-0"><i class="fas fa-pills"></i> Medicamentos Registrados</h5>
        </div>
        <div class="card-body" style="background-color: #f9f9f9;">
            <!-- Tabla de medicamentos con botones de exportación y búsqueda integrados en la parte superior -->
            <table id="tabla-medicamentos" class="table table-striped table-hover table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Lote</th>
                        <th>Presentación</th>
                        <th>Dosis</th>
                        <th>Tipo</th>
                        <th>Fecha Caducidad</th>
                        <th>Cantidad Stock</th>
                        <th>Costo Unitario</th>
                        <th>Fecha Registro</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php require_once '../footer.php'; ?>

<!-- Cargar jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- Cargar DataTables y sus dependencias -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<!-- Scripts personalizados -->
<script src="/haras/vendor/medicamento/listar-medicamento.js"></script>
<script src="../../JS/listar-medicamento.js"></script>