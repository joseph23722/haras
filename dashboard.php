<?php require_once 'header.php'; ?>

<div class="content-wrapper">
    <!-- Encabezado del Dashboard -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard v1</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido del Dashboard -->
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <!-- Equinos Registrados -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info shadow-lg rounded position-relative card-hover" style="padding: 15px; overflow: hidden;">
                        <div class="inner">
                            <h3 style="color: white;">150</h3>
                            <p style="color: white; margin-top: 5px;">Equinos Registrados</p>
                        </div>
                        <div class="icon animated-icon">
                            <i class="fas fa-horse" style="font-size: 80px; opacity: 0.4;"></i>
                        </div>
                        <a href="#" class="small-box-footer" style="background-color: rgba(0, 0, 0, 0.1); color: white; padding: 10px 0; text-align: center;">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <!-- ./col -->

                <!-- Servicios Realizados -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success shadow-lg rounded position-relative card-hover" style="padding: 15px; overflow: hidden;">
                        <div class="inner">
                            <h3 style="color: white;">53<sup style="font-size: 20px; color: white;">%</sup></h3>
                            <p style="color: white; margin-top: 5px;">Servicios Realizados</p>
                        </div>
                        <div class="icon animated-icon">
                            <i class="fas fa-handshake" style="font-size: 80px; opacity: 0.4;"></i>
                        </div>
                        <a href="#" class="small-box-footer" style="background-color: rgba(0, 0, 0, 0.1); color: white; padding: 10px 0; text-align: center;">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <!-- ./col -->

                <!-- Medicamentos Disponibles -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning shadow-lg rounded position-relative card-hover" style="padding: 15px; overflow: hidden;">
                        <div class="inner">
                            <h3 style="color: white;">120</h3>
                            <p style="color: white; margin-top: 5px;">Medicamentos Disponibles</p>
                        </div>
                        <div class="icon animated-icon">
                            <i class="fas fa-pills" style="font-size: 80px; opacity: 0.4;"></i>
                        </div>
                        <a href="#" class="small-box-footer" style="background-color: rgba(0, 0, 0, 0.1); color: white; padding: 10px 0; text-align: center;">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <!-- ./col -->

                <!-- Alimentos en Stock -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger shadow-lg rounded position-relative card-hover" style="padding: 15px; overflow: hidden;">
                        <div class="inner">
                            <h3 style="color: white;">65</h3>
                            <p style="color: white; margin-top: 5px;">Alimentos en Stock</p>
                        </div>
                        <div class="icon animated-icon">
                            <i class="fas fa-apple-alt" style="font-size: 80px; opacity: 0.4;"></i>
                        </div>
                        <a href="#" class="small-box-footer" style="background-color: rgba(0, 0, 0, 0.1); color: white; padding: 10px 0; text-align: center;">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <!-- ./col -->
            </div>

            <!-- Row for charts and map -->
            <div class="row">
                <!-- Sales Chart -->
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">Sales</h3>
                            <div class="card-tools">
                                <a href="#" class="btn btn-tool btn-sm">
                                    <i class="fas fa-download"></i>
                                </a>
                                <a href="#" class="btn btn-tool btn-sm">
                                    <i class="fas fa-bars"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex">
                                <p class="d-flex flex-column">
                                    <span class="text-bold text-lg">820</span>
                                    <span>Sales Over Time</span>
                                </p>
                                <p class="ml-auto d-flex flex-column text-right">
                                    <span class="text-success">
                                        <i class="fas fa-arrow-up"></i> 33.1%
                                    </span>
                                    <span class="text-muted">Since last month</span>
                                </p>
                            </div>
                            <!-- Chart -->
                            <div class="position-relative mb-4">
                                <canvas id="sales-chart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visitors Map -->
                <div class="col-lg-6">
                    <div class="card bg-gradient-primary">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-map-marker-alt"></i>
                                Visitors
                            </h3>
                            <!-- tools card -->
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm daterange" title="Date range">
                                    <i class="far fa-calendar-alt"></i>
                                </button>
                                <button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse" title="Collapse">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                            <!-- /. tools -->
                        </div>
                        <div class="card-body">
                            <div id="world-map" style="height: 250px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once 'footer.php'; ?>

<!-- Enlaces a Librerías de AdminLTE y Plugins -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Estilos y Animaciones adicionales -->
<style>
    .card-hover:hover {
        transform: translateY(-5px);
        transition: transform 0.3s ease;
    }

    .animated-icon {
        position: absolute;
        top: 10px;
        right: 15px;
        transition: transform 0.3s ease;
    }

    .small-box:hover .animated-icon {
        transform: translateY(-10px);
    }

    .small-box:hover {
        box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2);
    }
</style>

<script>
    // Chart para estadísticas de ventas
    var salesChartCanvas = document.getElementById('sales-chart').getContext('2d');

    var salesChartData = {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
            label: 'Sales',
            backgroundColor: 'rgba(60,141,188,0.9)',
            borderColor: 'rgba(60,141,188,0.8)',
            data: [30, 50, 60, 70, 50, 80, 90]
        }]
    };

    var salesChartOptions = {
        maintainAspectRatio: false,
        responsive: true,
        scales: {
            x: {
                grid: {
                    display: false,
                }
            },
            y: {
                grid: {
                    borderDash: [5, 5],
                },
                beginAtZero: true
            }
        }
    };

    // Create the sales chart
    new Chart(salesChartCanvas, {
        type: 'line',
        data: salesChartData,
        options: salesChartOptions
    });
</script>
