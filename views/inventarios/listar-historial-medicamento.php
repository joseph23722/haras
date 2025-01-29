<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
  <!-- Título de la página -->
  <h1 class="mt-4 text-center text-uppercase fw-bold" style="font-size: 40px; color: #000; letter-spacing: 2px; font-family: 'Roboto', sans-serif;">
    Historial Medicamentos
  </h1>

  <!-- Tabla de Medicamentos Registrados -->
  <div class="card mb-4 shadow-lg rounded-3" style="background-color: #FFFFFF; border: none;">
    <div class="card-header" style="background: linear-gradient(to left, #123524, #356C56); color: #EFE3C2">
      <h5 class="text-center mb-0">
        <i class="fas fa-pills" style="color: #EFE3C2;"></i> Movimientos Registrados
      </h5>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-4" style="background-color: #ECF0F1; border-radius: 10px;">
      <div class="d-flex align-items-center">
        <label for="filtroRango" class="me-2 fw-bold" style="font-size: 16px; color: #2C3E50;">Ver movimientos de:</label>
        <select id="filtroRango" class="form-select form-select-sm shadow-sm" style="width: 220px; border-radius: 40px; background-color: #F9F9F9; border: 1px solid #BDC3C7;">
          <option value="hoy">Hoy</option>
          <option value="ultimaSemana">Última semana</option>
          <option value="ultimoMes">Último mes</option>
          <option value="todos">Todos</option>
        </select>
      </div>
    </div>

    <!-- Pestañas para Entrada y Salida -->
    <ul class="nav nav-tabs mb-3" id="historialTab" role="tablist" style="border-bottom: 2px solid #2980B9;">
      <li class="nav-item" role="presentation">
        <button class="nav-link active rounded-pill" id="entradas-tab" data-bs-toggle="tab" data-bs-target="#entradas" type="button" role="tab" aria-controls="entradas" aria-selected="true" style="font-weight: 600; color: #2C3E50;">
          Entradas
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill" id="salidas-tab" data-bs-toggle="tab" data-bs-target="#salidas" type="button" role="tab" aria-controls="salidas" aria-selected="false" style="font-weight: 600; color: #2C3E50;">
          Salidas
        </button>
      </li>
    </ul>

    <!-- Contenido de las Pestañas -->
    <div class="tab-content">
      <!-- Tabla de Entradas de Medicamentos -->
      <div class="tab-pane fade show active" id="entradas" role="tabpanel" aria-labelledby="entradas-tab">
        <div class="table-responsive">
          <table id="tabla-entradas" class="table table-bordered table-hover table-striped table-sm" style="background-color: #FFFFFF; border-radius: 10px; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);">
            <!-- El contenido de la tabla se llenará dinámicamente -->
          </table>
        </div>
      </div>

      <!-- Tabla de Salidas de Medicamentos -->
      <div class="tab-pane fade" id="salidas" role="tabpanel" aria-labelledby="salidas-tab">
        <div class="table-responsive">
          <table id="tabla-salidas" class="table table-bordered table-hover table-striped table-sm" style="background-color: #FFFFFF; border-radius: 10px; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);">
            <!-- El contenido de la tabla se llenará dinámicamente -->
          </table>
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