function mostrarMensajeDinamico(mensaje, tipo) {
    let color = tipo === 'ERROR' ? 'red' : tipo === 'INFO' ? 'blue' : 'green';
    console.log(`%c${mensaje}`, `color: ${color}; font-weight: bold;`);
    // Aquí puedes agregar más lógica para mostrar el mensaje en el UI
}

const cargarImplementos = async (idTipoinventario = 1) => {
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

cargarImplementos();
