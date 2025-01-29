document.addEventListener("DOMContentLoaded", function () {
    // Cerramos el modal con la X y no tocando cualquier parte de la pantalla
    $('#modalMovimiento').modal({
        backdrop: 'static',
        keyboard: false
    });

    // Mostrar alerta cuando se abre el modal de movimiento
    $('#modalMovimiento').on('shown.bs.modal', function () {
        cargarProductos();
    });

    // Función para cargar productos cuando se abre el modal
    function cargarProductos() {
        const idTipoinventario = 2;
        fetch(`../../controllers/implemento.controller.php?operation=implementosPorInventario&idTipoinventario=${idTipoinventario}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la solicitud de productos: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                const selectNombreProducto = document.getElementById('idInventario');
                if (selectNombreProducto) {
                    selectNombreProducto.innerHTML = '';
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Seleccione un producto';
                    defaultOption.disabled = true;
                    defaultOption.selected = true;
                    selectNombreProducto.appendChild(defaultOption);

                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.idInventario;
                            option.textContent = `${item.nombreProducto} - Stock: ${item.stockFinal}`;
                            selectNombreProducto.appendChild(option);
                        });
                    }
                }
            })
            .catch(error => {
                showToast('Hubo un problema al cargar los productos', 'ERROR');
            });
    }

    const cargarImplementos = async (idTipoinventario = 2) => {
        try {
            const params = new URLSearchParams({
                operation: 'implementosPorInventario',
                idTipoinventario: idTipoinventario
            });

            const response = await fetch(`../../controllers/implemento.controller.php?${params.toString()}`, {
                method: "GET"
            });

            const textResponse = await response.text();

            if (textResponse.startsWith("<")) {
                mostrarMensajeDinamico("Error en la respuesta del servidor.", 'ERROR');
                showToast("Error en la respuesta del servidor", 'ERROR');
                return;
            }

            const implementos = JSON.parse(textResponse);

            if (implementos && implementos.length > 0) {
                if ($.fn.dataTable.isDataTable('#implementos-table')) {
                    $('#implementos-table').DataTable().clear().rows.add(implementos).draw();
                } else {
                    $('#implementos-table').DataTable({
                        data: implementos,
                        columns: [
                            { data: 'idInventario', title: 'ID' },
                            { data: 'nombreProducto', title: 'Producto' },
                            { data: 'stockFinal', title: 'Stock Final' },
                            { data: 'cantidad', title: 'Cantidad' },
                            { data: 'precioUnitario', title: 'Precio Unitario' },
                            { data: 'precioTotal', title: 'Precio Total' },
                            {
                                data: 'estado',
                                title: 'Estado',
                                render: function (data, type, row) {
                                    // Verificar si el estado es 1 (Disponible) o 0 (No Disponible)
                                    if (data === 1) {
                                        return `<span style="color: green; font-weight: bold;">Disponible</span>`;
                                    } else if (data === 0) {
                                        return `<span style="color: red; font-weight: bold;">No Disponible</span>`;
                                    }
                                    return data; // En caso de que el valor no sea 1 o 0
                                }
                            }
                        ]
                    });
                }
            } else {
                mostrarMensajeDinamico("No hay datos para mostrar en esta tabla.", 'INFO');
            }
        } catch (error) {
            console.error("Error al cargar implementos:", error.message);
            mostrarMensajeDinamico("Error al cargar implementos: " + error.message, 'ERROR');
            showToast("Error al cargar implementos", 'ERROR');
        }
    };

    cargarImplementos();
});