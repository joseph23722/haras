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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="../../JS/listar-bostas.js"></script>