<?php require_once '../header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard con Gráficos Avanzados</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    /* Estilos generales */
    body {
      background-color: #f4f5f7;
      font-family: 'Roboto', sans-serif;
    }

    .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
    }

    .bg-gradient {
      background: linear-gradient(135deg, #4c84ff, #8f94fb);
      color: white;
    }

    .chart-container {
      position: relative;
      height: 100px;
    }
  </style>
</head>
<body>

<div class="container py-4">
  <div class="row g-3">
    <!-- Gráfico de Ventas Diarias -->
    <div class="col-md-6 col-lg-4">
      <div class="card p-4">
        <div class="card-body">
          <h5 class="card-title">Average Daily Sales</h5>
          <h3 class="text-primary">$28,450</h3>
          <div class="chart-container">
            <canvas id="salesLineChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Gráfico de Ingresos -->
    <div class="col-md-6 col-lg-4">
      <div class="card p-4">
        <div class="card-body">
          <h5 class="card-title">Earnings Report</h5>
          <h3 class="text-success">$468</h3>
          <div class="chart-container">
            <canvas id="earningsBarChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Gráfico de Donut (Soporte Completado) -->
    <div class="col-md-6 col-lg-4">
      <div class="card p-4">
        <div class="card-body">
          <h5 class="card-title">Support Tracker</h5>
          <h3 class="text-info">164</h3>
          <div class="chart-container">
            <canvas id="supportDonutChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Script para Chart.js -->
<script>
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
      scales: {
        x: { display: false },
        y: { display: false }
      }
    }
  });

  // Gráfico de Barras para Ingresos
  const earningsCtx = document.getElementById('earningsBarChart').getContext('2d');
  const earningsBarChart = new Chart(earningsCtx, {
    type: 'bar',
    data: {
      labels: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
      datasets: [{
        data: [545.69, 256.34, 350.45, 470.78, 625.44, 714.29, 489.56],
        backgroundColor: '#6f42c1',
        borderRadius: 10,
        barThickness: 12,
      }]
    },
    options: {
      plugins: { legend: { display: false } },
      scales: {
        x: {
          display: true,
          grid: { display: false }
        },
        y: {
          display: false
        }
      }
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
      cutout: '80%', // Para hacer el corte más profundo en el centro
    }
  });
</script>

</body>
</html>


<?php require_once '../footer.php'; ?>
