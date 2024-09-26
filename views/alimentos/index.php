<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Gestionar Alimentos</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Alimento</h5>
        </div>

        <!-- Formulario para Registrar Alimento -->
        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-registrar-alimento" autocomplete="off">
                <div class="row g-3">
                    
                    <!-- Nombre del Alimento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nombreAlimento" id="nombreAlimento" class="form-control" required>
                            <label for="nombreAlimento"><i class="fas fa-seedling" style="color: #00b4d8;"></i> Nombre del Alimento</label>
                        </div>
                    </div>

                    <!-- Tipo de Alimento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="tipoAlimento" id="tipoAlimento" class="form-control" required>
                            <label for="tipoAlimento"><i class="fas fa-apple-alt" style="color: #00b4d8;"></i> Tipo de Alimento</label>
                        </div>
                    </div>

                    <!-- Cantidad -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="cantidad" id="cantidad" class="form-control" required min="0">
                            <label for="cantidad"><i class="fas fa-balance-scale" style="color: #0096c7;"></i> Cantidad</label>
                        </div>
                    </div>

                    <!-- Unidad de Medida -->
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

                    <!-- Costo -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" step="0.01" name="costo" id="costo" class="form-control">
                            <label for="costo"><i class="fas fa-dollar-sign" style="color: #0077b6;"></i> Costo</label>
                        </div>
                    </div>

                    <!-- Lote -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="lote" id="lote" class="form-control" required>
                            <label for="lote"><i class="fas fa-box" style="color: #0077b6;"></i> Lote</label>
                        </div>
                    </div>

                    <!-- Fecha de Caducidad -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fechaCaducidad" id="fechaCaducidad" class="form-control">
                            <label for="fechaCaducidad"><i class="fas fa-calendar-alt" style="color: #0077b6;"></i> Fecha de Caducidad</label>
                        </div>
                    </div>

                    <!-- Fecha de Ingreso -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="datetime-local" name="fechaIngreso" id="fechaIngreso" class="form-control" required>
                            <label for="fechaIngreso"><i class="fas fa-calendar-alt" style="color: #0077b6;"></i> Fecha de Ingreso</label>
                        </div>
                    </div>

                    <!-- Botones de Registrar -->
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="registrar-alimento" style="background-color: #0077b6; border: none;">
                            <i class="fas fa-save"></i> Registrar Alimento
                        </button>
                        <button type="reset" class="btn btn-secondary btn-lg shadow-sm" style="background-color: #adb5bd; border: none;">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
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

    <!-- Formulario para Entrada/Salida de Alimentos -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Entrada / Salida de Alimentos</h5>
        </div>
        <div class="card-body p-4" style="background-color: #f9f9f9;">
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
                            <label for="cantidad-movimiento"><i class="fas fa-balance-scale" style="color: #0096c7;"></i> Cantidad</label>
                        </div>
                    </div>

                    <!-- Tipo de Equino para Salida -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="tipoEquinoMovimiento" name="idTipoEquino" class="form-select">
                                <option value="">Seleccione Tipo de Equino (Solo para salida)</option>
                            </select>
                            <label for="tipoEquinoMovimiento">Tipo de Equino</label>
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
// Cargar tipos de equinos y alimentos registrados
document.addEventListener("DOMContentLoaded", () => {
    const formRegistrarAlimento = document.querySelector("#form-registrar-alimento");
    const formMovimientoAlimento = document.querySelector("#form-movimiento-alimento");
    const alimentosTable = document.querySelector("#alimentos-table");
    const alimentoSelect = document.querySelector("#alimento-select");
    const idTipomovimientoMovimiento = document.querySelector("#idTipomovimiento-movimiento");
    const tipoEquinoMovimiento = document.querySelector("#tipoEquinoMovimiento");

    // Validar cantidad positiva en movimiento
    document.querySelector("#cantidad-movimiento").addEventListener("input", (e) => {
        if (e.target.value < 0) {
            e.target.value = 0;
        }
    });

    // Validar cantidad positiva en registro
    document.querySelector("#cantidad").addEventListener("input", (e) => {
        if (e.target.value < 0) {
            e.target.value = 0;
        }
    });

    // Cargar la lista de alimentos registrados
    const loadAlimentos = async () => {
        try {
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                body: new URLSearchParams({ operation: 'getAllAlimentos' })
            });

            if (!response.ok) {
                throw new Error("Error al cargar alimentos");
            }

            const alimentos = await response.json();

            // Mostrar el stock actual (stockFinal) y el último movimiento (cantidad)
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

            // Llenar el select con los alimentos registrados
            alimentoSelect.innerHTML = '<option value="">Seleccione un Alimento</option>';
            alimentos.forEach(alim => {
                const option = document.createElement('option');
                option.value = alim.nombreAlimento;
                option.textContent = alim.nombreAlimento;
                alimentoSelect.appendChild(option);
            });
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

            if (!response.ok) {
                throw new Error("Error al cargar los tipos de equinos.");
            }

            const tipoEquinos = await response.json();

            // Limpiar el select de opciones previas
            tipoEquinoMovimiento.innerHTML = '<option value="">Seleccione Tipo de Equino (Solo para salida)</option>';

            // Llenar el select con las opciones
            tipoEquinos.forEach(equino => {
                const option = document.createElement("option");
                option.value = equino.idTipoEquino;
                option.textContent = equino.tipoEquino;
                tipoEquinoMovimiento.appendChild(option);
            });
        } catch (error) {
            console.error('Error:', error);
        }
    };

    // Llamar a la función para cargar los tipos de equinos al cargar la página
    loadAlimentos();
    loadTipoEquinos();

    // Evento para registrar un nuevo alimento
    formRegistrarAlimento.addEventListener("submit", async (event) => {
        event.preventDefault();

        const formData = new FormData(formRegistrarAlimento);
        
        // Asegurarse de que la fecha esté presente
        const fechaIngreso = document.querySelector("#fechaIngreso").value;
        if (!fechaIngreso) {
            alert("Por favor, ingrese la fecha de ingreso.");
            return;
        }

        const data = new URLSearchParams(formData);
        data.append('operation', 'registrar');

        try {
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                body: data
            });

            if (!response.ok) {
                throw new Error("Error al registrar el alimento");
            }

            const result = await response.json();

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

    // Función para manejar movimientos de alimentos (entrada/salida)
    const actualizarStock = async (tipoMovimiento) => {
        if (!alimentoSelect.value) {
            alert("Por favor, seleccione un alimento.");
            return;
        }

        idTipomovimientoMovimiento.value = tipoMovimiento;

        if (tipoMovimiento === 2 && !tipoEquinoMovimiento.value) {
            alert("idTipoEquino es obligatorio para las salidas.");
            return;
        }

        const formData = new FormData(formMovimientoAlimento);
        const data = new URLSearchParams(formData);
        data.append('operation', 'movimiento');

        try {
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                body: data
            });

            if (!response.ok) {
                throw new Error("Error al realizar movimiento");
            }

            const result = await response.json();

            if (result.status === "success") {
                alert(result.message);
                formMovimientoAlimento.reset();
                loadAlimentos();
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

            if (!response.ok) {
                throw new Error("Error al eliminar el alimento");
            }

            const result = await response.json();

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

    // Eventos para los botones de entrada y salida
    document.querySelector("#entrada-btn").addEventListener("click", () => {
        actualizarStock(1); // Entrada
    });

    document.querySelector("#salida-btn").addEventListener("click", () => {
        actualizarStock(2); // Salida
    });

});
</script>
