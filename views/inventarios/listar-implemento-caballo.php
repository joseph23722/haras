<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título de la página -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">
        Gestionar Implementos de Equino
    </h1>

    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
            <h5 class="text-center"><i class="fas fa-database"></i> Implementos Registrados</h5>
        </div>
        <div class="card-body" style="background-color: #f9f9f9;">
            <table id="implementos-table" class="table table-striped table-hover table-bordered">
                <thead style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
                </thead>
            </table>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>

<!-- DataTables Principal -->
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js" defer></script>

<!-- DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js" defer></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js" defer></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js" defer></script>

<!-- Dependencias de PDF y Excel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" defer></script>

<!-- Scripts de tus funcionalidades -->
<script src="../../JS/listar-implementos-caballos.js" defer></script>