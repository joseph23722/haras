<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
  <h1 class="mt-4 text-center text-uppercase fw-bold" style="font-size: 32px; color: #000;">Listado de Servicios de Monta</h1>
  <ol class="breadcrumb mb-4 p-2 rounded shadow-sm" style="background-color: #123524;">
    <li class="breadcrumb-item active" style="color: #EFE3C2; font-weight: bold;">
      <i class="fas fa-info-circle me-2" style="color: #EFE3C2;"></i> Seleccione el tipo de servicio para ver el listado de servicios de monta
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
          <button id="btnFiltrar" class="btn btn-primary w-100 fw-bold" style="background-color: #001F3F; border-color: #efe3c2; color: #EFE3C2;">
            Filtrar <i class="fas fa-filter ms-2"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <table id="serviciosTable" class="table table-striped table-hover table-bordered">
    <thead style="background-color: #123524;">
      <tr class="text-center">
        <th><i class="fas fa-hashtag"></i> ID</th>
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
    <tbody id="tbodyServicios">
      <!-- Los datos se cargarán aquí -->
    </tbody>
  </table>
</div>

<?php require_once '../footer.php'; ?>
<script src="../../JS/listar-servicio.js"></script>