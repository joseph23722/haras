<?php require_once '../header.php'; ?>
<div class="container-fluid px-4">
  <!-- Título de la página -->
  <h1 class="mt-4 text-center text-uppercase fw-bold" style="font-size: 40px; color: #2C3E50; letter-spacing: 2px; font-family: 'Roboto', sans-serif;">
    Historial Alimentos
  </h1>

  <!-- Tabla de Medicamentos Registrados -->
  <div class="card mb-4 shadow-lg rounded-3" style="background-color: #FFFFFF; border: none;">
    <div class="card-header" style="background: #1ABC9C; color: #FFFFFF; border-radius: 15px 15px 0 0; border-bottom: 4px solid #16A085;">
      <h5 class="text-center mb-0">
        <i class="fas fa-apple-alt" style="color: #F39C12;"></i> Movimientos Registrados
      </h5>
    </div>

    <!-- Opciones de Filtrado Rápido -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-4" style="background-color: #ECF0F1; border-radius: 10px;">
      <div class="d-flex align-items-center">
        <label for="filtroRangoAlimentos" class="me-2 fw-bold" style="font-size: 16px; color: #2C3E50;">Ver movimientos de:</label>
        <select id="filtroRangoAlimentos" class="form-select form-select-sm shadow-sm" style="width: 220px; border-radius: 40px; background-color: #F9F9F9; border: 1px solid #BDC3C7;">
          <option value="hoy">Hoy</option>
          <option value="ultimaSemana">Última semana</option>
          <option value="ultimoMes">Último mes</option>
          <option value="todos">Todos</option>
        </select>
      </div>
    </div>

    <!-- Pestañas para Entrada y Salida -->
    <ul class="nav nav-tabs mb-3" id="historialTabAlimentos" role="tablist" style="border-bottom: 2px solid #2980B9;">
      <li class="nav-item" role="presentation">
        <button class="nav-link active rounded-pill" id="entradas-tab-alimentos" data-bs-toggle="tab" data-bs-target="#entradasAlimentos" type="button" role="tab" aria-controls="entradasAlimentos" aria-selected="true" style="font-weight: 600; color: #2C3E50;">
          Entradas
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill" id="salidas-tab-alimentos" data-bs-toggle="tab" data-bs-target="#salidasAlimentos" type="button" role="tab" aria-controls="salidasAlimentos" aria-selected="false" style="font-weight: 600; color: #2C3E50;">
          Salidas
        </button>
      </li>
    </ul>

    <!-- Contenido de las Pestañas -->
    <div class="tab-content">
      <!-- Tabla de Entradas de Alimentos -->
      <div class="tab-pane fade show active" id="entradasAlimentos" role="tabpanel" aria-labelledby="entradas-tab-alimentos">
        <div class="table-responsive">
          <table id="tabla-entradas-alimentos" class="table table-bordered table-hover table-striped table-sm" style="background-color: #FFFFFF; border-radius: 10px; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);">
            <!-- El contenido de la tabla se llenará dinámicamente -->
          </table>
        </div>
      </div>

      <!-- Tabla de Salidas de Alimentos -->
      <div class="tab-pane fade" id="salidasAlimentos" role="tabpanel" aria-labelledby="salidas-tab-alimentos">
        <div class="table-responsive">
          <table id="tabla-salidas-alimentos" class="table table-bordered table-hover table-striped table-sm" style="background-color: #FFFFFF; border-radius: 10px; box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);">
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
<script src="/haras/vendor/alimentos/historial-alimentos.js"></script>
<script src="../../JS/listar-historial-alimento.js"></script>