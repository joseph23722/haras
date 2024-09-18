<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #ff6347;">Gestionar Medicamentos</h1>

    <div class="card mb-4 shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0 text-uppercase">Datos del Medicamento</h5>
        </div>

        <!-- Formulario -->
        <div class="card-body p-4 bg-light rounded">
            <form action="" id="form-medicamento" autocomplete="off">
                <div class="row g-3">
                    
                    <!-- Nombre del Medicamento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nombreMedicamento" id="nombreMedicamento" class="form-control">
                            <label for="nombreMedicamento"><i class="fas fa-capsules" style="color: #ff7f50;"></i> Nombre del Medicamento</label>
                        </div>
                    </div>

                    <!-- Cantidad -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="cantidad" id="cantidad" class="form-control">
                            <label for="cantidad"><i class="fas fa-balance-scale" style="color: #32cd32;"></i> Cantidad</label>
                        </div>
                    </div>

                    <!-- Fecha de Caducidad (Solo para Registrar) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="caducidad" id="caducidad" class="form-control">
                            <label for="caducidad"><i class="fas fa-calendar-alt" style="color: #ba55d3;"></i> Fecha de Caducidad</label>
                        </div>
                    </div>

                    <!-- Precio Unitario (Solo para Registrar) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" step="0.01" name="precioUnitario" id="precioUnitario" class="form-control">
                            <label for="precioUnitario"><i class="fas fa-dollar-sign" style="color: #1e90ff;"></i> Precio Unitario</label>
                        </div>
                    </div>

                    <!-- Tipo de Movimiento con botones -->
                    <div class="col-md-12">
                        <label for="tipoMovimiento" class="form-label">Tipo de Movimiento</label>
                        <div class="btn-group d-flex justify-content-center">
                            <button type="button" class="btn btn-outline-success btn-lg" id="entrada-btn"><i class="fas fa-arrow-up"></i> Entrada</button>
                            <button type="button" class="btn btn-outline-danger btn-lg ms-2" id="salida-btn"><i class="fas fa-arrow-down"></i> Salida</button>
                        </div>
                        <input type="hidden" name="idTipomovimiento" id="idTipomovimiento">
                    </div>

                    <!-- Botones -->
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-lg" id="registrar-medicamento"><i class="fas fa-save"></i> Registrar Medicamento</button>
                        <button type="reset" class="btn btn-secondary btn-lg shadow-sm"><i class="fas fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Medicamentos Registrados -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5><i class="fas fa-database"></i> Medicamentos Registrados</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped table-hover table-bordered">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="medicamentos-table">
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const formMedicamento = document.querySelector("#form-medicamento");
    const medicamentosTable = document.querySelector("#medicamentos-table");
    const idTipomovimiento = document.querySelector("#idTipomovimiento");
    const caducidadField = document.querySelector("#caducidad");
    const precioField = document.querySelector("#precioUnitario");

    // Cargar la lista de medicamentos registrados
    const loadMedicamentos = async () => {
        try {
            const response = await fetch('../../controllers/admedi.controller.php', {
                method: "POST",
                body: new URLSearchParams({ operation: 'getAllMedicamentos' })
            });

            if (!response.ok) {
                throw new Error("Error al cargar medicamentos");
            }

            const medicamentos = await response.json();

            // Mostrar la respuesta en la consola para depuración
            console.log("Respuesta del servidor:", medicamentos);

            // Validar que 'medicamentos' es un arreglo
            if (!Array.isArray(medicamentos)) {
                throw new Error("La respuesta no es un arreglo");
            }

            medicamentosTable.innerHTML = medicamentos.map(med => `
                <tr>
                    <td>${med.idMedicamento}</td>
                    <td>${med.nombreMedicamento}</td>
                    <td>${med.cantidad}</td>
                    <td>${med.precioUnitario}</td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm" onclick="eliminarMedicamento(${med.idMedicamento})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error:', error);
        }
    };


    // Definir la función eliminarMedicamento en el contexto global
    window.eliminarMedicamento = async (idMedicamento) => {
        if (!confirm('¿Estás seguro de que deseas eliminar este medicamento?')) return;

        try {
            const response = await fetch('../../controllers/admedi.controller.php', {
                method: "DELETE",
                body: new URLSearchParams({ idMedicamento })
            });

            if (!response.ok) {
                throw new Error("Error al eliminar el medicamento");
            }

            const result = await response.json();

            if (result.status === "success") {
                alert(result.message);
                loadMedicamentos(); // Recargar la lista después de eliminar
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert("Error en la solicitud: " + error.message);
            console.error('Error:', error);
        }
    };

    // Función para manejar las actualizaciones de stock
    const actualizarStock = async (tipoMovimiento) => {
        idTipomovimiento.value = tipoMovimiento;

        const formData = new FormData(formMedicamento);
        const data = new URLSearchParams(formData);
        data.append('operation', 'movimiento');

        try {
            const response = await fetch('../../controllers/admedi.controller.php', {
                method: "POST",
                body: data
            });

            if (!response.ok) {
                throw new Error("Error al actualizar el stock");
            }

            const result = await response.json();

            if (result.status === "success") {
                alert(result.message);
                formMedicamento.reset();
                loadMedicamentos();
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert("Error en la solicitud: " + error.message);
            console.error('Error:', error);
        }
    };

    // Eventos para los botones de entrada y salida
    document.querySelector("#entrada-btn").addEventListener("click", () => {
        actualizarStock(1);
    });

    document.querySelector("#salida-btn").addEventListener("click", () => {
        actualizarStock(2);
    });

    // Evento para el botón de registrar un nuevo medicamento
    formMedicamento.addEventListener("submit", async (event) => {
        event.preventDefault();

        if (!idTipomovimiento.value) {
            // Validar campos necesarios para el registro de medicamentos
            if (!formMedicamento['nombreMedicamento'].value || !formMedicamento['cantidad'].value || !caducidadField.value || !precioField.value) {
                alert('Por favor, complete todos los campos requeridos para registrar el medicamento.');
                return;
            }

            // Cambiar la operación a 'registrar'
            const formData = new FormData(formMedicamento);
            const data = new URLSearchParams(formData);
            data.append('operation', 'registrar');

            try {
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: data
                });

                if (!response.ok) {
                    throw new Error("Error al registrar el medicamento");
                }

                const result = await response.json();

                if (result.status === "success") {
                    alert(result.message);
                    formMedicamento.reset();
                    loadMedicamentos();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert("Error en la solicitud: " + error.message);
                console.error('Error:', error);
            }
        }
    });

    loadMedicamentos();
});
</script>
