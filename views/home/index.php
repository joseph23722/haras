<?php
// Definir valores predeterminados para evitar errores de variables indefinidas
$totalAlimentosDisponibles = $totalAlimentosDisponibles ?? 0;
$cambioAlimentos = $cambioAlimentos ?? '0%';
$alimentosEnStock = $alimentosEnStock ?? 0;
$alimentosBajaCantidad = $alimentosBajaCantidad ?? 0;

//123
$totalAlimentos = $totalAlimentos ?? 0;


$totalServicios = $totalServicios ?? 0;
$porcentajeServiciosPropios = $porcentajeServiciosPropios ?? 0;
$porcentajeServiciosMixtos = $porcentajeServiciosMixtos ?? 0;
$porcentajeCrecimiento = $porcentajeCrecimiento ?? 0;

$totalMedicamentosDisponibles = $totalMedicamentosDisponibles ?? 0;
$medicamentosEnStock = $medicamentosEnStock ?? 0;
$medicamentosCriticos = $medicamentosCriticos ?? 0;

?>

<?php require_once '../header.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Refined Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="http://localhost/haras/css/dashboard.css" rel="stylesheet" />
</head>

<body>

  <div class="container py-4">
    <div class="row g-3">
      <!-- Tarjeta simple de carrusel con solo título -->
      <div class="col-md-6 col-lg-4">
        <div class="card bg-gradient equinos-card">
          <h5 class="card-title fancy-title text-center">Nuestros Equinos</h5>
          <div id="carouselEquinosSimple" class="carousel slide equinos-carousel" data-bs-ride="carousel">
            <div class="carousel-inner">
              <!-- Imagen de carga predeterminada -->
              <div class="carousel-item active">
                <img src="https://via.placeholder.com/400x300?text=Cargando..." class="d-block w-100 equino-image" alt="Cargando...">
              </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselEquinosSimple" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselEquinosSimple" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Servicios Realizados GRAFICO + TOTAL -->
      <div class="col-md-6 col-lg-4">
          <div class="card">
              <h5 class="card-title">Servicios Realizados</h5>
              <h3 class="text-accent"><?php echo $totalServiciosMes; ?></h3>
              <p class="small text-muted">Total de Servicios Realizados este Mes</p>
              <div class="progress my-2">
                  <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $porcentajeProgreso; ?>%;" aria-valuenow="<?php echo $porcentajeProgreso; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $porcentajeProgreso; ?>%</div>
              </div>
              <!-- Agregamos el canvas aquí -->
              <div class="chart-container mt-3">
                  <canvas id="salesLineChart" style="max-height: 300px;"></canvas>
              </div>
          </div>
      </div>


      <!-- Servicios con iconos para Servicios Propios y Mixtos -->
      <div class="col-md-6 col-lg-4">
        <div class="card">
          <h5 class="card-title">Servicios</h5>
          <h3 class="text-primary">Total: <?php echo $totalServicios; ?> Servicios
            <span class="text-success" style="font-size: 0.8rem;">
              <?php echo '+' . $porcentajeCrecimiento . '%'; ?>
            </span>
          </h3> <!-- Número total de servicios y crecimiento -->

          <div class="d-flex justify-content-between text-muted mt-2">
            <span><i class="fas fa-tools text-blue"></i> Servicios Propios <strong><?php echo $porcentajeServiciosPropios; ?>%</strong></span> <!-- Servicios Propios -->
            <span><i class="fas fa-handshake text-purple"></i> Servicios Mixtos <strong><?php echo $porcentajeServiciosMixtos; ?>%</strong></span> <!-- Servicios Mixtos -->
          </div>

          <div class="chart-container mt-3">
            <canvas id="overviewBarChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Nueva tarjeta para Equinos Registrados -->
      <div class="col-md-6 col-lg-3">
        <div class="custom-card text-center">
          <h5 class="custom-card-title">Equinos Propios Registrados</h5>
          <h3 class="text-info"><?php echo $totalEquinosRegistrados; ?></h3>
          <p class="small custom-text-muted">Total de equinos actualmente registrados</p>
        </div>
      </div>

      <!-- Nueva tarjeta para Servicios Realizados -->
      <div class="col-md-6 col-lg-3">
        <div class="custom-card text-center">
          <h5 class="custom-card-title">Servicios Realizados</h5>
          <h3 class="text-purple"><?php echo $totalServiciosSemana; ?></h3>
          <p class="small custom-text-muted">Total de servicios realizados esta semana</p>
        </div>
      </div>

      <!-- Nueva tarjeta para Alimentos en Stock -->
      <div class="col-md-6 col-lg-3">
        <div class="custom-card text-center">
          <h5 class="custom-card-title">Alimentos en Stock</h5>
          <h3 class="text-success" id="total_alimentos">0</h3>
          <p class="small custom-text-muted">Cantidad total de alimentos disponibles</p>
        </div>
      </div>

      <!-- Nueva tarjeta para Medicamentos en Stock -->
      <div class="col-md-6 col-lg-3">
        <div class="custom-card text-center">
          <h5 class="custom-card-title">Medicamentos en Stock</h5>
          <h3 class="text-success" id="total_medicamentos">0</h3>
          <p class="small custom-text-muted">Total de medicamentos disponibles</p>
        </div>
      </div>

      <!-- Barra de progreso alimentos GRAFICO -->
      <div class="col-md-6 col-lg-6">
        <div class="card">
          <h5 class="card-title">Stock de Alimentos</h5>
          <h3 class="text-purple" id="totalAlimentos">0</h3>
          <p class="small text-muted">Cantidad total de alimentos disponibles</p>
          <div class="chart-container">
            <canvas id="earningsBarChart"></canvas>
          </div>
          <div class="d-flex justify-content-between mt-3 text-muted">
            <span>
              <i class="fas fa-dollar-sign text-blue"></i> En Stock
              <strong id="enStock">0</strong>
            </span>
            <span>
              <i class="fas fa-chart-line text-pink"></i> Baja Cantidad
              <strong id="bajaCantidad">0</strong>
            </span>
          </div>
        </div>
      </div>

      <!-- Gráfico circular para Medicamentos GRAFICO -->
      <div class="col-md-6 col-lg-6">
        <div class="card">
          <h5 class="card-title">Stock de Medicamentos</h5>
          <h3 class="text-purple" id="totalMedicamentos">0</h3>
          <p class="small text-muted">Cantidad total de medicamentos disponibles</p>
          <div class="chart-container">
            <canvas id="supportDonutChart"></canvas>
          </div>
          <div class="d-flex justify-content-between mt-3 text-muted">
            <span>
              <i class="fas fa-capsules text-blue"></i> En Stock
              <strong id="enStockMedicamentos">0</strong>
            </span>
            <span>
              <i class="fas fa-exclamation-triangle text-pink"></i> Críticos
              <strong id="criticosMedicamentos">0</strong>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript para Chart.js y Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../JS/notificaciones.js"></script>
  <script src="../../JS/dashboard.js"></script>

</body>

</html>