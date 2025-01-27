<?php require_once '../header.php'; ?>

<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #000;">
        Listado Alimentos
    </h1>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-md-3">
            <label for="ordenSelect" class="form-label">Ordenar por Cantidad en Stock:</label>
            <select id="ordenSelect" class="form-select">
                <option value="">Seleccione</option>
                <option value="ASC">Menor a Mayor</option>
                <option value="DESC">Mayor a Menor</option>
            </select>
        </div>
        <div class="col-md-3 align-self-end">
            <button id="btn-buscar" class="btn" style="background-color: #001F3F; color: #EFE3C2;">Buscar</button>
        </div>
    </div>

    <!-- Tabla de Alimentos Registrados -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(to left, #123524, #356C56); color: #EFE3C2;">
            <h5 class="text-center"><i class="fas fa-database"></i> Alimentos Registrados</h5>
        </div>
        <div class="card-body" style="background-color: #f9f9f9;">
            <table id="alimentos-table" class="table table-striped table-hover table-bordered">
                <thead style="background-color: #efe3c2; color: #EFE3C2;">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>U.M</th>
                        <th>Lote</th>
                        <th>Stock Actual</th>
                        <th>Stock mínimo</th>
                        <th>Costo</th>
                        <th>Fecha Caducidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


<!-- Contenedor de mensajes dinámicos -->
<div id="mensaje"></div>

<?php require_once '../footer.php'; ?>
<script src="/haras/vendor/alimentos/historial-alimentos.js" defer></script>
<script src="/haras/vendor/alimentos/listar-alimentos.js" defer></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="../../JS/listar-alimentos.js"></script>