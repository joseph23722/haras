<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Registro de Historial de Herrero</h1>

    <!-- Tabla para DataTable -->
    <div class="card mt-4">
        <div class="card-header" style="background: linear-gradient(to right, #ffcc80, #ffb74d); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Historiales de Herrero</h5>
        </div>
        <div class="card-body">
            <!-- No es necesario llenar tbody manualmente, DataTables lo hace -->
            <table id="historialHerreroTable" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre del Equino</th>
                        <th>Tipo de Equino</th>
                        <th>Fecha</th>
                        <th>Trabajo Realizado</th>
                        <th>Herramienta Usada</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- JS de DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="/haras/vendor/herrero/herrero.js" defer></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inicializar DataTable
        const table = $("#historialHerreroTable").DataTable({
            ajax: {
                url: "../../controllers/herrero.controller.php?operation=consultarHistorialEquino",
                method: "GET",
                dataSrc: function(json) {
                    if (json.data) {
                        return json.data;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: json.message || 'Error al cargar los datos.',
                        });
                        return [];
                    }
                }
            },
            columns: [{
                    data: "nombreEquino"
                }, // Nombre del Equino
                {
                    data: "tipoEquino"
                }, // Tipo de Equino
                {
                    data: "fecha"
                }, // Fecha
                {
                    data: "TrabajoRealizado"
                }, // Trabajo Realizado
                {
                    data: "HerramientasUsadas"
                }, // Herramientas Usadas
                {
                    data: "observaciones"
                } // Observaciones
            ]
        });
    });
</script>