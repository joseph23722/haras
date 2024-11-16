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

  <script>


    // Definir las opciones de animación globalmente
    const animationOptions = {
      duration: 1500,
      easing: 'easeOutBounce'
    };

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
              totalServiciosMesElement.textContent = data.totalServiciosRealizados || 0;
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

      // Realizar una solicitud GET para obtener el resumen de los servicios
      fetch('../../controllers/dashboard.controller.php?action=resumen_servicios')
        .then(response => response.json())
        .then(data => {
          // Si la respuesta contiene los datos esperados, actualizar los elementos en la página
          if (data) {
            // Total de servicios
            const totalServiciosElement = document.querySelector('.text-primary');
            if (totalServiciosElement) {
              totalServiciosElement.innerHTML = `Total: ${data.totalServicios} Servicios
          <span class="text-success" style="font-size: 0.8rem;">
            +${data.porcentajeCrecimiento || 0}%
          </span>`;
            }

            // Porcentaje de Servicios Propios
            const serviciosPropiosElement = document.querySelector('.d-flex .text-blue');
            if (serviciosPropiosElement) {
              serviciosPropiosElement.innerHTML = `Servicios Propios <strong>${data.porcentajeServiciosPropios || 0}%</strong>`;
            }

            // Porcentaje de Servicios Mixtos
            const serviciosMixtosElement = document.querySelector('.d-flex .text-purple');
            if (serviciosMixtosElement) {
              serviciosMixtosElement.innerHTML = `Servicios Mixtos <strong>${data.porcentajeServiciosMixtos || 0}%</strong>`;
            }

            // Llamar a la función para actualizar el gráfico
            actualizarGraficoBarra(data.totalServiciosPropios, data.totalServiciosMixtos);
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
          console.log("Datos de medicamento:", data);

          if (data) {
            // Obtener las cantidades de medicamentos en stock y críticos
            const medicamentosEnStock = data.en_stock_count || 0;
            const medicamentosCriticos = data.criticos_count || 0;

            // Mostrar la cantidad total de medicamentos
            const totalMedicamentosElement = document.querySelector("#totalMedicamentos");
            const totalMedicamentos = document.querySelector("#total_medicamentos");
            if (totalMedicamentosElement) {
              totalMedicamentosElement.textContent = data.stock_total || 0;
              totalMedicamentos.textContent = data.stock_total || 0;
            }

            // Mostrar la cantidad de medicamentos en stock
            const enStockElement = document.querySelector("#enStockMedicamentos");
            if (enStockElement) {
              enStockElement.textContent = medicamentosEnStock || '0';
            }

            // Mostrar la cantidad de medicamentos críticos
            const criticosElement = document.querySelector("#criticosMedicamentos");
            if (criticosElement) {
              criticosElement.textContent = medicamentosCriticos || '0';
            }

            // Separar los nombres de los medicamentos en stock y críticos
            const medicamentosEnStockList = data.en_stock ? data.en_stock.split(',') : [];
            const medicamentosCriticosList = data.criticos ? data.criticos.split(',') : [];

            // Actualizar el gráfico circular con las cantidades de stock y críticos
            actualizarGraficoCircularMedicamentos([medicamentosEnStock, medicamentosCriticos], medicamentosEnStockList, medicamentosCriticosList);
          }
        })
        .catch(error => console.error("Error fetching medicamentos_stock:", error));

      // Stock de Alimentos
      fetch('../../controllers/dashboard.controller.php?action=alimentos_stock')
        .then(response => validarRespuestaJSON(response))
        .then(data => {
          console.log("Datos de alimento:", data);

          if (data) {
            // Obtener las cantidades de alimentos en stock y baja cantidad
            const alimentosEnStock = data.en_stock_count || 0;
            const alimentosBajaCantidad = data.baja_cantidad_count || 0;

            // Mostrar la cantidad total de alimentos
            const totalAlimentosElement = document.querySelector("#totalAlimentos");
            const totalALimentos = document.querySelector("#total_alimentos");
            if (totalAlimentosElement) {
              totalAlimentosElement.textContent = data.stock_total || 0;
              totalALimentos.textContent = data.stock_total || 0;
            }

            // Mostrar la cantidad de alimentos en stock
            const enStockElement = document.querySelector("#enStock");
            if (enStockElement) {
              enStockElement.textContent = alimentosEnStock || '0';
            }

            // Mostrar la cantidad de alimentos con baja cantidad
            const bajaCantidadElement = document.querySelector("#bajaCantidad");
            if (bajaCantidadElement) {
              bajaCantidadElement.textContent = alimentosBajaCantidad || '0';
            }

            // Separar los nombres de los alimentos en stock y con baja cantidad
            const alimentosEnStockList = data.en_stock ? data.en_stock.split(',') : [];
            const alimentosBajaCantidadList = data.baja_cantidad ? data.baja_cantidad.split(',') : [];

            // Actualizar el gráfico de barras con las cantidades de stock y baja cantidad
            actualizarGraficoBarrasAlimentos([alimentosEnStock, alimentosBajaCantidad], alimentosEnStockList, alimentosBajaCantidadList);

            // Aquí puedes mostrar los nombres de los alimentos si los necesitas
            console.log("Alimentos en stock:", alimentosEnStockList);
            console.log("Alimentos con baja cantidad:", alimentosBajaCantidadList);
          }
        })
        .catch(error => console.error("Error fetching alimentos_stock:", error));



      // Mostrar imágenes en el dashboard, CARRUSEL
      fetch('../../controllers/dashboard.controller.php?action=fotografias_equinos')
        .then(response => response.json())
        .then(data => {
          const carouselInner = document.querySelector('#carouselEquinosSimple .carousel-inner');
          carouselInner.innerHTML = '';

          if (data.error) {
            console.error(data.error);
            carouselInner.innerHTML = `
            <div class="carousel-item active">
              <img src="https://via.placeholder.com/400x300?text=No+se+encontraron+imágenes" class="d-block w-100 equino-image" alt="No se encontraron imágenes">
            </div>
          `;
            return;
          }

          data.forEach((equino, index) => {
            const isActive = index === 0 ? 'active' : '';
            const item = `
            <div class="carousel-item ${isActive}">
              <img src="${equino.url}" class="d-block w-100 equino-image" alt="${equino.nombreEquino}">
              <div class="equino-name text-center mt-2">
                <strong>${equino.nombreEquino}</strong>
              </div>
            </div>
          `;
            carouselInner.insertAdjacentHTML('beforeend', item);
          });
        })
        .catch(error => {
          console.error('Error:', error);
          const carouselInner = document.querySelector('#carouselEquinosSimple .carousel-inner');
          carouselInner.innerHTML = `
          <div class="carousel-item active">
            <img src="https://via.placeholder.com/400x300?text=Error+cargando+imágenes" class="d-block w-100 equino-image" alt="Error cargando imágenes">
          </div>
        `;
        });
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
          plugins: {
            legend: {
              display: false
            }
          },
          scales: {
            x: {
              display: false
            },
            y: {
              display: false
            }
          },
          animation: animationOptions
        }
      });
    }

    // Función para actualizar el gráfico de barras de Servicios PROPIOS Y MIXTOS
    function actualizarGraficoBarra(serviciosPropios, serviciosMixtos) {
      const ctx = document.getElementById('overviewBarChart').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Servicios Propios', 'Servicios Mixtos'],
          datasets: [{
            label: 'Servicios',
            data: [serviciosPropios, serviciosMixtos],
            backgroundColor: ['#4CAF50', '#9C27B0'],
            borderColor: ['#388E3C', '#7B1FA2'],
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1
              }
            }
          },
          responsive: true,
          plugins: {
            legend: {
              display: false // Desactivar leyenda
            }
          }
        }
      });
    }
    // Función para actualizar el gráfico de barras de Alimentos
    function actualizarGraficoBarrasAlimentos([enStock, bajaCantidad], alimentosEnStockList, alimentosBajaCantidadList) {
      const ctx = document.getElementById("earningsBarChart").getContext("2d");

      // Aquí definimos los datos para el gráfico
      const data = {
        labels: ['En Stock', 'Baja Cantidad'], // Las etiquetas de las barras
        datasets: [{
          label: 'Cantidad',
          data: [enStock, bajaCantidad], // Los valores que se muestran en las barras
          backgroundColor: ['#4caf50', '#f44336'],
          borderColor: ['#388e3c', '#d32f2f'],
          borderWidth: 1,
          barThickness: 80 // Se puede ajustar el ancho de la barra
        }]
      };

      const config = {
        type: 'bar', // Gráfico de barras
        data: data,
        options: {
          responsive: true,
          plugins: {
            tooltip: {
              callbacks: {
                label: function(context) {
                  let label = context.dataset.label || '';
                  let value = context.raw;

                  // Aquí decidimos qué mostrar dependiendo de la barra en la que estamos
                  if (context.dataIndex === 0) {
                    label += ': ' + value + ' alimentos';
                    // Añadir salto de línea entre los alimentos
                    label += '\n' + alimentosEnStockList.join('\n');
                  } else if (context.dataIndex === 1) {
                    label += ': ' + value + ' alimentos';
                    label += '\n' + alimentosBajaCantidadList.join('\n');
                  }
                  return label;
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      };
      new Chart(ctx, config);
    }

    // Función para actualizar el gráfico circular de Medicamentos
    function actualizarGraficoCircularMedicamentos([enStock, criticos], medicamentosEnStockList, medicamentosCriticosList) {
      const ctx = document.getElementById("supportDonutChart").getContext("2d");

      const data = {
        labels: ['En Stock', 'Críticos'],
        datasets: [{
          data: [enStock, criticos],
          backgroundColor: ['#34c38f', '#e9ecef'],
          hoverBackgroundColor: ['#34c38f', '#e9ecef'],
          borderWidth: 5
        }]
      };

      const config = {
        type: 'doughnut',
        data: data,
        options: {
          responsive: true,
          plugins: {
            tooltip: {
              callbacks: {
                label: function(context) {
                  let label = context.dataset.label || '';
                  let value = context.raw;

                  if (context.dataIndex === 0) {
                    label += ': ' + value + ' medicamentos';
                    label += '\n' + medicamentosEnStockList.join('\n');
                  } else if (context.dataIndex === 1) {
                    label += ': ' + value + ' medicamentos';
                    label += '\n' + medicamentosCriticosList.join('\n');
                  }
                  return label;
                }
              }
            }
          },
          cutout: '80%', // Ajustar el tamaño del agujero central del gráfico donut
          animation: {
            animateRotate: true,
            animateScale: true
          }
        }
      };

      // Crear o actualizar el gráfico
      new Chart(ctx, config);
    }



  </script>
  <script src="http://localhost/haras/JS/notificaciones.js"></script>

</body>

</html>