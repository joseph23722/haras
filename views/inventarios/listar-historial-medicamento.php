<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
  <!-- Título de la página -->
  <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">
    Historial Medicamentos
  </h1>

  <!-- Tabla de Medicamentos Registrados -->
  <div class="card mb-4 shadow-sm">
    <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
      <h5 class="text-center mb-0"><i class="fas fa-pills"></i> Movimientos Registrados</h5>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="d-flex justify-content-between align-items-center mb-6">
      <div class="d-flex align-items-center">
        <label for="filtroRango" class="me-2 fw-bold">Ver movimientos de:</label>
        <select id="filtroRango" class="form-select form-select-sm">
          <option value="hoy">Hoy</option>
          <option value="ultimaSemana">Última semana</option>
          <option value="ultimoMes">Último mes</option>
          <option value="todos">Todos</option>
        </select>
      </div>
      <button type="button" id="buscarHistorial" class="btn btn-primary ms-3" onclick="reloadHistorialMovimientos();">
        <i class="fas fa-search me-1"></i> Buscar
      </button>
    </div>
    <!-- Pestañas de Navegación -->
    <div class="mb-4">
      <ul class="nav nav-pills justify-content-center">
        <li class="nav-item" role="presentation">
          <a class="nav-link active" id="entradas-tab" data-bs-toggle="pill" href="#entradas" role="tab" aria-selected="true">Entradas</a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link" id="salidas-tab" data-bs-toggle="pill" href="#salidas" role="tab" aria-selected="false">Salidas</a>
        </li>
      </ul>
    </div>

    <!-- Pestañas de Entradas y Salidas -->
    <div class="tab-content">
      <div class="tab-pane fade show active" id="entradas" role="tabpanel" aria-labelledby="entradas-tab">
        <div class="table-responsive">
          <table id="tabla-entradas" class="table table-striped"></table>
        </div>
      </div>
      <div class="tab-pane fade" id="salidas" role="tabpanel" aria-labelledby="salidas-tab">
        <div class="table-responsive">
          <table id="tabla-salidas" class="table table-striped"></table>
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
<script src="/haras/vendor/medicamento/historial-medicamento.js"></script>
<script src="../../JS/listar-historial-medicamento.js"></script>