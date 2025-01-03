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
        .then(response => response.json())  // Asegúrate de que la respuesta sea JSON
        .then(data => {

            if (data) {
                // Actualiza el total de servicios realizados
                const totalServiciosMesElement = document.querySelector(".text-accent");
                if (totalServiciosMesElement) {
                    totalServiciosMesElement.textContent = data.totalServiciosRealizados || 0;
                }

                // Actualiza el porcentaje de progreso
                const progressBar = document.querySelector(".progress-bar");
                if (progressBar) {
                    progressBar.style.width = `${data.porcentajeProgreso || 0}%`;
                    progressBar.textContent = `${data.porcentajeProgreso || 0}%`;
                    progressBar.setAttribute('aria-valuenow', data.porcentajeProgreso || 0);
                }

                // Si no hay datos para seriesMensual, generamos un gráfico con el total de servicios realizados
                let dataSeries = [data.totalServiciosRealizados, 0, 0, 0, 0, 0, 0];  // Usamos el total de servicios realizados, y el resto con 0

                // Actualizamos el gráfico
                actualizarGraficoLineal(dataSeries);
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
    // Verifica si los datos de la serie están vacíos
    if (dataSeries.length === 0) {
        return;  // Si no hay datos, no intentamos renderizar el gráfico
    }

    const salesCtx = document.getElementById('salesLineChart').getContext('2d');

    // Etiquetas para semanas del mes
    const semanasDelMes = ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4', 'Semana 5'];

    new Chart(salesCtx, {
        type: 'line',  // Tipo de gráfico: línea
        data: {
            labels: semanasDelMes,  // Semanas del mes
            datasets: [{
                data: dataSeries,  // Los datos reales pasados desde PHP
                borderColor: '#34c38f',  // Color de la línea
                backgroundColor: 'rgba(52, 195, 143, 0.1)',  // Color de fondo debajo de la línea
                borderWidth: 2,  // Ancho de la línea
                tension: 0.4,  // Curvatura de la línea
                fill: true,  // Llenado debajo de la línea
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false  // Ocultar la leyenda
                }
            },
            scales: {
                x: {
                    display: true,  // Mostrar el eje X
                    ticks: {
                        maxRotation: 90,  // Rota las etiquetas del eje X si es necesario
                        minRotation: 45,
                    }
                },
                y: {
                    display: true,  // Mostrar el eje Y
                    ticks: {
                        beginAtZero: true  // Comenzar desde cero en el eje Y
                    }
                }
            },
            animation: {
                duration: 1000,  // Duración de la animación
                easing: 'easeOutQuart'  // Efecto de animación de la línea
            }
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
                    display: false
                }
            },
            barPercentage: 0.5, // Establecer el porcentaje de la barra
            borderRadius: 8, // Bordes redondeados

            // Configuración de la animación y transición
            animation: {
                duration: 1500,  // Duración de la animación en milisegundos
                easing: 'easeOutBounce', // Tipo de animación (rebote al final)
                onComplete: function () {
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
                        label: function (context) {
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
            },
            animation: {
                duration: 1500,  // Duración de la animación en milisegundos (1.5 segundos)
                easing: 'easeOutBounce', // Efecto de rebote en la animación
                onComplete: function () {                }
            },
            barPercentage: 0.5, // Porcentaje de la barra
            borderRadius: 8, // Bordes redondeados de las barras
        }
    };

    new Chart(ctx, config);  // Crear y renderizar el gráfico
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
        type: 'doughnut',  // Tipo de gráfico: donut (circular)
        data: data,
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function (context) {
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
                animateRotate: true, // Habilitar animación de rotación
                animateScale: true,  // Habilitar animación de escala (expandir/contraer)
                duration: 1500,  // Duración de la animación (1.5 segundos)
                easing: 'easeOutBounce',  // Efecto de rebote al final de la animación
                onComplete: function () {
                }
            }
        }
    };

    // Crear o actualizar el gráfico
    new Chart(ctx, config);
}

// Obtener el botón y el sidebar
const sidebarToggle = document.getElementById("sidebarToggle");
const sidenav = document.getElementById("layoutSidenav_nav");

// Función para alternar la visibilidad del sidebar
sidebarToggle.addEventListener("click", function (event) {
    // Prevenir la acción predeterminada del enlace, ya que no es necesario
    event.preventDefault();

    // Alternar una clase 'sb-sidenav-toggled' en el sidebar
    sidenav.classList.toggle("sb-sidenav-toggled");
});