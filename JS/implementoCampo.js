document.addEventListener("DOMContentLoaded", function () {
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
                const selectNombreProducto = document.getElementById('productos');
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
                            option.textContent = `${item.nombreProducto} - Cantidad: ${item.cantidad}`;
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

        if (!data.idTipoinventario || !data.nombreProducto || !data.precioUnitario || !data.cantidad || !data.descripcion) {
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
});