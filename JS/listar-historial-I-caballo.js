document.addEventListener("DOMContentLoaded", () => {
    // Función para cargar el historial de movimientos
    const cargarHistorialMovimiento = async (idTipoinventario = 1, idTipomovimiento = 1, tablaID) => {
        try {
            const params = new URLSearchParams({
                operation: 'listarHistorialMovimiento',
                idTipoinventario: idTipoinventario,
                idTipomovimiento: idTipomovimiento
            });

            const response = await fetch(`../../controllers/implemento.controller.php?${params.toString()}`, {
                method: "GET"
            });

            const textResponse = await response.text();

            if (textResponse.startsWith("<")) {
                console.error("Error en la respuesta del servidor.");
                return;
            }

            const implementos = JSON.parse(textResponse);

            const tbody = document.getElementById(tablaID);
            tbody.innerHTML = ""; // Limpiar contenido previo

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

    // Event listeners para las pestañas de Entradas y Salidas
    const entradasTab = document.getElementById('entradas-tab');
    const salidasTab = document.getElementById('salidas-tab');

    if (entradasTab) {
        entradasTab.addEventListener('click', () => {
            cargarHistorialMovimiento(1, 1, 'historial-entradas-table');
        });
    }

    if (salidasTab) {
        salidasTab.addEventListener('click', () => {
            cargarHistorialMovimiento(1, 2, 'historial-salidas-table');
        });
    }

    // Inicializar las tablas con DataTables
    $(document).ready(function () {
        $('#tabla-entradas').DataTable({
            responsive: true,
        });

        $('#tabla-salidas').DataTable({
            responsive: true,
        });
    });

    // Llamar a cargar las entradas al inicio para mostrar datos predeterminados
    cargarHistorialMovimiento(1, 1, 'historial-entradas-table');
});
