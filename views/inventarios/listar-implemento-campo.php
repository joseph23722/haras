<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título de la página -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #000;">
        Listado Implementos de Campo
    </h1>
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(to left, #123524, #356C56); color: #EFE3C2;">
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="../../JS/listar-implementos-campos.js"></script>