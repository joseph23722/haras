<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestionar Alimentos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Registrar entrada o salida de alimentos</li>
    </ol>

    <!-- Formulario de Alimentos -->
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-plus-circle"></i> Complete los datos</h5>
                </div>
                <div class="card-body">
                    <form id="form-alimentos" autocomplete="off">
                        <div class="row g-3">
                            <!-- Nombre del Alimento -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="nombreAlimento" id="nombreAlimento" class="form-control" required>
                                    <label for="nombreAlimento">Nombre del Alimento</label>
                                </div>
                            </div>

                            <!-- Cantidad -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" step="0.01" name="cantidad" id="cantidad" class="form-control" required>
                                    <label for="cantidad">Cantidad</label>
                                </div>
                            </div>

                            <!-- Costo -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" step="0.01" name="costo" id="costo" class="form-control">
                                    <label for="costo">Costo</label>
                                </div>
                            </div>

                            <!-- Tipo de Equino -->
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <select name="idTipoEquino" id="idTipoEquino" class="form-select" required>
                                        <option value="">Seleccione Tipo de Equino</option>
                                    </select>
                                    <label for="idTipoEquino">Tipo de Equino</label>
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

                            <!-- Fecha de Ingreso -->
                            <div class="col-md-12">
                                <div class="form-floating">
                                    <input type="datetime-local" name="fechaIngreso" id="fechaIngreso" class="form-control">
                                    <label for="fechaIngreso">Fecha de Ingreso</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary btn-sm" id="registrar-alimento"><i class="fas fa-save"></i> Registrar Movimiento</button>
                            <button type="reset" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabla de Alimentos Registrados -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-database"></i> Alimentos Registrados</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover table-bordered">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Cantidad</th>
                                <th>Costo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="alimentos-table">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>

<script>
   document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("#form-alimentos");
    const alimentosTable = document.querySelector("#alimentos-table");
    const idTipomovimiento = document.querySelector("#idTipomovimiento");
    const idTipoEquinoSelect = document.querySelector("#idTipoEquino");
    const costoField = document.querySelector("#costo");
    const fechaField = document.querySelector("#fechaIngreso");

    // Cargar la lista de tipos de equinos
    const loadTipoEquinos = async () => {
        try {
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                body: new URLSearchParams({ operation: 'getTipoEquinos' })
            });
            const tipos = await response.json();
            idTipoEquinoSelect.innerHTML = '<option value="">Seleccione Tipo de Equino</option>';
            tipos.forEach(tipo => {
                const option = document.createElement('option');
                option.value = tipo.idTipoEquino;
                option.textContent = tipo.tipoEquino;
                idTipoEquinoSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error:', error);
        }
    };

    // Cargar la lista de alimentos registrados
    const loadAlimentos = async () => {
        try {
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                body: new URLSearchParams({ operation: 'getAllAlimentos' })
            });
            const alimentos = await response.json();
            alimentosTable.innerHTML = alimentos.map(alimento => `
                <tr>
                    <td>${alimento.idAlimento}</td>
                    <td>${alimento.nombreAlimento}</td>
                    <td>${alimento.cantidad}</td>
                    <td>${alimento.costo}</td>
                    <td class="text-center">
                        <button class="btn btn-danger btn-sm" onclick="eliminarAlimento(${alimento.idAlimento})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Error:', error);
        }
    };

    // Definir la función eliminarAlimento en el contexto global
    window.eliminarAlimento = async (idAlimento) => {
        if (!confirm('¿Estás seguro de que deseas eliminar este alimento?')) return;

        try {
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: "DELETE",
                body: new URLSearchParams({ idAlimento })
            });
            const result = await response.json();

            if (result.status === "success") {
                alert(result.message);
                loadAlimentos(); // Recargar la lista de alimentos después de eliminar
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert("Error en la solicitud: " + error.message);
            console.error('Error:', error);
        }
    };

    // Función para verificar si un alimento ya existe
    const verificarNombreDuplicado = (nombre) => {
        const filas = document.querySelectorAll("#alimentos-table tr");
        for (const fila of filas) {
            const nombreAlimento = fila.children[1].textContent.trim();
            if (nombreAlimento.toLowerCase() === nombre.toLowerCase()) {
                return true;
            }
        }
        return false;
    };

    // Función para manejar las actualizaciones de stock
    const actualizarStock = async (tipoMovimiento) => {
        idTipomovimiento.value = tipoMovimiento;
        form.dataset.operation = 'actualizar_stock';

        costoField.removeAttribute('required');
        fechaField.removeAttribute('required');

        const formData = new FormData(form);
        const data = new URLSearchParams(formData);
        data.append('operation', 'actualizar_stock');

        try {
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                body: data
            });
            const result = await response.json();

            if (result.status === "success") {
                alert(result.message);
                form.reset();
                loadAlimentos();
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert("Error en la solicitud: " + error.message);
            console.error('Error:', error);
        }
    };

    // Evento para el botón de entrada
    document.querySelector("#entrada-btn").addEventListener("click", () => {
        actualizarStock(1);
    });

    // Evento para el botón de salida
    document.querySelector("#salida-btn").addEventListener("click", () => {
        actualizarStock(2);
    });

    // Evento para el botón de registrar un nuevo alimento
    form.addEventListener("submit", (event) => {
        event.preventDefault();

        if (!idTipomovimiento.value) {
            const nombreAlimento = document.querySelector("#nombreAlimento").value.trim();
            if (verificarNombreDuplicado(nombreAlimento)) {
                alert('El alimento ya existe. No se puede duplicar.');
                return;
            }

            costoField.setAttribute('required', 'required');
            fechaField.setAttribute('required', 'required');
        }

        form.submit();
    });

    loadTipoEquinos();
    loadAlimentos();
});
 
</script>
