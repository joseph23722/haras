<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Gestionar Alimentos</h1>

    <!-- Botón para abrir el modal de registrar alimentos -->
    <div class="d-flex justify-content-end">
        <button class="btn btn-primary btn-lg mb-3" data-bs-toggle="modal" data-bs-target="#modalRegistrarAlimento">
            <i class="fas fa-plus-circle"></i> Registrar Nuevo Alimento
        </button>
    </div>

    <!-- Tabla de Alimentos Registrados -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5><i class="fas fa-database"></i> Alimentos Registrados</h5>
        </div>
        <div class="card-body" style="background-color: #f9f9f9;">
            <table class="table table-striped table-hover table-bordered">
                <thead style="background-color: #caf0f8; color: #003366;">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Stock Final</th>
                        <th>Costo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="alimentos-table"></tbody>
            </table>
        </div>
    </div>

    <!-- Botón para abrir el modal de movimientos (entrada/salida) -->
    <div class="d-flex justify-content-end">
        <button class="btn btn-outline-success btn-lg" data-bs-toggle="modal" data-bs-target="#modalMovimientoAlimento">
            <i class="fas fa-exchange-alt"></i> Movimientos (Entrada / Salida)
        </button>
    </div>

    <!-- Modal para Registrar Nuevo Alimento -->
    <div class="modal fade" id="modalRegistrarAlimento" tabindex="-1" aria-labelledby="modalRegistrarAlimentoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #00b4d8; color: white;">
                    <h5 class="modal-title" id="modalRegistrarAlimentoLabel">Registrar Nuevo Alimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="form-registrar-alimento" autocomplete="off">
                        <div class="row g-3">
                            <!-- Campos del formulario para registrar alimento -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="nombreAlimento" id="nombreAlimento" class="form-control" required>
                                    <label for="nombreAlimento">Nombre del Alimento</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="tipoAlimento" id="tipoAlimento" class="form-control" required>
                                    <label for="tipoAlimento">Tipo de Alimento</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" name="cantidad" id="cantidad" class="form-control" required min="0">
                                    <label for="cantidad">Cantidad</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select id="unidadMedida" name="unidadMedida" class="form-select" required>
                                        <option value="">Seleccione Unidad de Medida</option>
                                        <option value="Kg">Kg</option>
                                        <option value="Gr">Gr</option>
                                        <option value="Lt">Lt</option>
                                    </select>
                                    <label for="unidadMedida">Unidad de Medida</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" step="0.01" name="costo" id="costo" class="form-control" required>
                                    <label for="costo">Costo</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="lote" id="lote" class="form-control" required>
                                    <label for="lote">Lote</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" name="fechaCaducidad" id="fechaCaducidad" class="form-control" required>
                                    <label for="fechaCaducidad">Fecha de Caducidad</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="datetime-local" name="fechaIngreso" id="fechaIngreso" class="form-control" required>
                                    <label for="fechaIngreso">Fecha de Ingreso</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="registrar-alimento">Registrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Movimientos de Entrada/Salida -->
    <div class="modal fade" id="modalMovimientoAlimento" tabindex="-1" aria-labelledby="modalMovimientoAlimentoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #48cae4; color: white;">
                    <h5 class="modal-title" id="modalMovimientoAlimentoLabel">Registrar Movimiento de Alimento (Entrada/Salida)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="form-movimiento-alimento" autocomplete="off">
                        <div class="row g-3">
                            <!-- Seleccionar Alimento -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select id="alimento-select" name="nombreAlimento" class="form-select" required>
                                        <option value="">Seleccione un Alimento</option>
                                    </select>
                                    <label for="alimento-select">Alimento</label>
                                </div>
                            </div>
                            <!-- Cantidad para Movimiento -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" name="cantidad" id="cantidad-movimiento" class="form-control" required min="0">
                                    <label for="cantidad-movimiento">Cantidad</label>
                                </div>
                            </div>
                            <!-- Tipo de Equino para Salida (si es salida) -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select id="tipoEquinoMovimiento" name="idTipoEquino" class="form-select">
                                        <option value="">Seleccione Tipo de Equino (Solo para salida)</option>
                                    </select>
                                    <label for="tipoEquinoMovimiento">Tipo de Equino</label>
                                </div>
                            </div>
                            <!-- Lote y Fecha de Caducidad para Entrada -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="lote" id="lote-movimiento" class="form-control" placeholder="Lote" required>
                                    <label for="lote-movimiento">Lote (Solo para entrada)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" name="fechaCaducidad" id="fechaCaducidad-movimiento" class="form-control" required>
                                    <label for="fechaCaducidad-movimiento">Fecha de Caducidad (Solo para entrada)</label>
                                </div>
                            </div>
                            <!-- Merma (Solo para salida) -->
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="number" step="0.01" name="merma" id="merma" class="form-control">
                                    <label for="merma">Merma (Solo para salida)</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="entrada-btn">Entrada</button>
                    <button type="button" class="btn btn-danger" id="salida-btn">Salida</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Movimientos -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5><i class="fas fa-history"></i> Historial de Movimientos</h5>
        </div>
        <div class="card-body" style="background-color: #f9f9f9;">
            <table class="table table-striped table-hover table-bordered">
                <thead style="background-color: #caf0f8; color: #003366;">
                    <tr>
                        <th>ID</th>
                        <th>Alimento</th>
                        <th>Tipo Movimiento</th>
                        <th>Cantidad</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody id="historial-movimientos-table"></tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const formRegistrarAlimento = document.querySelector("#form-registrar-alimento");
        const formMovimientoAlimento = document.querySelector("#form-movimiento-alimento");
        const alimentosTable = document.querySelector("#alimentos-table");
        const historialMovimientosTable = document.querySelector("#historial-movimientos-table");
        const alimentoSelect = document.querySelector("#alimento-select");
        const tipoEquinoMovimiento = document.querySelector("#tipoEquinoMovimiento");

        // Validar cantidad positiva en movimiento y registro
        document.querySelector("#cantidad-movimiento").addEventListener("input", (e) => {
            if (e.target.value < 0) e.target.value = 0;
        });

        document.querySelector("#cantidad").addEventListener("input", (e) => {
            if (e.target.value < 0) e.target.value = 0;
        });

        // Función para cargar los alimentos registrados
        const loadAlimentos = async () => {
            try {
                const response = await fetch('../../controllers/alimento.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({ operation: 'getAllAlimentos' })
                });

                if (!response.ok) throw new Error("Error al cargar alimentos");

                const alimentos = await response.json();

                if (Array.isArray(alimentos)) {
                    // Mostrar alimentos en la tabla
                    alimentosTable.innerHTML = alimentos.map(alim => `
                        <tr>
                            <td>${alim.idAlimento}</td>
                            <td>${alim.nombreAlimento}</td>
                            <td>${alim.stockFinal}</td>
                            <td>${alim.costo}</td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-sm" onclick="eliminarAlimento(${alim.idAlimento})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `).join('');

                    // Llenar el select de alimentos para los movimientos
                    alimentoSelect.innerHTML = '<option value="">Seleccione un Alimento</option>';
                    alimentos.forEach(alim => {
                        const option = document.createElement('option');
                        option.value = alim.nombreAlimento;
                        option.textContent = alim.nombreAlimento;
                        alimentoSelect.appendChild(option);
                    });
                } else {
                    alimentosTable.innerHTML = '<tr><td colspan="5">No se encontraron alimentos.</td></tr>';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        };

        // Función para cargar los tipos de equinos
        const loadTipoEquinos = async () => {
            try {
                const response = await fetch('../../controllers/alimento.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({ operation: 'getTipoEquinos' })
                });

                if (!response.ok) throw new Error("Error al cargar tipos de equinos");

                const tipos = await response.json();

                tipoEquinoMovimiento.innerHTML = '<option value="">Seleccione Tipo de Equino (Solo para salida)</option>';
                tipos.forEach(tipo => {
                    const option = document.createElement('option');
                    option.value = tipo.idTipoEquino;
                    option.textContent = tipo.tipoEquino;
                    tipoEquinoMovimiento.appendChild(option);
                });
            } catch (error) {
                console.error('Error:', error);
            }
        };

        // Función para cargar el historial de movimientos
        const loadHistorialMovimientos = async () => {
            try {
                const response = await fetch('../../controllers/alimento.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({ operation: 'getHistorialMovimientos' })
                });

                if (!response.ok) throw new Error("Error al cargar historial de movimientos");

                const movimientos = await response.json();

                if (Array.isArray(movimientos)) {
                    historialMovimientosTable.innerHTML = movimientos.map(mov => `
                        <tr>
                            <td>${mov.idMovimiento}</td>
                            <td>${mov.nombreAlimento}</td>
                            <td>${mov.tipoMovimiento}</td>
                            <td>${mov.cantidad}</td>
                            <td>${mov.fechaMovimiento}</td>
                        </tr>
                    `).join('');
                } else {
                    historialMovimientosTable.innerHTML = '<tr><td colspan="5">No se encontraron movimientos.</td></tr>';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        };

        // Cargar todo al inicio
        loadAlimentos();
        loadTipoEquinos();
        loadHistorialMovimientos();

        // Función para registrar un nuevo alimento
        formRegistrarAlimento.addEventListener("submit", async (event) => {
            event.preventDefault();

            const formData = new FormData(formRegistrarAlimento);
            const data = new URLSearchParams(formData);
            data.append('operation', 'registrar');

            try {
                const response = await fetch('../../controllers/alimento.controller.php', {
                    method: "POST",
                    body: data
                });

                const resultText = await response.text(); // Obtener la respuesta como texto
                console.log(resultText); // Mostrar la respuesta en la consola para depurar

                // Intenta convertir la respuesta a JSON
                let result;
                try {
                    result = JSON.parse(resultText); // Convierte la respuesta a JSON
                } catch (e) {
                    console.error('Error al convertir la respuesta a JSON', e);
                    alert('Ocurrió un error inesperado. Revisa la consola para más detalles.');
                    return;
                }

                if (result.status === "success") {
                    alert(result.message);
                    formRegistrarAlimento.reset();
                    loadAlimentos();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert("Error en la solicitud: " + error.message);
                console.error('Error:', error);
            }
        });

        // Función para manejar entradas/salidas de alimentos
        const actualizarStock = async (tipoMovimiento) => {
            if (!alimentoSelect.value) {
                alert("Por favor, seleccione un alimento.");
                return;
            }

            // Validaciones para entradas y salidas
            if (tipoMovimiento === 1) {  // Entrada
                if (!document.querySelector("#lote-movimiento").value || !document.querySelector("#fechaCaducidad-movimiento").value) {
                    alert("Por favor, ingrese el lote y la fecha de caducidad para la entrada.");
                    return;
                }
            } else if (tipoMovimiento === 2 && !tipoEquinoMovimiento.value) {  // Salida
                alert("Seleccione un tipo de equino para la salida.");
                return;
            }

            const formData = new FormData(formMovimientoAlimento);
            const data = new URLSearchParams(formData);
            data.append('operation', 'movimiento');
            data.append('idTipomovimiento', tipoMovimiento);  // Asignar tipo de movimiento (entrada o salida)

            try {
                const response = await fetch('../../controllers/alimento.controller.php', {
                    method: "POST",
                    body: data
                });

                const resultText = await response.text(); // Obtener la respuesta como texto
                console.log(resultText); // Mostrar la respuesta en la consola para depurar

                // Intenta convertir la respuesta a JSON
                let result;
                try {
                    result = JSON.parse(resultText); // Convierte la respuesta a JSON
                } catch (e) {
                    console.error('Error al convertir la respuesta a JSON', e);
                    alert('Ocurrió un error inesperado. Revisa la consola para más detalles.');
                    return;
                }

                if (result.status === "success") {
                    alert(result.message);
                    formMovimientoAlimento.reset();
                    loadAlimentos();
                    loadHistorialMovimientos();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert("Error en la solicitud: " + error.message);
                console.error('Error:', error);
            }
        };

        // Función para eliminar un alimento
        window.eliminarAlimento = async (idAlimento) => {
            if (!confirm('¿Estás seguro de que deseas eliminar este alimento?')) return;

            const data = new URLSearchParams();
            data.append('operation', 'eliminar');
            data.append('idAlimento', idAlimento);

            try {
                const response = await fetch('../../controllers/alimento.controller.php', {
                    method: "POST",
                    body: data
                });

                const resultText = await response.text(); // Obtener la respuesta como texto
                console.log(resultText); // Mostrar la respuesta en la consola para depurar

                // Intenta convertir la respuesta a JSON
                let result;
                try {
                    result = JSON.parse(resultText); // Convierte la respuesta a JSON
                } catch (e) {
                    console.error('Error al convertir la respuesta a JSON', e);
                    alert('Ocurrió un error inesperado. Revisa la consola para más detalles.');
                    return;
                }

                if (result.status === "success") {
                    alert(result.message);
                    loadAlimentos();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert("Error en la solicitud: " + error.message);
                console.error('Error:', error);
            }
        };

        // Eventos para botones de entrada y salida
        document.querySelector("#entrada-btn").addEventListener("click", () => {
            actualizarStock(1); // Entrada
        });

        document.querySelector("#salida-btn").addEventListener("click", () => {
            actualizarStock(2); // Salida
        });
    });
</script>
