<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
  <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #005b99;">LISTADO DE SERVICIOS</h1>
  <ol class="breadcrumb mb-4 p-2 rounded" style="background-color: #e8f1f8;">
    <li class="breadcrumb-item active" style="color: #004085; font-weight: bold;">
      <i class="fas fa-info-circle" style="color: #007bff;"></i> Seleccione la fecha y tipo de servicio para ver el listado de servicios de monta
    </li>
  </ol>

  <div class="row mb-4">
    <div class="col-md-4">
      <label for="FechaInicio" class="form-label">Fecha Inicio</label>
      <input type="date" id="FechaInicio" class="form-control">
    </div>
    <div class="col-md-4">
      <label for="FechaFin" class="form-label">Fecha Fin</label>
      <input type="date" id="FechaFin" class="form-control">
    </div>
    <div class="col-md-4">
      <label for="filtroTipoServicio" class="form-label">Tipo de Servicio</label>
      <select id="filtroTipoServicio" class="form-select">
        <option value="">Seleccione Tipo de Servicio</option>
        <option value="Propio">Propio</option>
        <option value="Mixto">Mixto</option>
      </select>
    </div>
  </div>
  <button id="btnFiltrar" class="btn btn-primary" style="margin-bottom: 20px;">Filtrar</button>

  <table id="serviciosTable" class="table table-hover mt-4">
    <thead>
      <tr>
        <th>ID Servicio</th>
        <th>Padrillo</th>
        <th>Yegua</th>
        <th>Fecha del Servicio</th>
        <th>Detalles</th>
        <th>Hora Entrada</th>
        <th>Hora Salida</th>
        <th>Nombre Haras</th>
        <th>Costo Servicio</th>
      </tr>
    </thead>
    <tbody>
      <!-- Los datos se cargarán aquí -->
    </tbody>
  </table>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script>
  $(document).ready(function() {
    const table = $('#serviciosTable').DataTable();

    const costoServicioColumn = table.column(8);
    costoServicioColumn.visible(false);
    
    $('#btnFiltrar').click(function() {
      const fechaInicio = $('#FechaInicio').val();
      const fechaFin = $('#FechaFin').val();
      const tipoServicio = $('#filtroTipoServicio').val();

      if (fechaInicio && fechaFin && tipoServicio) {
        $.ajax({
          url: '../../controllers/Propio.controller.php',
          method: 'GET',
          data: {
            fechaInicio: fechaInicio,
            fechaFin: fechaFin,
            tipoServicio: tipoServicio
          },
          dataType: 'json',
          success: function(data) {
            // Limpiar la tabla
            table.clear();

            // Agregar los datos a la tabla
            data.forEach(function(item) {
              table.row.add([
                item.idServicio,
                item.nombrePadrillo,
                item.nombreYegua,
                item.fechaServicio,
                item.detalles || '',
                item.horaEntrada,
                item.horaSalida,
                item.nombreHaras || 'Haras Rancho Sur',
                item.costoServicio || 'Por verificar',
              ]).draw();
            });
            
            // Mostrar u ocultar la columna de costo de servicio según el tipo
            if (tipoServicio === 'Mixto') {
              costoServicioColumn.visible(true); // Mostrar columna
            } else {
              costoServicioColumn.visible(false); // Ocultar columna
            }
          },
          error: function(err) {
            console.error('Error al cargar datos:', err);
          }
        });
      } else {
        alert('Por favor, seleccione una fecha de inicio, una fecha de fin y un tipo de servicio.');
      }
    });
  });
</script>

<?php require_once '../../footer.php'; ?>