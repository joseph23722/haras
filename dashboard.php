<?php require_once 'header.php'; ?>

<!-- Encabezado del Dashboard -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            </div>
        </div>
    </div>
</div>

<!-- Contenido del Dashboard -->
<section class="content flex-grow-1">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <!-- Equinos Registrados -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="small-box bg-info shadow-lg rounded position-relative card-hover">
                    <div class="inner">
                        <h3 style="color: white;">150</h3>
                        <p style="color: white; margin-top: 5px;">Equinos Registrados</p>
                    </div>
                    <div class="icon animated-icon">
                        <i class="fas fa-horse" style="font-size: 80px; opacity: 0.4;"></i>
                    </div>
                    <a href="#" class="small-box-footer"
                        style="background-color: rgba(0, 0, 0, 0.1); color: white; padding: 10px 0; text-align: center;">
                        Más información <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <!-- ./col -->

            <!-- Servicios Realizados -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="small-box bg-success shadow-lg rounded position-relative card-hover">
                    <div class="inner">
                        <h3 style="color: white;">53<sup style="font-size: 20px; color: white;">%</sup></h3>
                        <p style="color: white; margin-top: 5px;">Servicios Realizados</p>
                    </div>
                    <div class="icon animated-icon">
                        <i class="fas fa-handshake" style="font-size: 80px; opacity: 0.4;"></i>
                    </div>
                    <a href="#" class="small-box-footer"
                        style="background-color: rgba(0, 0, 0, 0.1); color: white; padding: 10px 0; text-align: center;">
                        Más información <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <!-- ./col -->

            <!-- Medicamentos en Stock -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="small-box bg-warning shadow-lg rounded position-relative card-hover">
                    <div class="inner">
                        <h3 id="total-stock-medicamentos" style="color: white;">0</h3> <!-- Mostrar el stock dinámicamente -->
                        <p style="color: white; margin-top: 5px;">Medicamentos en Stock</p>
                    </div>
                    <div class="icon animated-icon">
                        <i class="fas fa-pills" style="font-size: 80px; opacity: 0.4;"></i>
                    </div>
                    <a href="tablas/tablas.admedi.php" class="small-box-footer"
                        style="background-color: rgba(0, 0, 0, 0.1); color: white; padding: 10px 0; text-align: center;">
                        Más información <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- ./col -->
            <!-- Alimentos en Stock -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                <div class="small-box bg-danger shadow-lg rounded position-relative card-hover">
                    <div class="inner">
                        <h3 id="total-stock-alimentos" style="color: white;">0</h3> <!-- Mostrar el stock dinámicamente -->
                        <p style="color: white; margin-top: 5px;">Alimentos en Stock</p>
                    </div>
                    <div class="icon animated-icon">
                        <i class="fas fa-apple-alt" style="font-size: 80px; opacity: 0.4;"></i>
                    </div>
                    <a href="tablas/tablas.alimento.php" class="small-box-footer"
                        style="background-color: rgba(0, 0, 0, 0.1); color: white; padding: 10px 0; text-align: center;">
                        Más información <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <!-- ./col -->
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>

<!-- Enlaces a Librerías de AdminLTE y Plugins -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
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
    // Función para obtener el total de alimentos en stock
    const loadAlimentosStock = async () => {
        try {
            const response = await fetch('controllers/alimento.controller.php', {
                method: "POST",
                body: new URLSearchParams({ operation: 'getAlimentosStockInfo' }) // Llamada al nuevo método
            });

            if (!response.ok) {
                throw new Error("Error al obtener los alimentos en stock");
            }

            const result = await response.json();
            
            let totalStock = 0;
            result.forEach(alimento => {
                totalStock += parseFloat(alimento.stockFinal); // Sumar el stock final
            });

            document.querySelector("#total-stock-alimentos").textContent = totalStock; // Mostrar total en el dashboard
        } catch (error) {
            console.error('Error:', error);
        }
    };

    // Función para obtener el total de medicamentos en stock
    const loadMedicamentosStock = async () => {
        try {
            const response = await fetch('controllers/admedi.controller.php', {
                method: "POST",
                body: new URLSearchParams({ operation: 'getMedicamentosStockInfo' }) // Llamada al nuevo método para medicamentos
            });

            if (!response.ok) {
                throw new Error("Error al obtener los medicamentos en stock");
            }

            const result = await response.json();
            
            const totalStock = result.totalStock || 0;

            document.querySelector("#total-stock-medicamentos").textContent = totalStock; // Mostrar total en el dashboard
        } catch (error) {
            console.error('Error:', error);
        }
    };

    // Llamar a las funciones para cargar los alimentos y medicamentos en stock al cargar la página
    document.addEventListener("DOMContentLoaded", () => {
        loadAlimentosStock();
        loadMedicamentosStock(); // Llamar también a la función para los medicamentos
    });
</script>
