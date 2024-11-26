<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <div class="card-body p-1" style="background-color: #f9f9f9;">
        <div class="d-flex justify-content-center align-items-center mt-1" style="position: relative; width: 100%;">
            <h1 class="text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3; margin: 0; flex-grow: 1; text-align: center; margin-left: 170px;">
                Registro de Historial Médico
            </h1>
            <a href="./revisar-equino" class="btn btn-warning btn-lg" style="font-size: 1.1em; padding: 6px 20px;">
                Revisión Básica
            </a>
        </div>
    </div>

    <!-- Tabla para DataTable de Historiales Médicos -->
    <div class="card mt-4">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Historiales Médicos</h5>
        </div>
        <div class="card-body">
            <table id="historialTable" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Equino</th>
                        <th>Peso (kg)</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Medicamento</th>
                        <th>Dosis</th>
                        <th>Frecuencia</th>
                        <th>Vía</th>
                        <th>Registro</th>
                        <th>Fin</th>
                        <th>Observaciones</th>
                        <th>Reacciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>

<script src="/haras/vendor/veterinario/veterinario.js" defer></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="../../JS/listar-diagnostico-avanzado.js"></script>