<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <div class="card-body p-1" style="background-color: #f9f9f9;">
        <div class="d-flex justify-content-center align-items-center mt-1" style="position: relative; width: 100%;">
            <h1 class="text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3; margin: 0; flex-grow: 1; text-align: center; margin-left: 170px;">
                Listado Revisión Básica
            </h1>
        </div>
    </div>

    <!-- Tabla para DataTable de Historiales Médicos -->
    <div class="card mt-4">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Revisiones Registradas</h5>
        </div>
        <div class="card-body">
            <table id="listadobasico" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Equino</th>
                        <th>Propietario</th>
                        <th>Tipo Revisión</th>
                        <th>Fecha Revisión</th>
                        <th>Observaciones</th>
                        <th>Costo Revisión</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>
<script src="../../JS/listar-diagnostico-basico.js"></script>