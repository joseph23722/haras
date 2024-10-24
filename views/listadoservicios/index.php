<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
  <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #005b99;">LISTADO DE SERVICIOS</h1>
  <ol class="breadcrumb mb-4 p-2 rounded" style="background-color: #e8f1f8;">
    <li class="breadcrumb-item active" style="color: #004085; font-weight: bold;">
      <i class="fas fa-info-circle" style="color: #007bff;"></i> Seleccione el tipo de servicio para ver el listado de servicios de monta
    </li>
  </ol>

  <div class="row mb-4">
    <div class="col-md-8">
      <label for="filtroTipoServicio" class="form-label">Tipo de Servicio</label>
      <select id="filtroTipoServicio" class="form-select">
        <option value="">Seleccione Tipo de Servicio</option>
        <option value="General">Todos</option>
        <option value="Propio">Propio</option>
        <option value="Mixto">Mixto</option>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label" style="visibility: hidden;">Filtrar</label>
      <button id="btnFiltrar" class="btn btn-primary w-100">Filtrar</button>
    </div>
  </div>
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

    // Identificar las columnas para controlarlas dinámicamente
    const costoServicioColumn = table.column(8);
    const horaEntradaColumn = table.column(5); // Columna de Hora Entrada
    const horaSalidaColumn = table.column(6); // Columna de Hora Salida

    // Ocultar la columna de costo por defecto
    costoServicioColumn.visible(false);

    $('#btnFiltrar').click(function() {
      const tipoServicio = $('#filtroTipoServicio').val();

      if (tipoServicio) {
        $.ajax({
          url: '../../controllers/Propio.controller.php',
          method: 'GET',
          data: {
            tipoServicio: tipoServicio
          },
          dataType: 'json',
          success: function(data) {
            // Limpiar la tabla
            table.clear(); // Limpia todas las filas de la tabla

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
              ]);
            });

            table.draw();

            // Controlar la visibilidad de las columnas según el tipo de servicio
            if (tipoServicio === 'Mixto') {
              costoServicioColumn.visible(true);
              horaEntradaColumn.visible(true);
              horaSalidaColumn.visible(true);
            } else if (tipoServicio === 'Propio') {
              costoServicioColumn.visible(false);
              horaEntradaColumn.visible(false);
              horaSalidaColumn.visible(false);
            } else if (tipoServicio === 'General') {
              costoServicioColumn.visible(true);
              horaEntradaColumn.visible(true);
              horaSalidaColumn.visible(true);
            }
          },
          error: function(err) {
            console.error('Error al cargar datos:', err);
          }
        });
      } else {
        alert('Por favor, seleccione un tipo de servicio.');
      }
    });
  });
</script>

<?php require_once '../footer.php'; ?>