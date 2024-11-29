document.addEventListener("DOMContentLoaded", function () {
    $('#tabla-entradas').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
    });

    $('#tabla-salidas').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
    });

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

            // Verifica si la respuesta es un array de implementos
            if (Array.isArray(historial)) {
                const tbody = document.getElementById(tablaID);
                tbody.innerHTML = "";

                if (historial.length > 0) {
                    historial.forEach(implemento => {
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
            } else {
                console.error("La respuesta no es un array válido:", historial);
            }
        } catch (error) {
            console.error("Error al cargar el historial de movimientos:", error);
        }
    };

    // Cargar los datos en las tablas cuando se cambian las pestañas
    document.getElementById('entradas-tab').addEventListener('click', () => {
        cargarHistorialMovimiento(2, 1, 'historial-entradas-table');
    });

    document.getElementById('salidas-tab').addEventListener('click', () => {
        cargarHistorialMovimiento(2, 2, 'historial-salidas-table');
    });

    // Llamar a cargar las entradas por defecto
    cargarHistorialMovimiento(2, 1, 'historial-entradas-table');
});