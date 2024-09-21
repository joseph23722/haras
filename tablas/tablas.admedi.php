<?php require_once '../header.php'; ?> <!-- Incluir el encabezado -->

<div class="container mt-5">
    <h2 class="text-center" style="font-weight: bold; color: #0056b3;">Medicamentos en Stock</h2>

    <!-- Añadir una sombra y bordes redondeados para un diseño moderno -->
    <div class="table-responsive shadow-lg p-3 mb-5 bg-white rounded" style="border-radius: 15px;">
        <table class="table table-striped table-hover table-bordered">
            <thead style="background-color: #c9f0ff; color: #003366;">
                <tr>
                    <th>Nombre del Medicamento</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Caducidad</th>
                </tr>
            </thead>
            <tbody id="medicamentos-table">
                <!-- Los datos de medicamentos se cargarán aquí -->
            </tbody>
        </table>
    </div>
</div>

<!-- Librerías necesarias -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Función para cargar los datos de medicamentos
    const loadMedicamentosData = async () => {
        try {
            const response = await fetch('../controllers/admedi.controller.php', {
                method: 'POST',
                body: new URLSearchParams({ operation: 'getAllMedicamentos' }) // Llamada al método para obtener los medicamentos
            });

            if (!response.ok) {
                throw new Error("Error al obtener los datos de medicamentos.");
            }

            const medicamentos = await response.json();

            // Cargar los datos en la tabla
            const tableBody = document.querySelector('#medicamentos-table');
            medicamentos.forEach(medicamento => {
                const row = `
                    <tr>
                        <td>${medicamento.nombreMedicamento}</td>
                        <td>${medicamento.cantidad}</td>
                        <td>${medicamento.precioUnitario}</td>
                        <td>${medicamento.caducidad}</td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('beforeend', row);
            });
        } catch (error) {
            console.error('Error:', error);
        }
    };

    // Cargar los datos cuando la página esté lista
    document.addEventListener('DOMContentLoaded', loadMedicamentosData);
</script>

<?php require_once '../footer.php'; ?> <!-- Incluir el pie de página -->
