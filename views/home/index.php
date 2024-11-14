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
                <div class="carousel-item active">
                <img src="https://www.geekmi.news/img/2021/05/09/gokx-2.jpg?__scale=w:1200,h:1200,t:2" class="d-block w-100 equino-image" alt="Equino 1">
                </div>
                <div class="carousel-item">
                <img src="https://example.com/image2.jpg" class="d-block w-100 equino-image" alt="Equino 2">
                </div>
                <div class="carousel-item">
                <img src="https://example.com/image3.jpg" class="d-block w-100 equino-image" alt="Equino 3">
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

    <!-- Servicios Realizados con barra de progreso -->
    <div class="col-md-6 col-lg-4">
      <div class="card">
        <h5 class="card-title">Servicios Realizados</h5>
        <h3 class="text-accent"><?php echo $totalServiciosMes; ?></h3>
        <p class="small text-muted">Total de Servicios Realizados este Mes</p>
        <div class="progress my-2">
          <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $porcentajeProgreso; ?>%;" aria-valuenow="<?php echo $porcentajeProgreso; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $porcentajeProgreso; ?>%</div>
        </div>
        <div class="chart-container mt-3">
          <canvas id="salesLineChart"></canvas>
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
          <h5 class="custom-card-title">Equinos Registrados</h5>
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

    <!-- Nueva tarjeta para Medicamentos en Stock -->
    <div class="col-md-6 col-lg-3">
      <div class="custom-card text-center">
          <h5 class="custom-card-title">Medicamentos en Stock</h5>
          <h3 class="text-success"><?php echo $totalMedicamentos; ?></h3>
          <p class="small custom-text-muted">Total de medicamentos disponibles</p>
      </div>
    </div>

    <!-- Nueva tarjeta para Alimentos en Stock -->
    <div class="col-md-6 col-lg-3">
      <div class="custom-card text-center">
          <h5 class="custom-card-title">Alimentos en Stock</h5>
          <h3 class="text-danger"><?php echo $totalAlimentos; ?></h3>
          <p class="small custom-text-muted">Cantidad total de alimentos disponibles</p>
      </div>
    </div>



    <!-- Barra de progreso alimentos -->
    <div class="col-md-6 col-lg-6">
      <div class="card">
        <h5 class="card-title">Stock de Alimentos</h5>
        <h3 class="text-purple"><?php echo $totalAlimentosDisponibles; ?> <span class="text-success" style="font-size: 0.8rem;"><?php echo $cambioAlimentos; ?></span></h3>
        <p class="small text-muted">Cantidad total de alimentos disponibles</p>
        <div class="chart-container">
          <canvas id="earningsBarChart"></canvas>
        </div>
        <div class="d-flex justify-content-between mt-3 text-muted">
          <span><i class="fas fa-dollar-sign text-blue"></i> En Stock <strong><?php echo $alimentosEnStock; ?></strong></span>
          <span><i class="fas fa-chart-line text-pink"></i> Baja Cantidad <strong><?php echo $alimentosBajaCantidad; ?></strong></span>
        </div>
      </div>
    </div>


    <!-- Gráfico y barra de medicamento -->
    <div class="col-md-6 col-lg-6">
      <div class="card support-tracker-card">
        <h5 class="card-title small-title">Stock de Medicamentos</h5>
        <h3 class="text-info small-number"><?php echo $totalMedicamentosDisponibles; ?></h3>
        <p class="small text-muted">Cantidad total de medicamentos disponibles</p>
        <div class="chart-container small-chart">
          <canvas id="supportDonutChart"></canvas>
        </div>
        <div class="d-flex justify-content-between mt-2 text-muted">
          <span class="small-info"><i class="fas fa-ticket-alt text-blue"></i> En Stock <strong><?php echo $medicamentosEnStock; ?></strong></span>
          <span class="small-info"><i class="fas fa-envelope-open-text text-pink"></i> Críticos <strong><?php echo $medicamentosCriticos; ?></strong></span>
        </div>
      </div>
    </div>


<!-- JavaScript para Chart.js y Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>




<script>
  // Definir las opciones de animación globalmente
  const animationOptions = { duration: 1500, easing: 'easeOutBounce' };

  document.addEventListener("DOMContentLoaded", () => {
    cargarDatosDashboard();
  });

  function cargarDatosDashboard() {



    // Servicios Realizados este Mes
    fetch('../../controllers/dashboard.controller.php?action=servicios_mensual&meta=100')
      .then(response => validarRespuestaJSON(response))
      .then(data => {
        if (data) {
          const totalServiciosMesElement = document.querySelector(".text-accent");
          if (totalServiciosMesElement) {
            totalServiciosMesElement.textContent = data.totalServicios || 0;
          }
          const progressBar = document.querySelector(".progress-bar");
          if (progressBar) {
            progressBar.style.width = `${data.porcentajeProgreso || 0}%`;
            progressBar.textContent = `${data.porcentajeProgreso || 0}%`;
            progressBar.setAttribute('aria-valuenow', data.porcentajeProgreso || 0);
          }
          actualizarGraficoLineal(data.seriesMensual || []);
        }
      })
      .catch(error => console.error("Error fetching servicios_mensual:", error));




    // Servicios Totales y Detalle de Propios/Mixtos
    fetch('../../controllers/dashboard.controller.php?action=resumen_servicios')
      .then(response => validarRespuestaJSON(response))
      .then(data => {
        if (data) {
          const totalServiciosElement = document.querySelector(".text-primary");
          if (totalServiciosElement) {
            totalServiciosElement.textContent = `Total: ${data.totalServicios || 0} Servicios`;
          }
          const crecimientoElement = document.querySelector(".text-success");
          if (crecimientoElement) {
            crecimientoElement.textContent = `+${data.porcentajeCrecimiento || 0}%`;
          }
          const toolsElement = document.querySelector(".fas.fa-tools");
          const handshakeElement = document.querySelector(".fas.fa-handshake");
          if (toolsElement && toolsElement.nextElementSibling) {
            toolsElement.nextElementSibling.textContent = `${data.porcentajeServiciosPropios || 0}%`;
          }
          if (handshakeElement && handshakeElement.nextElementSibling) {
            handshakeElement.nextElementSibling.textContent = `${data.porcentajeServiciosMixtos || 0}%`;
          }
          actualizarGraficoBarrasServicios(data.seriesServicios || [0, 0]);
        }
      })
      .catch(error => console.error("Error fetching resumen_servicios:", error));




    // Equinos Registrados
    fetch('../../controllers/dashboard.controller.php?action=total_equinos')
      .then(response => validarRespuestaJSON(response))
      .then(data => {
        const equinosElement = document.querySelector(".custom-card.text-center .text-info");
        if (equinosElement) {
          equinosElement.textContent = data.totalEquinos || 0;
        }
      })
      .catch(error => console.error("Error fetching total_equinos:", error));



    // Servicios Realizados esta Semana
    fetch('../../controllers/dashboard.controller.php?action=servicios_semana')
      .then(response => validarRespuestaJSON(response))
      .then(data => {
        const serviciosSemanaElement = document.querySelector(".custom-card.text-center .text-purple");
        if (serviciosSemanaElement) {
          serviciosSemanaElement.textContent = data.totalServicios || 0;
        }
      })
      .catch(error => console.error("Error fetching servicios_semana:", error));




    // Stock de Medicamentos
    fetch('../../controllers/dashboard.controller.php?action=medicamentos_stock')
      .then(response => validarRespuestaJSON(response))
      .then(data => {
        console.log("Datos de medicamentos:", data);
        if (data) {
          const stockTotalElement = document.querySelector(".text-info.small-number");
          if (stockTotalElement) {
            stockTotalElement.textContent = data.stock_total || 0;
          }
          const ticketElement = document.querySelector(".fas.fa-ticket-alt");
          const envelopeElement = document.querySelector(".fas.fa-envelope-open-text");
          if (ticketElement && ticketElement.nextElementSibling) {
            ticketElement.nextElementSibling.textContent = data.en_stock || 0;
          }
          if (envelopeElement && envelopeElement.nextElementSibling) {
            envelopeElement.nextElementSibling.textContent = data.criticos || 0;
          }
          actualizarGraficoDonutMedicamentos([data.en_stock || 0, data.criticos || 0]);
        }
      })
      .catch(error => console.error("Error fetching medicamentos_stock:", error));





    // Stock de Alimentos
    fetch('../../controllers/dashboard.controller.php?action=alimentos_stock')
      .then(response => validarRespuestaJSON(response))
      .then(data => {
        console.log("Datos de alimento:", data);
        if (data) {
          const totalAlimentosElement = document.querySelector(".text-purple");
          if (totalAlimentosElement) {
            totalAlimentosElement.textContent = data.stock_total || 0;
          }
          const dollarSignElement = document.querySelector(".fas.fa-dollar-sign");
          const chartLineElement = document.querySelector(".fas.fa-chart-line");
          if (dollarSignElement && dollarSignElement.nextElementSibling) {
            dollarSignElement.nextElementSibling.textContent = data.en_stock || 0;
          }
          if (chartLineElement && chartLineElement.nextElementSibling) {
            chartLineElement.nextElementSibling.textContent = data.baja_cantidad || 0;
          }
          actualizarGraficoBarrasAlimentos([data.en_stock || 0, data.baja_cantidad || 0]);
        }
      })
      .catch(error => console.error("Error fetching alimentos_stock:", error));

      
  }




  // Función de validación para asegurar respuesta JSON válida
  function validarRespuestaJSON(response) {
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }
    return response.json().catch(() => {
      throw new Error("Error parsing JSON response");
    });
  }




  // Función para actualizar el gráfico de línea de Servicios Realizados Mensualmente
  function actualizarGraficoLineal(dataSeries) {
    const salesCtx = document.getElementById('salesLineChart').getContext('2d');
    new Chart(salesCtx, {
      type: 'line',
      data: {
        labels: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
        datasets: [{
          data: dataSeries,
          borderColor: '#34c38f',
          backgroundColor: 'rgba(52, 195, 143, 0.1)',
          borderWidth: 2,
          tension: 0.4,
          fill: true,
        }]
      },
      options: {
        plugins: { legend: { display: false } },
        scales: { x: { display: false }, y: { display: false } },
        animation: animationOptions
      }
    });
  }




  // Función para actualizar el gráfico de barras de Servicios
  function actualizarGraficoBarrasServicios(dataSeries) {
    const overviewCtx = document.getElementById('overviewBarChart').getContext('2d');
    new Chart(overviewCtx, {
      type: 'bar',
      data: {
        labels: ['Propios', 'Mixtos'],
        datasets: [{
          data: dataSeries,
          backgroundColor: ['#4c84ff', '#8f94fb'],
          borderRadius: 5,
          barThickness: 20,
        }]
      },
      options: {
        plugins: { legend: { display: false } },
        scales: { x: { display: true }, y: { display: false } },
        animation: animationOptions
      }
    });
  }




  // Función para actualizar el gráfico de barras de Alimentos
  function actualizarGraficoBarrasAlimentos(dataSeries) {
    const earningsCtx = document.getElementById('earningsBarChart').getContext('2d');
    new Chart(earningsCtx, {
      type: 'bar',
      data: {
        labels: ['En Stock', 'Baja Cantidad'],
        datasets: [{
          data: dataSeries,
          backgroundColor: '#6f42c1',
          borderRadius: 10,
          barThickness: 12,
        }]
      },
      options: {
        plugins: { legend: { display: false } },
        scales: { x: { display: true }, y: { display: false } },
        animation: animationOptions
      }
    });
  }



  
  // Función para actualizar el gráfico de donut de Medicamentos
  function actualizarGraficoDonutMedicamentos(dataSeries) {
    const supportCtx = document.getElementById('supportDonutChart').getContext('2d');
    new Chart(supportCtx, {
      type: 'doughnut',
      data: {
        labels: ['En Stock', 'Críticos'],
        datasets: [{
          data: dataSeries,
          backgroundColor: ['#34c38f', '#e9ecef'],
          hoverBackgroundColor: ['#34c38f', '#e9ecef'],
          borderWidth: 0
        }]
      },
      options: {
        plugins: { legend: { display: false } },
        cutout: '80%',
        animation: animationOptions
      }
    });
  }
</script>



</body>
</html>