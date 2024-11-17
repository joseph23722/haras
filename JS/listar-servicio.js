$(document).ready(function () {
    const table = $('#serviciosTable').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
        },
        autoWidth: true,
        pagingType: "simple",
        dom: '<"top"lf>rt<"bottom"p><"clear">'
    });

    const costoServicioColumn = table.column(8);
    const horaEntradaColumn = table.column(5);
    const horaSalidaColumn = table.column(6);
    const equinoExternoColumn = table.column(9);

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

    costoServicioColumn.visible(false);
    horaEntradaColumn.visible(false);
    horaSalidaColumn.visible(false);
    equinoExternoColumn.visible(false);

    $('#btnFiltrar').click(function () {
        const tipoServicio = $('#filtroTipoServicio').val();
        if (tipoServicio) {
            $.ajax({
                url: '../../controllers/Propio.controller.php',
                method: 'GET',
                data: {
                    tipoServicio: tipoServicio
                },
                dataType: 'json',
                success: function (data) {
                    table.clear();
                    if (data.length > 0) {
                        data.forEach(function (item) {
                            // Determinar el nombre de Haras solo si el tipo de servicio es "Propio"
                            const nombreHaras = tipoServicio === 'Propio' ? 'Haras Rancho Sur' : item.nombreHaras;

                            // Agregar las filas de la tabla
                            table.row.add([
                                item.idServicio,
                                item.nombrePadrillo,
                                item.nombreYegua,
                                item.nombreEquinoExterno || '--',
                                item.fechaServicio,
                                item.detalles || '',
                                item.horaEntrada || '--',
                                item.horaSalida || '--',
                                nombreHaras || 'Haras Rancho Sur',
                                item.costoServicio || 'Por verificar',
                            ]);
                        });
                        table.draw();

                        if (tipoServicio === 'Mixto' || tipoServicio === 'General') {
                            table.column(3).visible(true);
                            table.column(5).visible(true);
                            table.column(6).visible(true);
                            table.column(7).visible(true);
                            table.column(8).visible(true);
                            table.column(9).visible(true);
                        } else if (tipoServicio === 'Propio') {
                            table.column(3).visible(false);
                            table.column(5).visible(true);
                            table.column(6).visible(false);
                            table.column(7).visible(false);
                            table.column(8).visible(false);
                            table.column(9).visible(false);
                        }
                    } else {
                        mostrarMensajeDinamico('No se encontraron servicios de monta', 'WARNING');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error al obtener los datos: ' + error);
                    mostrarMensajeDinamico('Error al obtener los datos', 'ERROR');
                }
            });
        } else {
            table.clear().draw();
            mostrarMensajeDinamico('Seleccione un tipo de servicio para filtrar', 'INFO');
        }
    });

});