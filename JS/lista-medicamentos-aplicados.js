document.addEventListener("DOMContentLoaded", async () => {
    const tableElement = document.querySelector("#medicamentosAplicadosTable tbody");

    try {
        // Realizar una solicitud fetch para obtener datos
        const response = await fetch("../../controllers/Propio.controller.php?action=obtenerHistorialDosisAplicadas");
        const data = await response.json();

        console.log(data); // Agrega esto para ver la respuesta completa del servidor

        if (data.status === "success") {
            // Iterar sobre los datos y crear filas dinámicas
            data.data.forEach(item => {
                console.log(item.EstadoMedicamento); // Verifica el valor de EstadoMedicamento

                const row = `
                    <tr>
                        <td>${item.NombreDelEquino}</td>
                        <td>${item.Medicamento}</td>
                        <td>${item.DosisAplicada}</td>
                        <td>${item.StockRestante}</td>
                        <td>${item.StockAntes}</td>
                        <td>${item.StockActual}</td>
                        <td>
                        ${item.EstadoMedicamento === 'Disponible'
                        ? '<span class="badge bg-success">Disponible</span>'
                        : '<span class="badge bg-danger">Agotado</span>'}                       
                        </td>
                        <td>${item.FechaAplicación}</td>
                        <td>${item.NombreUsuario}</td>
                    </tr>
                `;
                tableElement.insertAdjacentHTML("beforeend", row);
            });

            // Inicializar SimpleDataTable
            new simpleDatatables.DataTable("#medicamentosAplicadosTable", {
                searchable: true,
                fixedHeight: true,
                perPage: 10,
                labels: {
                    placeholder: "Buscar...",
                    noRows: "No se encontraron registros",
                    info: "Mostrando {start} a {end} de {rows} registros",
                },
            });
        } else {
            console.error("Error al obtener los datos: ", data.message);
            Swal.fire("Error", data.message, "error");
        }
    } catch (error) {
        console.error("Error en la solicitud: ", error);
        Swal.fire("Error", "No se pudo cargar el historial. Intente nuevamente.", "error");
    }
});
