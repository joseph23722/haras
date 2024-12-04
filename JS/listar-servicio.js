document.addEventListener("DOMContentLoaded", () => {
    let datosServicios = [];

    // Función para mostrar mensajes dinámicos
    const mostrarMensajeDinamico = (mensaje, tipo = 'INFO') => {
        const mensajeDiv = document.getElementById('mensaje');
        if (mensajeDiv) {
            const estilos = {
                'INFO': {
                    color: '#3178c6',
                    bgColor: '#e7f3ff',
                    icon: 'ℹ️'
                },
                'SUCCESS': {
                    color: '#3c763d',
                    bgColor: '#dff0d8',
                    icon: '✅'
                },
                'ERROR': {
                    color: '#a94442',
                    bgColor: '#f2dede',
                    icon: '❌'
                },
                'WARNING': {
                    color: '#8a6d3b',
                    bgColor: '#fcf8e3',
                    icon: '⚠️'
                }
            };
            const estilo = estilos[tipo] || estilos['INFO'];
            mensajeDiv.style.color = estilo.color;
            mensajeDiv.style.backgroundColor = estilo.bgColor;
            mensajeDiv.style.fontWeight = 'bold';
            mensajeDiv.style.padding = '15px';
            mensajeDiv.style.marginBottom = '15px';
            mensajeDiv.style.border = `1px solid ${estilo.color}`;
            mensajeDiv.style.borderRadius = '8px';
            mensajeDiv.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
            mensajeDiv.style.display = 'flex';
            mensajeDiv.style.alignItems = 'center';
            mensajeDiv.innerHTML = `<span style="margin-right: 10px; font-size: 1.2em;">${estilo.icon}</span>${mensaje}`;
            setTimeout(() => {
                mensajeDiv.innerHTML = '';
                mensajeDiv.style.border = 'none';
                mensajeDiv.style.boxShadow = 'none';
                mensajeDiv.style.backgroundColor = 'transparent';
            }, 5000);
        }
    };

    // Función para manejar el clic en el botón de filtro
    const btnFiltrar = document.getElementById('btnFiltrar');
    btnFiltrar.addEventListener('click', function () {
        const tipoServicio = document.getElementById('filtroTipoServicio').value;
    
        // Si se seleccionó un tipo de servicio
        if (tipoServicio) {
            fetch(`../../controllers/Propio.controller.php?action=listarServiciosPorTipo&tipoServicio=${tipoServicio}`)
                .then(response => response.json())
                .then(data => {    
                    // Verificar si el tbody existe antes de hacer cualquier manipulación
                    const tbody = document.querySelector("#serviciosTable tbody");
    
                    if (tbody) {
                        // Limpiar el contenido actual del tbody antes de agregar nuevas filas
                        tbody.innerHTML = "";
    
                        // Validar que data es un array antes de usar forEach
                        if (Array.isArray(data)) {
                            data.forEach(function (item) {
                                const nombreHaras = tipoServicio === 'Propio' ? 'Haras Rancho Sur' : item.nombreHaras;
    
                                const nuevaFila = `
                                    <tr>
                                        <td>${item.idServicio}</td>
                                        <td>${item.nombrePadrillo || '--'}</td>
                                        <td>${item.nombreYegua || '--'}</td>
                                        <td>${item.nombreEquinoExterno || '--'}</td>
                                        <td>${item.fechaServicio}</td>
                                        <td>${item.detalles || '--'}</td>
                                        <td>${item.horaEntrada || '--'}</td>
                                        <td>${item.horaSalida || '--'}</td>
                                        <td>${nombreHaras || 'Haras Rancho Sur'}</td>
                                        <td>${item.costoServicio || 'Por verificar'}</td>
                                    </tr>
                                `;
                                tbody.innerHTML += nuevaFila;
                            });
    
                            // Mostrar mensaje de éxito
                            mostrarMensajeDinamico(`Se han cargado ${data.length} servicios`, 'SUCCESS');
                        } else if (data.status === "error") {
                            // Si el backend envía un error en formato JSON
                            mostrarMensajeDinamico(data.message, 'ERROR');
                        } else {
                            // Si data no es un array, mostrar advertencia
                            mostrarMensajeDinamico('La respuesta no tiene datos válidos.', 'WARNING');
                        }

                        // Inicializar o actualizar DataTable
                        if (!window.simpleTable) {
                            window.simpleTable = new simpleDatatables.DataTable("#serviciosTable", {
                                autoWidth: true,
                                perPage: 10,
                                searchable: true,
                                sortable: true,
                            });
                        } else {
                            window.simpleTable.update();
                        }
    
                        // Restaurar el color del encabezado
                        const encabezado = document.querySelector("#serviciosTable thead");
                        if (encabezado) {
                            encabezado.style.backgroundColor = '#a0ffb8';
                            encabezado.style.color = 'white';
                        }
                    } else {
                        console.error("El tbody no se encontró en el DOM.");
                    }
                })
                .catch(error => {
                    mostrarMensajeDinamico('Error al obtener los datos', 'ERROR');
                });
        } else {
            // Si no se seleccionó tipo de servicio
            mostrarMensajeDinamico('Seleccione un tipo de servicio para filtrar', 'INFO');
        }
    });
    
});
