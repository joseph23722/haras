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
  <style>
    body {
      background-color: #f4f5f7;
      font-family: 'Roboto', sans-serif;
    }

    .text-accent { color: #34c38f; }
    .text-purple { color: #6f42c1; }
    .text-blue { color: #4c84ff; }
    .text-pink { color: #d63384; }
    .progress {
      background-color: #e9ecef;
      border-radius: 5px;
    }
    .progress-bar {
      border-radius: 5px;
    }
    .container {
      max-width: 1800px;
    }

    /* Estilos personalizados para reducir el tamaño de la tarjeta Support Tracker */
    .support-tracker-card {
        padding: 15px;
        min-height: 470px; /* Ajusta la altura de la tarjeta */
    }
    .small-title {
        font-size: 2rem; /* Reduce el tamaño de la fuente del título */
        margin-bottom: 15px;
    }
    .small-number {
        font-size: 2 rem; /* Reduce el tamaño de la fuente del número principal */
    }
    .chart-container.small-chart {
        height: 200px; /* Reduce la altura del gráfico */
        width: 200px; /* Reduce el ancho del gráfico */
        margin: 0 auto; /* Centra el gráfico */
    }
    #supportDonutChart {
        max-width: 100%; /* Limita el ancho máximo del canvas */
        max-height: 100%; /* Limita la altura máxima del canvas */
    }
    .small-info {
        font-size: 1.1rem; /* Reduce el tamaño de fuente de la información inferior */
    }

    /* Estilos personalizados para la tarjeta de Nosotros y carrusel de Equinos */
    .fancy-title {
    font-size: 2rem; /* Ajusta este valor para cambiar el tamaño del título */
    font-family: 'Georgia', serif;
    margin-bottom: 5px;
    text-align: center; /* Mantiene el título centrado */
    color: #808080; /* Nuevo color para el texto */
    }

    .equinos-carousel {
    max-height: 770px; /* Ajusta la altura máxima del carrusel */
    }

    .equino-image {
    height: 290px; /* Ajusta este valor para cambiar el tamaño de las imágenes */
    object-fit: cover;
    border-radius: 12px;
    }

    /* propios y mixto**/
    .card {
    border: none;
    border-radius: 10px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    padding: 25px;
    min-height: 400px; /* Aumenta la altura de la tarjeta ajustando este valor */
    }

    /** tarjetas **/
    .custom-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    padding: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background-color: #ffffff; /* Fondo blanco para la tarjeta */
    }

    .custom-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .custom-card-title {
    font-size: 1.2rem; /* Tamaño del título */
    color: #333; /* Color del texto del título */
    }

    .custom-text-muted {
    color: #6c757d; /* Color del texto descriptivo */
    font-size: 0.9rem; /* Tamaño del texto */
    }

    .custom-card h3 {
    font-size: 2rem; /* Tamaño del número */
    margin-bottom: 5px;
    }

    .custom-card .text-info {
    color: #17a2b8; /* Color personalizado para números */
    }

    .custom-card .text-purple {
    color: #6f42c1;
    }

    .custom-card .text-success {
    color: #28a745;
    }

    .custom-card .text-danger {
    color: #dc3545;
    }

    /**alimentos */
    .food-stock-card {
    border: none;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    }

    .food-stock-title {
    font-size: 1.2rem;
    color: #333;
    }

    .food-stock-count {
    font-size: 2rem;
    margin-bottom: 5px;
    }

    .food-stock-chart-container {
    height: 150px; /* Ajuste para el tamaño del gráfico */
    margin-top: 15px;
    }

    /**medicamentos */
    .medication-stock-card {
    border: none;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    }

    .medication-stock-title {
    font-size: 1.2rem;
    color: #333;
    }

    .medication-stock-count {
    font-size: 2rem;
    margin-bottom: 5px;
    }

    .medication-stock-chart-container {
    height: 150px; /* Ajuste para el tamaño del gráfico */
    margin-top: 15px;
    }


    









  </style>
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
        <h3 class="text-accent">58</h3>
        <p class="small text-muted">Total de Servicios Realizados este Mes</p>
        <div class="progress my-2">
          <div class="progress-bar bg-success" role="progressbar" style="width: 65%;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">65%</div>
        </div>
        <div class="chart-container mt-3">
          <canvas id="salesLineChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Servicios  con iconos para Servicios Propios y Mixtos -->
    <div class="col-md-6 col-lg-4">
    <div class="card">
        <h5 class="card-title">Servicios</h5>
        <h3 class="text-primary">Total: 120 Servicios <span class="text-success" style="font-size: 0.8rem;">+10.5%</span></h3> <!-- Número total de servicios y crecimiento -->
        <div class="d-flex justify-content-between text-muted mt-2">
           <span><i class="fas fa-tools text-blue"></i> Servicios Propios <strong>70%</strong></span> <!-- Servicios Propios -->
          <span><i class="fas fa-handshake text-purple"></i> Servicios Mixtos <strong>30%</strong></span> <!-- Servicios Mixtos -->
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
        <h3 class="text-info">124</h3>
        <p class="small custom-text-muted">Total de equinos actualmente registrados</p>
    </div>
    </div>

    <!-- Nueva tarjeta para Servicios Realizados -->
    <div class="col-md-6 col-lg-3">
    <div class="custom-card text-center">
        <h5 class="custom-card-title">Servicios Realizados</h5>
        <h3 class="text-purple">58</h3>
        <p class="small custom-text-muted">Total de servicios realizados esta semana</p>
    </div>
    </div>

    <!-- Nueva tarjeta para Medicamentos en Stock -->
    <div class="col-md-6 col-lg-3">
    <div class="custom-card text-center">
        <h5 class="custom-card-title">Medicamentos en Stock</h5>
        <h3 class="text-success">320</h3>
        <p class="small custom-text-muted">Total de medicamentos disponibles</p>
    </div>
    </div>

    <!-- Nueva tarjeta para Alimentos en Stock -->
    <div class="col-md-6 col-lg-3">
    <div class="custom-card text-center">
        <h5 class="custom-card-title">Alimentos en Stock</h5>
        <h3 class="text-danger">105</h3>
        <p class="small custom-text-muted">Cantidad total de alimentos disponibles</p>
    </div>
    </div>


    <!-- barra de progreso  alimentos -->
    <div class="col-md-6 col-lg-6">
      <div class="card">
        <h5 class="card-title">Stock de Alimentos</h5>
        <h3 class="text-purple">105 <span class="text-success" style="font-size: 0.8rem;">+4.2%</span></h3>
        <p class="small text-muted">Cantidad total de alimentos disponibles</p>
        <div class="chart-container">
          <canvas id="earningsBarChart"></canvas>
        </div>
        <div class="d-flex justify-content-between mt-3 text-muted">
          <span><i class="fas fa-dollar-sign text-blue"></i> En Stock <strong>859</strong></span>
          <span><i class="fas fa-chart-line text-pink"></i> Baja Cantidad <strong>20</strong></span>
        </div>
      </div>
    </div>

    <!-- gráfico y barra de medicamento -->
    <div class="col-md-6 col-lg-6">
        <div class="card support-tracker-card">
            <h5 class="card-title small-title">Stock de Medicamentos</h5>
            <h3 class="text-info small-number">320</h3>
            <p class="small text-muted">Cantidad total de medicamentos disponibles</p>
            <div class="chart-container small-chart">
                 <canvas id="supportDonutChart"></canvas>
            </div>
            <div class="d-flex justify-content-between mt-2 text-muted">
                <span class="small-info"><i class="fas fa-ticket-alt text-blue"></i> En Stock <strong>290</strong></span>
                <span class="small-info"><i class="fas fa-envelope-open-text text-pink"></i> Críticos <strong>30</strong></span>
            </div>
        </div>
        </div>
    </div>

<!-- JavaScript para Chart.js y Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const animationOptions = { duration: 1500, easing: 'easeOutBounce' };

  // Gráfico de Línea para Ventas Diarias
  const salesCtx = document.getElementById('salesLineChart').getContext('2d');
  const salesLineChart = new Chart(salesCtx, {
    type: 'line',
    data: {
      labels: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
      datasets: [{
        data: [12000, 15000, 13000, 22000, 18000, 17000, 21000],
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

  // Gráfico de Barras para Resumen de Ventas
  const overviewCtx = document.getElementById('overviewBarChart').getContext('2d');
  const overviewBarChart = new Chart(overviewCtx, {
    type: 'bar',
    data: {
      labels: ['Orders', 'Visits'],
      datasets: [{
        data: [62.2, 25.5],
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

  // Gráfico de Barras para Ingresos
  const earningsCtx = document.getElementById('earningsBarChart').getContext('2d');
  const earningsBarChart = new Chart(earningsCtx, {
    type: 'bar',
    data: {
      labels: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
      datasets: [{
        data: [545.69, 256.34, 300.45, 474.22, 625.39, 714.58, 489.72],
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

  // Gráfico de Donut para Soporte Completado
  const supportCtx = document.getElementById('supportDonutChart').getContext('2d');
  const supportDonutChart = new Chart(supportCtx, {
    type: 'doughnut',
    data: {
      labels: ['Completed', 'Pending'],
      datasets: [{
        data: [85, 15],
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
</script>

</body>
</html>

