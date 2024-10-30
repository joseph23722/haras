<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #005b99;">LISTADO DE BOSTAS</h1>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;"></div>
                <div class="card-body">
                    <table id="tabla-bostas" class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th>Cantidad Sacos</th>
                                <th>Peso Aproximado</th>
                                <th>Diario</th>
                                <th>Semanal</th>
                                <th>N. Semana</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán aquí -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" style="text-align: right;">Total Acumulado:</th>
                                <th id="totalacumulado"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div> <!-- .col-md-12 -->
    </div> <!-- .row -->
</div>

<?php require_once '../footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../swalcustom.js"></script>

<script>
    $(document).ready(function() {
        $('#tabla-bostas').DataTable({
            "ajax": {
                "url": "../../controllers/bostas.controller.php",
                "type": "GET",
                "data": {
                    operation: 'listarBostas'
                },
                "dataSrc": "data"
            },
            "columns": [{
                    "data": "idbosta"
                },
                {
                    "data": "fecha"
                },
                {
                    "data": "cantidadsacos"
                },
                {
                    "data": "pesoaprox"
                },
                {
                    "data": "peso_diario"
                },
                {
                    "data": "peso_semanal"
                },
                {
                    "data": "numero_semana"
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return `
                        <button class="btn btn-warning btn-sm" onclick="editarBosta(${row.idbosta})"><i class="fas fa-edit"></i> Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarBosta(${row.idbosta})"><i class="fas fa-trash-alt"></i> Eliminar</button>`;
                    }
                }
            ],
            "drawCallback": function(settings) {
                var api = this.api();
                var totalAcumulado = 0;

                api.data().each(function(value) {
                    totalAcumulado += parseFloat(value.peso_diario || 0);
                });

                $('#totalacumulado').text(totalAcumulado.toFixed(2));
            }
        });
    });

    async function eliminarBosta(idbosta) {
        if (await ask('¿Estás seguro de que deseas eliminar esta bosta?')) {
            const formData = new FormData();
            formData.append('operation', 'eliminarBosta');
            formData.append('idbosta', idbosta);

            fetch('../../controllers/bostas.controller.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status !== "error") {
                        showToast('Bosta eliminada exitosamente.', 'SUCCESS');
                        $('#tabla-bostas').DataTable().ajax.reload();
                    } else {
                        showToast(data.message, 'ERROR');
                    }
                })
                .catch(error => console.error('Error eliminando bosta:', error));
        }
    }
</script>