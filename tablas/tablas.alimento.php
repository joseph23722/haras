<?php require_once '../header.php'; ?> <!-- Incluir el encabezado -->

<div class="container mt-5">
    <h2 class="text-center" style="font-weight: bold; color: #0056b3;">Alimentos en Stock</h2>

    <!-- Añadir una sombra y bordes redondeados para un diseño moderno -->
    <div class="table-responsive shadow-lg p-3 mb-5 bg-white rounded" style="border-radius: 15px;">
        <table class="table table-striped table-hover table-bordered">
            <thead style="background-color: #c9f0ff; color: #003366;">
                <tr>
                    <th>Nombre del Alimento</th>
                    <th>Cantidad</th>
                    <th>Stock Final</th>
                    <th>Costo</th>
                </tr>
            </thead>
            <tbody id="alimentos-table">
                <!-- Los datos de alimentos se cargarán aquí -->
            </tbody>
        </table>
    </div>
</div>

<!-- Librerías necesarias -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Función para cargar los datos de alimentos
    const loadAlimentosData = async () => {
        try {
            const response = await fetch('../controllers/alimento.controller.php', {
                method: 'POST',
                body: new URLSearchParams({
                    operation: 'getAllAlimentos'
                }) // Llamada al método para obtener los alimentos
            });

            if (!response.ok) {
                throw new Error("Error al obtener los datos de alimentos.");
            }

            const alimentos = await response.json();

            // Cargar los datos en la tabla
            const tableBody = document.querySelector('#alimentos-table');
            alimentos.forEach(alimento => {
                const row = `
                    <tr>
                        <td>${alimento.nombreAlimento}</td>
                        <td>${alimento.cantidad}</td>
                        <td>${alimento.stockFinal}</td>
                        <td>${alimento.costo}</td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
        } catch (error) {
            console.error('Error:', error);
        }
    };

    // Cargar los datos cuando la página esté lista
    document.addEventListener('DOMContentLoaded', loadAlimentosData);
</script>

<?php require_once '../footer.php'; ?> <!-- Incluir el pie de página -->