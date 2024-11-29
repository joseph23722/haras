<?php require_once '../header.php'; ?>
<div class="container-fluid px-4">
  <!-- Título de la página -->
  <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">
    Historial Alimentos
  </h1>

  <!-- Tabla de Medicamentos Registrados -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
      <h5 class="text-center mb-0"><i class="fas fa-apple-alt" style="color: #3498db;"></i> Movimientos Registrados</h5>
    </div>
    <!-- Opciones de Filtrado Rápido -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div class="d-flex align-items-center">
        <label for="filtroRangoAlimentos" class="me-2 fw-bold">Ver movimientos de:</label>
        <select id="filtroRangoAlimentos" class="form-select form-select-sm">
          <option value="hoy">Hoy</option>
          <option value="ultimaSemana">Última semana</option>
          <option value="ultimoMes">Último mes</option>
          <option value="todos">Todos</option>
        </select>
      </div>
      <button type="button" id="buscarHistorialAlimentos" class="btn btn-primary ms-3">
        <i class="fas fa-search me-1"></i>Buscar
      </button>
    </div>

    <!-- Pestañas para Entrada y Salida -->
    <ul class="nav nav-tabs mb-3" id="historialTabAlimentos" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="entradas-tab-alimentos" data-bs-toggle="tab" data-bs-target="#entradasAlimentos" type="button" role="tab" aria-controls="entradasAlimentos" aria-selected="true">Entradas</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="salidas-tab-alimentos" data-bs-toggle="tab" data-bs-target="#salidasAlimentos" type="button" role="tab" aria-controls="salidasAlimentos" aria-selected="false">Salidas</button>
      </li>
    </ul>

    <!-- Contenido de las Pestañas -->
    <div class="tab-content">
      <!-- Tabla de Entradas de Alimentos -->
      <div class="tab-pane fade show active" id="entradasAlimentos" role="tabpanel" aria-labelledby="entradas-tab-alimentos">
        <div class="table-responsive">
          <table id="tabla-entradas-alimentos" class="table table-bordered table-hover table-striped"></table>
        </div>
      </div>

      <!-- Tabla de Salidas de Alimentos -->
      <div class="tab-pane fade" id="salidasAlimentos" role="tabpanel" aria-labelledby="salidas-tab-alimentos">
        <div class="table-responsive">
          <table id="tabla-salidas-alimentos" class="table table-bordered table-hover table-striped"></table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once '../footer.php'; ?>
<!-- Cargar jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Cargar DataTables y sus dependencias -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="/haras/vendor/alimentos/historial-alimentos.js"></script>
<script src="../../JS/listar-historial-alimento.js"></script>