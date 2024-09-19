<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Gestionar Medicamentos</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Medicamento</h5>
        </div>

        <!-- Formulario para Registrar Medicamento -->
        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-registrar-medicamento" autocomplete="off">
                <div class="row g-3">
                    
                    <!-- Nombre del Medicamento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nombreMedicamento" id="nombreMedicamento" class="form-control" required>
                            <label for="nombreMedicamento"><i class="fas fa-capsules" style="color: #00b4d8;"></i> Nombre del Medicamento</label>
                        </div>
                    </div>

                    <!-- Cantidad -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="cantidad" id="cantidad" class="form-control" required min="0">
                            <label for="cantidad"><i class="fas fa-balance-scale" style="color: #0096c7;"></i> Cantidad</label>
                        </div>
                    </div>

                    <!-- Fecha de Caducidad -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="caducidad" id="caducidad" class="form-control" required>
                            <label for="caducidad"><i class="fas fa-calendar-alt" style="color: #ba55d3;"></i> Fecha de Caducidad</label>
                        </div>
                    </div>

                    <!-- Precio Unitario -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" step="0.01" name="precioUnitario" id="precioUnitario" class="form-control" required>
                            <label for="precioUnitario"><i class="fas fa-dollar-sign" style="color: #0077b6;"></i> Precio Unitario</label>
                        </div>
                    </div>

                    <!-- Botón de Registrar -->
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="registrar-medicamento" style="background-color: #0077b6; border: none;">
                            <i class="fas fa-save"></i> Registrar Medicamento
                        </button>
                        <button type="reset" class="btn btn-secondary btn-lg shadow-sm" style="background-color: #adb5bd; border: none;">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Medicamentos Registrados -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5><i class="fas fa-database"></i> Medicamentos Registrados</h5>
        </div>
        <div class="card-body" style="background-color: #f9f9f9;">
            <table class="table table-striped table-hover table-bordered">
                <thead style="background-color: #caf0f8; color: #003366;">
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

    <!-- Formulario para Entrada/Salida de Medicamentos -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Entrada / Salida de Medicamentos</h5>
        </div>
        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-movimiento-medicamento" autocomplete="off">
                <div class="row g-3">
                    
                    <!-- Seleccionar Medicamento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="medicamento-select" name="nombreMedicamento" class="form-select" required>
                                <option value="">Seleccione un Medicamento</option>
                            </select>
                            <label for="medicamento-select">Medicamento</label>
                        </div>
                    </div>

                    <!-- Cantidad para Movimiento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="cantidad" id="cantidad-movimiento" class="form-control" required min="0">
                            <label for="cantidad-movimiento"><i class="fas fa-balance-scale" style="color: #0096c7;"></i> Cantidad</label>
                        </div>
                    </div>

                    <!-- Botones de Entrada/Salida -->
                    <div class="col-md-12 text-center mt-3">
                        <button type="button" class="btn btn-outline-success btn-lg" id="entrada-btn" style="background-color: #48cae4; color: #003366; border: none;">
                            <i class="fas fa-arrow-up"></i> Entrada
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-lg ms-2" id="salida-btn" style="background-color: #ff7f50; color: #003366; border: none;">
                            <i class="fas fa-arrow-down"></i> Salida
                        </button>
                    </div>
                    
                    <input type="hidden" name="idTipomovimiento" id="idTipomovimiento-movimiento">
                </div>
            </form>
        </div>
    </div>

</div>

<?php require_once '../../footer.php'; ?>


<script>
document.addEventListener("DOMContentLoaded", () => {
    const formRegistrarMedicamento = document.querySelector("#form-registrar-medicamento");
    const formMovimientoMedicamento = document.querySelector("#form-movimiento-medicamento");
    const medicamentosTable = document.querySelector("#medicamentos-table");
    const medicamentoSelect = document.querySelector("#medicamento-select");
    const idTipomovimientoMovimiento = document.querySelector("#idTipomovimiento-movimiento");

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
            console.log("Respuesta del servidor:", medicamentos);

            if (!Array.isArray(medicamentos)) {
                throw new Error("La respuesta no es un arreglo");
            }

            // Poblar la tabla de medicamentos
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

            // Poblar el selector de medicamentos para movimientos
            medicamentoSelect.innerHTML = '<option value="">Seleccione un Medicamento</option>';
            medicamentos.forEach(med => {
                const option = document.createElement('option');
                option.value = med.nombreMedicamento;
                option.textContent = med.nombreMedicamento;
                medicamentoSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error:', error);
        }
    };

    // Evento para registrar un nuevo medicamento
    formRegistrarMedicamento.addEventListener("submit", async (event) => {
        event.preventDefault();

        const formData = new FormData(formRegistrarMedicamento);
        const data = new URLSearchParams(formData);
        data.append('operation', 'registrar');

        console.log("Datos enviados al servidor para registrar:", Object.fromEntries(data.entries()));

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
                formRegistrarMedicamento.reset();
                loadMedicamentos();
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert("Error en la solicitud: " + error.message);
            console.error('Error:', error);
        }
    });

    // Función para manejar movimientos de medicamentos (entrada/salida)
    const actualizarStock = async (tipoMovimiento) => {
        idTipomovimientoMovimiento.value = tipoMovimiento;

        const formData = new FormData(formMovimientoMedicamento);
        const data = new URLSearchParams(formData);
        data.append('operation', 'movimiento');

        console.log("Datos enviados al servidor para movimiento:", Object.fromEntries(data.entries()));

        try {
            const response = await fetch('../../controllers/admedi.controller.php', {
                method: "POST",
                body: data
            });

            if (!response.ok) {
                throw new Error("Error al realizar movimiento");
            }

            const result = await response.json();

            if (result.status === "success") {
                alert(result.message);
                formMovimientoMedicamento.reset();
                loadMedicamentos();
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert("Error en la solicitud: " + error.message);
            console.error('Error:', error);
        }
    };

    // Función para eliminar un medicamento
    window.eliminarMedicamento = async (idMedicamento) => {
        if (!confirm('¿Estás seguro de que deseas eliminar este medicamento?')) return;

        const data = new URLSearchParams();
        data.append('operation', 'eliminar');
        data.append('idMedicamento', idMedicamento);

        try {
            const response = await fetch('../../controllers/admedi.controller.php', {
                method: "POST",
                body: data
            });

            if (!response.ok) {
                throw new Error("Error al eliminar el medicamento");
            }

            const result = await response.json();

            if (result.status === "success") {
                alert(result.message);
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
        actualizarStock(1); // Entrada
    });

    document.querySelector("#salida-btn").addEventListener("click", () => {
        actualizarStock(2); // Salida
    });

    // Inicializar la carga de medicamentos
    loadMedicamentos();
});

</script>
