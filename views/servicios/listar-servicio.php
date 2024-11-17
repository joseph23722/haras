<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
  <h1 class="mt-4 text-center text-uppercase fw-bold" style="font-size: 32px; color: #2c3e50;">Listado de Servicios de Monta</h1>

  <ol class="breadcrumb mb-4 p-2 rounded shadow-sm" style="background-color: #f8f9fa;">
    <li class="breadcrumb-item active" style="color: #34495e; font-weight: bold;">
      <i class="fas fa-info-circle me-2" style="color: #3498db;"></i> Seleccione el tipo de servicio para ver el listado de servicios de monta
    </li>
  </ol>

  <div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
      <div class="row g-3 align-items-center">
        <div class="col-md-8">
          <label for="filtroTipoServicio" class="form-label fw-bold">Tipo de Servicio</label>
          <select id="filtroTipoServicio" class="form-select" style="background-color: #ecf0f1; border-color: #dcdde1;">
            <option value="">Seleccione Tipo de Servicio</option>
            <option value="General">Todos</option>
            <option value="Propio">Propio</option>
            <option value="Mixto">Mixto</option>
          </select>
          <div id="mensaje" style="margin-top: 10px; color: green; font-weight: bold;"></div>
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <button id="btnFiltrar" class="btn btn-primary w-100 fw-bold" style="background-color: #a0ffb8; border-color: #a0ffb8; color: #000;">
            Filtrar <i class="fas fa-filter ms-2"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="table-responsive shadow-sm rounded" style="overflow-x: hidden;">
    <div class="d-flex justify-content-between mb-2">
      <!-- Campo de "Mostrar entradas" y Búsqueda alineados a la izquierda -->
      <div class="dataTables_length" id="serviciosTable_length"></div>
      <div class="dataTables_filter" id="serviciosTable_filter" style="text-align: left;"></div>
    </div>
    <table id="serviciosTable" class="table table-hover mt-4 w-100" style="background-color: #a0ffb8;">
      <thead style="background-color: #a0ffb8; color: white;">
        <tr class="text-center">
          <th><i class="fas fa-hashtag"></i> ID Servicio</th>
          <th><i class="fas fa-horse-head"></i> Padrillo</th>
          <th><i class="fas fa-female"></i> Yegua</th>
          <th><i class="fas fa-users"></i> Equino Externo</th>
          <th><i class="fas fa-calendar-alt"></i> Fecha del Servicio</th>
          <th><i class="fas fa-info-circle"></i> Detalles</th>
          <th><i class="fas fa-clock"></i> Hora Entrada</th>
          <th><i class="fas fa-clock"></i> Hora Salida</th>
          <th><i class="fas fa-home"></i> Nombre Haras</th>
          <th><i class="fas fa-dollar-sign"></i> Costo Servicio</th>
        </tr>
      </thead>
      <tbody>
        <!-- Los datos se cargarán aquí -->
      </tbody>
    </table>
  </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<!-- CSS para alinear el campo de búsqueda -->
<style>
  /* Ajuste del campo de búsqueda para alinear a la izquierda */
  .dataTables_wrapper .dataTables_length,
  .dataTables_wrapper .dataTables_filter {
    display: inline-block;
    margin-bottom: 1px;
  }

  .dataTables_wrapper .dataTables_filter {
    margin-left: 1px;
    /* Ajusta este margen para posicionar más a la izquierda */
  }

  .dataTables_wrapper .dataTables_filter input {
    border: 1px solid #dcdde1;
    border-radius: 4px;
    padding: 5px 10px;
    width: 200px;
  }

  .dataTables_wrapper .dataTables_length label,
  .dataTables_wrapper .dataTables_filter label {
    font-weight: bold;
    color: #34495e;
  }
</style>

<script src="../../JS/listar-servicio.js"></script>

<?php require_once '../footer.php'; ?>