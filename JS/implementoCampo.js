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

    // Cargar tipos de movimiento
    fetch('../../controllers/implemento.controller.php?operation=tipoMovimiento', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error en la solicitud de tipos de movimiento: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (Array.isArray(data) && data.length > 0) {
                const selectTipoMovimiento = document.getElementById('idTipoMovimiento');
                if (selectTipoMovimiento) {
                    selectTipoMovimiento.innerHTML = '';
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Seleccione un tipo de movimiento';
                    defaultOption.disabled = true;
                    defaultOption.selected = true;
                    selectTipoMovimiento.appendChild(defaultOption);

                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.idTipomovimiento;
                        option.textContent = item.movimiento;
                        selectTipoMovimiento.appendChild(option);
                    });
                } else {
                    console.error('No se encontró el select con id "idTipoMovimiento"');
                    showToast('No se encontró el campo de tipo de movimiento.', 'ERROR');
                }
            } else {
                console.error('Error al cargar los tipos de movimiento:', data.message || 'Datos vacíos');
                showToast('Error al cargar los tipos de movimiento', 'ERROR');
            }
        })
        .catch(error => {
            console.error('Error de solicitud de tipos de movimiento:', error);
            showToast('Hubo un problema al cargar los tipos de movimiento', 'ERROR');
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
                    } else {
                        console.log('No se encontraron productos.');
                        showToast('No se encontraron productos para este inventario.', 'ERROR');
                    }
                } else {
                    console.error('No se encontró el select con id "productos"');
                    showToast('No se encontró el campo de productos.', 'ERROR');
                }
            })
            .catch(error => {
                console.error('Error de solicitud de productos:', error);
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
                    console.log("La tabla ya existe. Actualizando datos...");
                    $('#implementos-table').DataTable().clear().rows.add(implementos).draw();
                } else {
                    console.log("Inicializando DataTable...");
                    $('#implementos-table').DataTable({
                        data: implementos,
                        columns: [
                            { data: 'idInventario', title: 'ID' },
                            { data: 'nombreProducto', title: 'Producto' },
                            { data: 'stockFinal', title: 'Stock Final' },
                            { data: 'cantidad', title: 'Cantidad' },
                            { data: 'precioUnitario', title: 'Precio Unitario' },
                            { data: 'precioTotal', title: 'Precio Total' },
                            { data: 'estado', title: 'Estado' }
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

    // Función para calcular y mostrar el precio total
    function calcularPrecioTotal() {
        const precioUnitario = parseFloat(document.getElementById('precioUnitario').value);
        const cantidad = parseInt(document.getElementById('cantidad').value);

        if (!isNaN(precioUnitario) && !isNaN(cantidad) && cantidad > 0) {
            const precioTotal = precioUnitario * cantidad;
            document.getElementById('precioTotal').value = precioTotal.toFixed(2);
        } else {
            document.getElementById('precioTotal').value = '';
        }
    }

    document.getElementById('precioUnitario').addEventListener('input', calcularPrecioTotal);
    document.getElementById('cantidad').addEventListener('input', calcularPrecioTotal);

    // Manejo del formulario de registro de implementos
    const form = document.getElementById('form-registrar-implemento');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        const data = {
            operation: 'registrarImplemento',
            idTipoinventario: formData.get('idTipoinventario'),
            nombreProducto: formData.get('nombreProducto'),
            precioUnitario: formData.get('precioUnitario'),
            cantidad: formData.get('cantidad'),
            descripcion: formData.get('descripcion')
        };

        if (!data.idTipoinventario || !data.nombreProducto || !data.precioUnitario || !data.cantidad) {
            showToast('Por favor complete todos los campos.', 'WARNING');
            return;
        }

        ask('¿Está seguro de registrar el implemento?', 'Módulo de Implementos').then(respuesta => {
            if (respuesta) {
                fetch('../../controllers/implemento.controller.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 1) {
                            showToast('Implemento registrado exitosamente', 'SUCCESS');
                            form.reset();
                            cargarImplementos();
                            cargarHistorialMovimiento();
                        } else if (result.status === -1) {
                            if (result.message && result.message.includes('Ya existe un producto con el mismo nombre')) {
                                showToast('Error: Ya existe un producto con el mismo nombre en este tipo de inventario.', 'ERROR');
                            } else {
                                showToast(`Error al registrar el implemento: ${result.message}`, 'ERROR');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error de solicitud al registrar implemento:', error);
                        showToast('Hubo un problema al registrar el implemento', 'ERROR');
                    });
            } else {
                showToast('Acción cancelada', 'WARNING');
            }
        });
    });

    document.getElementById('formMovimiento').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        const idTipoinventario = formData.get('idTipoinventario');
        const idTipoMovimiento = formData.get('idTipoMovimiento');
        const idInventario = formData.get('idInventario');
        const cantidad = formData.get('cantidad');
        const descripcion = formData.get('descripcion');
        let precioUnitario = formData.get('precioUnitario');

        let operation = 'registrarEntrada';
        if (idTipoMovimiento === '2') {
            operation = 'registrarSalida';
            precioUnitario = null;
        }

        const data = {
            operation: operation,
            idTipoinventario: idTipoinventario,
            idTipoMovimiento: idTipoMovimiento,
            idInventario: idInventario,
            cantidad: cantidad,
            descripcion: descripcion,
            precioUnitario: precioUnitario  // Será `null` si es "Salida"
        };

        if (!idTipoMovimiento || !idInventario || !cantidad || !descripcion || !idTipoinventario) {
            showToast('Por favor complete todos los campos.', 'WARNING');
            return;
        }

        // Confirmar antes de registrar
        ask(`¿Está seguro de registrar la ${operation === 'registrarSalida' ? 'salida' : 'entrada'}?`, 'Módulo de Implementos').then(respuesta => {
            if (respuesta) {
                fetch('../../controllers/implemento.controller.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 1) {
                            showToast(`${operation === 'registrarSalida' ? 'Salida' : 'Entrada'} registrada exitosamente`, 'SUCCESS');
                            this.reset();
                            cargarProductos();
                            cargarImplementos();
                        } else if (result.status === -1) {
                            showToast(`Error al registrar la ${operation === 'registrarSalida' ? 'salida' : 'entrada'}: ${result.message}`, 'ERROR');
                        }
                    })
                    .catch(error => {
                        console.error(`Error de solicitud al registrar ${operation === 'registrarSalida' ? 'salida' : 'entrada'}:`, error);
                        showToast(`Hubo un problema al registrar la ${operation === 'registrarSalida' ? 'salida' : 'entrada'}`, 'ERROR');
                    });
            } else {
                showToast('Acción cancelada', 'WARNING');
            }
        });
    });

    // Función para cargar el historial de movimientos
    const cargarHistorialMovimiento = async (idTipoinventario = 2, idTipomovimiento = 1, tablaID) => {
        try {
            const params = new URLSearchParams({
                operation: 'listarHistorialMovimiento',
                idTipoinventario: idTipoinventario,
                idTipomovimiento: idTipomovimiento
            });

            const response = await fetch(`../../controllers/implemento.controller.php?${params.toString()}`, {
                method: "GET"
            });

            const historial = await response.json();
            console.log("Respuesta del servidor en json:", historial);

            if (historial.startsWith("<")) {
                console.error("Error en la respuesta del servidor.");
                return;
            }

            const implementos = JSON.parse(historial);
            console.log("Datos de implementos:", implementos);

            const tbody = document.getElementById(tablaID);
            tbody.innerHTML = "";

            if (implementos.length > 0) {
                implementos.forEach(implemento => {
                    const row = document.createElement("tr");

                    row.innerHTML = `
                    <td>${implemento.idHistorial}</td>
                    <td>${implemento.nombreProducto}</td>
                    <td>${implemento.precioUnitario || '-'}</td>
                    <td>${implemento.cantidad}</td>
                    <td>${implemento.descripcion || '-'}</td>
                    <td>${implemento.fechaMovimiento}</td>
                    <td>${implemento.nombreInventario}</td>
                `;

                    tbody.appendChild(row);
                });
            } else {
                const noDataRow = document.createElement("tr");
                noDataRow.innerHTML = `<td colspan="7" class="text-center">No hay datos disponibles</td>`;
                tbody.appendChild(noDataRow);
            }
        } catch (error) {
            console.error("Error al cargar el historial de movimientos:", error);
        }
    };

    // Event listeners para las pestañas del modal ENTRADA
    document.getElementById('entradas-tab').addEventListener('click', () => {
        cargarHistorialMovimiento(2, 1, 'historial-entradas-table');
    });

    document.getElementById('salidas-tab').addEventListener('click', () => {
        cargarHistorialMovimiento(2, 2, 'historial-salidas-table');
    });

    // Llamar a cargar las entradas al abrir el modal
    document.getElementById('modalHistorial').addEventListener('show.bs.modal', () => {
        cargarHistorialMovimiento(2, 1, 'historial-entradas-table');
    });

    cargarImplementos();
});