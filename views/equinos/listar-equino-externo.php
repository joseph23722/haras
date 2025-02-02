<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
  <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 36px; color: #000;">
    <i class="fas fa-horse-head" style="color: #000;"></i> Equinos Externos
  </h1>

  <div class="card mb-4 shadow-lg border-0">
    <div class="card-header text-center" style="background: linear-gradient(to left, #123524, #356C56); color: #EFE3C2;">
      <h5 class="m-0" style="font-weight: bold;">
        <i class="fas fa-info-circle" style="color: #EFE3C2;"></i> Listado de Equinos Externos
      </h5>
    </div>

    <div class="card-body">
      <div id="error-message" class="alert alert-danger" style="display: none;"></div>

      <!-- Tabla de Equinos Externos -->
      <table class="table table-bordered table-striped" id="equinos-table">
        <thead class="thead-light">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Sexo</th>
            <th>Tipo</th>
            <th>Estado Monta</th>
            <th>Propietario</th>
            <th>Nacionalidad</th>
            <th>Fecha Entrada</th>
            <th>Fecha Salida</th>
            <th>Detalles</th>
          </tr>
        </thead>
        <tbody>
          <!-- Los datos de los equinos se llenarán aquí mediante JavaScript -->
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require_once '../footer.php'; ?>
<script src="../../JS/listar-equino-externo.js"></script>