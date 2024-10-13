<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título de la página -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">
        Gestionar Medicamentos
    </h1>

    <!-- Sección del formulario para registrar un medicamento -->
    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Medicamento</h5>
        </div>
        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-registrar-medicamento" autocomplete="off">
                <div class="row g-3">
                    <!-- Nombre del Medicamento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nombreMedicamento" id="nombreMedicamento" class="form-control" required>
                            <label for="nombreMedicamento">
                                <i class="fas fa-capsules" style="color: #00b4d8;"></i> Nombre del Medicamento
                            </label>
                        </div>
                    </div>

                    <!-- Descripción del Medicamento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="descripcion" id="descripcion" class="form-control">
                            <label for="descripcion">
                                <i class="fas fa-info-circle" style="color: #6d6875;"></i> Descripción
                            </label>
                        </div>
                    </div>

                    <!-- Lote del Medicamento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="lote" id="lote" class="form-control" value="LOTE-" required>
                            <label for="lote">
                                <i class="fas fa-box" style="color: #6d6875;"></i> Lote del Medicamento (LOTE-)
                            </label>
                        </div>
                    </div>

                    <!-- Presentación -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="presentacion" id="presentacion" class="form-select" required>
                                <option value="">Seleccione la Presentación</option>
                                <option value="nuevo">Agregar nueva presentación...</option>
                            </select>
                            <label for="presentacion">
                                <i class="fas fa-prescription-bottle-alt" style="color: #8e44ad;"></i> Presentación
                            </label>
                        </div>
                    </div>
                    <!-- Composición (Dosis) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="dosis" id="dosis" class="form-control" required pattern="^[0-9]+[a-zA-Z\.\/]*$" title="Formato válido: número seguido de una unidad de medida (ej. mg, g, ml, etc.)">
                            <label for="dosis">
                                <i class="fas fa-weight" style="color: #0096c7;"></i> Composición (ej. 500 mg)
                            </label>
                        </div>
                    </div>

                    <!-- Tipo de Medicamento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="tipo" id="tipo" class="form-select" required>
                                <option value="">Seleccione el Tipo de Medicamento</option>
                                <option value="nuevo">Agregar nuevo tipo...</option>
                            </select>
                            <label for="tipo">
                                <i class="fas fa-pills" style="color: #ff6b6b;"></i> Tipo de Medicamento
                            </label>
                        </div>
                    </div>

                    <!-- Cantidad en Stock -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="cantidad_stock" id="cantidad_stock" class="form-control" required min="0">
                            <label for="cantidad_stock">
                                <i class="fas fa-balance-scale" style="color: #0096c7;"></i> Cantidad en Stock
                            </label>
                        </div>
                    </div>

                    <!-- Stock Mínimo -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="stockMinimo" id="stockMinimo" class="form-control" value="10" required min="0">
                            <label for="stockMinimo">
                                <i class="fas fa-battery-quarter" style="color: #ff0000;"></i> Stock Mínimo
                            </label>
                        </div>
                    </div>

                    <!-- Fecha de Caducidad -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fechaCaducidad" id="fechaCaducidad" class="form-control" required min="<?= date('Y-m-d'); ?>">
                            <label for="fechaCaducidad">
                                <i class="fas fa-calendar-alt" style="color: #ba55d3;"></i> Fecha de Caducidad
                            </label>
                        </div>
                    </div>

                   <!-- Precio Unitario -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" step="0.01" name="precioUnitario" id="precioUnitario" class="form-control" required min="0">
                            <label for="precioUnitario">
                                <i class="fas fa-dollar-sign" style="color: #0077b6;"></i> Precio Unitario
                            </label>
                        </div>
                    </div>

                    <!-- Div para mostrar mensajes dinámicos -->
                    <div id="message-area" style="width: 100%; padding: 10px; background-color: #e0f7fa; color: #006064; text-align: center; font-weight: bold; border-radius: 5px; display: none;"></div>

                    <!-- Botones de acción -->
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg" style="background-color: #0077b6; border: none;">
                            <i class="fas fa-save"></i> Registrar Medicamento
                        </button>
                        <button type="reset" class="btn btn-secondary btn-lg" style="background-color: #adb5bd; border: none;">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <!-- Botón de Sugerencias -->
                        <button type="button" class="btn btn-info btn-lg" style="background-color: #17a2b8; border: none;" id="btnSugerencias" data-bs-toggle="modal" data-bs-target="#modalSugerencias">
                            <i class="fas fa-lightbulb"></i> Ver Sugerencias
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Botones para Registrar Entrada y Salida -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Opciones de Movimiento</h5>
        </div>
        <div class="card-body text-center">
            <button class="btn btn-outline-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalEntrada">
                <i class="fas fa-arrow-up"></i> Registrar Entrada de Medicamento
            </button>
            <button class="btn btn-outline-danger btn-lg" data-bs-toggle="modal" data-bs-target="#modalSalida">
                <i class="fas fa-arrow-down"></i> Registrar Salida de Medicamento
            </button>
        </div>
    </div>

    <!-- Tabla de Medicamentos Registrados -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5><i class="fas fa-database"></i> Medicamentos Registrados</h5>
        </div>
        <div class="card-body" style="background-color: #f9f9f9; overflow-x: auto;">
            <table id="medicamentosTable" class="table table-striped table-hover table-bordered" style="width: 100%;">
                <tbody id="medicamentos-table"></tbody> <!-- Solo se deja el tbody, sin thead ni th -->
            </table>
        </div>
    </div>


    <!-- Modal para Agregar Nuevo Tipo de Medicamento -->
    <div class="modal fade" id="modalAgregarTipo" tabindex="-1" aria-labelledby="modalAgregarTipoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarTipoLabel">Agregar Nuevo Tipo de Medicamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAgregarTipo">
                        <div class="form-group mb-3">
                            <label for="nuevoTipoMedicamento" class="form-label">Nuevo Tipo de Medicamento</label>
                            <input type="text" name="nuevoTipoMedicamento" id="nuevoTipoMedicamento" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar Tipo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Agregar Nueva Presentación -->
    <div class="modal fade" id="modalAgregarPresentacion" tabindex="-1" aria-labelledby="modalAgregarPresentacionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAgregarPresentacionLabel">Agregar Nueva Presentación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formAgregarPresentacion">
                        <div class="form-group mb-3">
                            <label for="nuevaPresentacion" class="form-label">Nueva Presentación</label>
                            <input type="text" name="nuevaPresentacion" id="nuevaPresentacion" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar Presentación</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Registrar Entrada de Medicamento -->
    <div class="modal fade" id="modalEntrada" tabindex="-1" aria-labelledby="modalEntradaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEntradaLabel">Registrar Entrada de Medicamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEntrada">
                        <div class="form-group mb-3">
                            <label for="entradaMedicamento" class="form-label">Medicamento</label>
                            <select name="nombreMedicamento" id="entradaMedicamento" class="form-control" required>
                                <!-- Aquí puedes cargar las opciones de medicamentos disponibles -->
                            </select>
                        </div>
                        
                        <!-- Presentación -->
                        <div class="form-group mb-3">
                            <label for="entradaPresentacion" class="form-label">Presentación</label>
                            <select name="presentacion" id="entradaPresentacion" class="form-control" required>
                                <option value="">Seleccione la Presentación</option>
                                <option value="nuevo">Agregar nueva presentación...</option>
                            </select>
                        </div>
                        
                        <!-- Dosis -->
                        <div class="form-group mb-3">
                            <label for="entradaDosis" class="form-label">Composición</label>
                            <input type="text" name="dosis" id="entradaDosis" class="form-control" required>
                        </div>

                        <!-- Tipo de Medicamento -->
                        <div class="form-group mb-3">
                            <label for="tipoentrada" class="form-label">Tipo de Medicamento</label>
                            <select name="tipo" id="tipoentrada" class="form-select" required>
                                <option value="">Seleccione el Tipo de Medicamento</option>
                                <option value="nuevo">Agregar nuevo tipo...</option>
                            </select>
                        </div>

                        <!-- Lote -->
                        <div class="form-group mb-3">
                            <label for="entradaLote" class="form-label">Lote</label>
                            <input type="text" name="lote" id="entradaLote" class="form-control" required>
                        </div>
                        
                        <!-- Cantidad -->
                        <div class="form-group mb-3">
                            <label for="entradaCantidad" class="form-label">Cantidad</label>
                            <input type="number" name="cantidad" id="entradaCantidad" class="form-control" required min="0">
                        </div>

                        <!-- Fecha de Caducidad -->
                        <div class="form-group mb-3">
                            <label for="entradaFechaCaducidad" class="form-label">Fecha de Caducidad</label>
                            <input type="date" name="fechaCaducidad" id="entradaFechaCaducidad" class="form-control" required min="<?= date('Y-m-d'); ?>">
                        </div>
                        
                        <!-- Precio Unitario -->
                        <div class="form-group mb-3">
                            <label for="entradaPrecio" class="form-label">Precio Unitario</label>
                            <input type="number" step="0.01" name="nuevoPrecio" id="entradaPrecio" class="form-control" required min="0">
                        </div>

                        <!-- Stock Mínimo -->
                        <div class="form-group mb-3">
                            <label for="entradaStockMinimo" class="form-label">Stock Mínimo</label>
                            <input type="number" name="stockMinimo" id="entradaStockMinimo" class="form-control" required min="0" value="10">
                        </div>

                        <!-- Botón para registrar la entrada -->
                        <button type="submit" class="btn btn-primary">Registrar Entrada</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Registrar Salida de Medicamento -->
    <div class="modal fade" id="modalSalida" tabindex="-1" aria-labelledby="modalSalidaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSalidaLabel">Registrar Salida de Medicamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formSalida">
                        <div class="form-group mb-3">
                            <label for="salidaMedicamento" class="form-label">Medicamento</label>
                            <select name="nombreMedicamento" id="salidaMedicamento" class="form-control" required></select>
                        </div>
                        <!-- Cantidad -->
                        <div class="form-group mb-3">
                            <label for="salidaCantidad" class="form-label">Cantidad</label>
                            <input type="number" name="cantidad" id="salidaCantidad" class="form-control" required min="0">
                        </div>
                        <!-- Botón para registrar la salida -->
                        <button type="submit" class="btn btn-danger">Registrar Salida</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal de Sugerencias -->
    <div class="modal fade" id="modalSugerencias" tabindex="-1" aria-labelledby="modalSugerenciasLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSugerenciasLabel">Sugerencias de Medicamentos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tipo de Medicamento</th>
                                <th>Presentación</th>
                                <th>Dosis</th>
                            </tr>
                        </thead>
                        <tbody id="sugerenciasTableBody">
                            <!-- Aquí se insertarán las sugerencias dinámicamente -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Incluye SweetAlert -->
<script src="../../swalcustom.js"></script>



<script>
    document.addEventListener("DOMContentLoaded", () => {

        let notificacionesMostradas = false;

        const formRegistrarMedicamento = document.querySelector("#form-registrar-medicamento");
        const formEntrada = document.querySelector("#formEntrada");
        const formSalida = document.querySelector("#formSalida");
        const medicamentosTable = document.querySelector("#medicamentos-table");
        const tipoMedicamentoSelect = document.querySelector("#tipo");
        const formAgregarTipo = document.querySelector("#formAgregarTipo");
        const presentacionSelect = document.querySelector("#presentacion");
        const formAgregarPresentacion = document.querySelector("#formAgregarPresentacion");

        const messageArea = document.getElementById("message-area");
        const btnSugerencias = document.getElementById("btnSugerencias");

        // Función para mostrar mensajes dinámicos
        function mostrarMensaje(mensaje, tipo) {
            messageArea.style.display = 'block';
            messageArea.textContent = mensaje;

            if (tipo === 'success') {
                messageArea.style.backgroundColor = '#d4edda'; // Fondo verde para éxito
                messageArea.style.color = '#155724'; // Texto verde oscuro
            } else if (tipo === 'error') {
                messageArea.style.backgroundColor = '#f8d7da'; // Fondo rojo para error
                messageArea.style.color = '#721c24'; // Texto rojo oscuro
            }

            // Ocultar el mensaje después de 5 segundos
            setTimeout(() => {
                messageArea.style.display = 'none';
            }, 5000);
        }

        // Función para validar el campo lote
        async function validarLote(loteInput) {
            const loteValue = loteInput.value.trim();
            if (loteValue === 'LOTE-') {
                await ask('El campo Lote está incompleto. Agrega algo después de "LOTE-".');
                return false;
            }
            return true;
        }

        // Evento para el botón de sugerencias
        btnSugerencias.addEventListener("click", async () => {
            try {
                // Hacer la solicitud al servidor para obtener todas las sugerencias
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({
                        operation: 'listarSugerenciasMedicamentos'
                    })
                });

                const result = await response.json();

                if (result.status === "success") {
                    // Limpiar el contenido anterior de la tabla
                    const tableBody = document.getElementById('sugerenciasTableBody');
                    tableBody.innerHTML = '';

                    // Iterar sobre las sugerencias y agregarlas a la tabla
                    result.data.forEach(sugerencia => {
                        const row = `
                            <tr>
                                <td>${sugerencia.tipo}</td>
                                <td>${sugerencia.presentaciones}</td>
                                <td>${sugerencia.dosis}</td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });

                } else {
                    alert("Error: " + result.message);
                }
            } catch (error) {
                alert("Ocurrió un error al obtener las sugerencias: " + error.message);
            }
        });

        // Cargar los tipos de medicamentos desde el servidor
        const loadTiposMedicamentos = async () => {
            try {
            const response = await fetch('../../controllers/admedi.controller.php', {
                method: "POST",
                body: new URLSearchParams({ operation: 'listarTiposMedicamentos' })
            });
            const result = await response.json();
            const tipos = result.data;

            tipoMedicamentoSelect.innerHTML = '<option value="">Seleccione el Tipo de Medicamento</option>';
            tipos.forEach(tipo => {
                const option = document.createElement("option");
                option.value = tipo.tipo;
                option.textContent = tipo.tipo;
                tipoMedicamentoSelect.appendChild(option);
            });

            tipoMedicamentoSelect.innerHTML += '<option value="nuevo">Agregar nuevo tipo...</option>';

            // También cargar los tipos en el modal de entrada
            const tipoEntradaSelect = document.querySelector("#tipoentrada");
            tipoEntradaSelect.innerHTML = '<option value="">Seleccione el Tipo de Medicamento</option>';
            tipos.forEach(tipo => {
                const option = document.createElement("option");
                option.value = tipo.tipo;
                option.textContent = tipo.tipo;
                tipoEntradaSelect.appendChild(option);
            });

            tipoEntradaSelect.innerHTML += '<option value="nuevo">Agregar nuevo tipo...</option>';
            } catch (error) {
            mostrarMensaje("Error al cargar tipos de medicamentos: " + error.message, 'error');
            }
        };

        // Mostrar modal para agregar nuevo tipo
        tipoMedicamentoSelect.addEventListener('change', function () {
            if (this.value === 'nuevo') {
                $('#modalAgregarTipo').modal('show');
            }
        });
       
        // Mostrar modal para agregar nuevo tipo en el modal de entrada
        const tipoEntradaSelect = document.querySelector("#tipoentrada");
        tipoEntradaSelect.addEventListener('change', function () {
            if (this.value === 'nuevo') {
            $('#modalAgregarTipo').modal('show');
            }
        });

        // Procesar la adición de un nuevo tipo de medicamento
        formAgregarTipo.addEventListener('submit', async (event) => {
            event.preventDefault();
            const nuevoTipoMedicamento = document.querySelector("#nuevoTipoMedicamento").value;

            try {
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({ operation: 'agregarTipoMedicamento', tipo: nuevoTipoMedicamento })
                });

                const result = await response.json();
                if (result.status === "success") {
                    mostrarMensaje(result.message, 'success');
                    showToast(result.message, 'SUCCESS');
                    $('#modalAgregarTipo').modal('hide');
                    loadTiposMedicamentos();
                } else {
                    mostrarMensaje(result.message, 'error');
                    showToast(result.message, 'ERROR');
                }
            } catch (error) {
                mostrarMensaje("Error al agregar tipo de medicamento: " + error.message, 'error');
                showToast("Error al agregar tipo de medicamento", 'ERROR');
            }
        });

        // Cargar presentaciones de medicamentos desde el servidor
        const loadPresentaciones = async () => {
            try {
            const response = await fetch('../../controllers/admedi.controller.php', {
                method: "POST",
                body: new URLSearchParams({ operation: 'listarPresentacionesMedicamentos' })
            });

            const result = await response.json();
            const presentaciones = result.data;

            // Limpiar y agregar opciones al select de presentaciones
            presentacionSelect.innerHTML = '<option value="">Seleccione la Presentación</option>';
            presentaciones.forEach(presentacion => {
                const option = document.createElement("option");
                option.value = presentacion.presentacion;
                option.textContent = presentacion.presentacion;
                presentacionSelect.appendChild(option);
            });
            presentacionSelect.innerHTML += '<option value="nuevo">Agregar nueva presentación...</option>';

            // También cargar las presentaciones en el modal de entrada
            const presentacionEntradaSelect = document.querySelector("#entradaPresentacion");
            presentacionEntradaSelect.innerHTML = '<option value="">Seleccione la Presentación</option>';
            presentaciones.forEach(presentacion => {
                const option = document.createElement("option");
                option.value = presentacion.presentacion;
                option.textContent = presentacion.presentacion;
                presentacionEntradaSelect.appendChild(option);
            });
            presentacionEntradaSelect.innerHTML += '<option value="nuevo">Agregar nueva presentación...</option>';

            } catch (error) {
            mostrarMensaje("Error al cargar presentaciones: " + error.message, 'error');
            showToast("Error al cargar presentaciones", 'ERROR');
            }
        };

        // Mostrar modal para agregar nueva presentación
        presentacionSelect.addEventListener('change', function () {
            if (this.value === 'nuevo') {
            $('#modalAgregarPresentacion').modal('show');
            }
        });

        // Mostrar modal para agregar nueva presentación en el modal de entrada
        const presentacionEntradaSelect = document.querySelector("#entradaPresentacion");
        presentacionEntradaSelect.addEventListener('change', function () {
            if (this.value === 'nuevo') {
            $('#modalAgregarPresentacion').modal('show');
            }
        });

        // Procesar la adición de una nueva presentación de medicamento
        formAgregarPresentacion.addEventListener('submit', async (event) => {
            event.preventDefault();
            const nuevaPresentacion = document.querySelector("#nuevaPresentacion").value;

            try {
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({ operation: 'agregarPresentacion', presentacion: nuevaPresentacion })
                });

                const result = await response.json();
                if (result.status === "success") {
                    mostrarMensaje(result.message, 'success');
                    showToast(result.message, 'SUCCESS');
                    $('#modalAgregarPresentacion').modal('hide');
                    loadPresentaciones();
                } else {
                    mostrarMensaje(result.message, 'error');
                    showToast(result.message, 'ERROR');
                }
            } catch (error) {
                mostrarMensaje("Error al agregar presentación: " + error.message, 'error');
                showToast("Error al agregar presentación", 'ERROR');
            }
        });

        // Cargar medicamentos en los selectores de entrada y salida
        const loadSelectMedicamentos = async () => {
            try {
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({ operation: 'getAllMedicamentos' })
                });
                const textResponse = await response.text();
                const result = JSON.parse(textResponse);
                const medicamentos = result.data;

                document.querySelector("#entradaMedicamento").innerHTML = '<option value="">Seleccione un Medicamento</option>';
                document.querySelector("#salidaMedicamento").innerHTML = '<option value="">Seleccione un Medicamento</option>';

                medicamentos.forEach(med => {
                    const optionEntrada = document.createElement("option");
                    optionEntrada.value = med.nombreMedicamento;
                    optionEntrada.textContent = med.nombreMedicamento;

                    const optionSalida = document.createElement("option");
                    optionSalida.value = med.nombreMedicamento;
                    optionSalida.textContent = med.nombreMedicamento;

                    document.querySelector("#entradaMedicamento").appendChild(optionEntrada);
                    document.querySelector("#salidaMedicamento").appendChild(optionSalida);
                });

                // **Notificaciones sobre stock bajo y agotado**
                const stockBajo = medicamentos.filter(med => med.cantidad_stock < med.stockMinimo && med.cantidad_stock > 0);
                const agotados = medicamentos.filter(med => med.cantidad_stock <= 0);

                const mostrarNotificacionesSecuenciales = async () => {
                    let delay = 5000; // Intervalo entre cada notificación (en milisegundos)

                    // Mostrar notificaciones de stock bajo primero
                    if (stockBajo.length > 0) {
                        for (const med of stockBajo) {
                            showToast(`Stock bajo: ${med.nombreMedicamento} (Lote: ${med.lote}, Stock: ${med.cantidad_stock})`, "WARNING");
                            await new Promise(resolve => setTimeout(resolve, delay));  // Espera antes de mostrar la siguiente notificación
                        }
                    } else {
                        console.log("No hay medicamentos con stock bajo.");
                    }

                    // Mostrar notificaciones de medicamentos agotados después de stock bajo
                    if (agotados.length > 0) {
                        for (const med of agotados) {
                            showToast(`Agotado: ${med.nombreMedicamento} (Lote: ${med.lote})`, "ERROR");
                            await new Promise(resolve => setTimeout(resolve, delay));  // Espera antes de mostrar la siguiente notificación
                        }
                    } else {
                        console.log("No hay medicamentos agotados.");
                    }
                };

                // Ejecutar automáticamente las notificaciones al cargar la página
                mostrarNotificacionesSecuenciales();

            } catch (error) {
                mostrarMensaje("Error al cargar medicamentos: " + error.message, 'error');
                showToast("Error al cargar medicamentos", 'ERROR');
            }
        };

        // Cargar lista de medicamentos en la tabla
        const loadMedicamentos = async () => {
            try {
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({ operation: 'getAllMedicamentos' })
                });
                const textResponse = await response.text();
                if (textResponse.startsWith("<")) {
                    mostrarMensaje("Error en la respuesta del servidor.", 'error');
                    showToast("Error en la respuesta del servidor", 'ERROR');
                    return;
                }

                const result = JSON.parse(textResponse);
                const medicamentos = result.data;

                if ($.fn.dataTable.isDataTable('#medicamentosTable')) {
                    $('#medicamentosTable').DataTable().destroy();
                }

                $('#medicamentosTable').DataTable({
                    data: medicamentos,
                    columns: [
                        { data: 'nombreMedicamento', title: 'Nombre' },
                        { data: 'lote', title: 'Lote' },
                        { data: 'presentacion', title: 'Presentación', defaultContent: 'N/A' },
                        { data: 'dosis', title: 'Dosis', defaultContent: 'N/A' },
                        { data: 'nombreTipo', title: 'Tipo', defaultContent: 'N/A' },
                        { data: 'fecha_caducidad', title: 'Fecha Caducidad', defaultContent: 'N/A' },
                        { data: 'cantidad_stock', title: 'Cantidad Stock', defaultContent: 'N/A' },
                        { data: 'fecha_registro', title: 'Fecha Registro', defaultContent: 'N/A' },
                        { data: 'estado', title: 'Estado', defaultContent: 'N/A' },
                        {
                            data: null,
                            title: 'Acciones',
                            render: function(data, type, row) {
                                return `<button class="btn btn-danger btn-sm" onclick="borrarMedicamento('${row.idMedicamento}')">
                                            <i class="fas fa-trash"></i> Borrar
                                        </button>`;
                            },
                            orderable: false
                        }
                    ],
                    pageLength: 9,
                    destroy: true,
                    autoWidth: false,
                    responsive: true,
                    scrollX: true,  // Para asegurar que la tabla ocupe el 100% del ancho
                    language: {
                        url: '/haras/data/es_es.json'
                    },
                    createdRow: function(row, data, dataIndex) {
                        // Estilos personalizados para filas
                        $(row).css({
                            'font-weight': 'bold',
                            'color': '#333',
                            'background-color': (dataIndex % 2 === 0) ? '#f0f0f5' : '#ffffff',
                            'border-bottom': '2px solid #0077b6'
                        });
                    },
                    initComplete: function() {
                        // Ajustar tabla para que se adapte al contenedor sin encogerse
                        $('#medicamentosTable').css({
                            'border-collapse': 'collapse',
                            'width': '100%',  // Ocupa todo el ancho
                            'font-size': '16px',
                            'table-layout': 'fixed'  // Evita que las columnas se encojan
                        });

                        $('th').css({
                            'width': 'auto',  // Ajusta la anchura del encabezado
                            'white-space': 'nowrap',  // Evita que el texto se ajuste en múltiples líneas
                            'text-align': 'center'
                        });

                        $('td').css({
                            'padding': '8px',
                            'text-align': 'center'
                        });

                        // Estilos adicionales para el botón "Borrar"
                        $('.btn-outline-danger').css({
                            'border': '1px solid #dc3545',
                            'font-weight': 'bold',
                            'color': '#dc3545'
                        });
                    }
                });

                // Mostrar notificaciones de stock bajo y agotado solo al cargar la página
                if (!notificacionesMostradas) {
                    const stockBajo = medicamentos.filter(med => med.cantidad_stock < med.stockMinimo && med.cantidad_stock > 0);
                    const agotados = medicamentos.filter(med => med.cantidad_stock <= 0);

                    if (stockBajo.length > 0) {
                        stockBajo.forEach(med => {
                            showToast(`Stock bajo: ${med.nombreMedicamento} (Lote: ${med.lote})`, "WARNING");
                        });
                    }

                    if (agotados.length > 0) {
                        agotados.forEach(med => {
                            showToast(`Agotado: ${med.nombreMedicamento} (Lote: ${med.lote})`, "ERROR");
                        });
                    }

                    notificacionesMostradas = true;  // Asegurar que las notificaciones solo se muestren una vez
                }

            } catch (error) {
                mostrarMensaje("Error al cargar medicamentos: " + error.message, 'error');
                showToast("Error al cargar medicamentos", 'ERROR');
            }
        };
       // Función para confirmar la eliminación del medicamento
        window.borrarMedicamento = async (idMedicamento, nombreMedicamento) => {
            const confirmacion = await ask(`¿Estás seguro de que deseas eliminar el medicamento "${nombreMedicamento}"?`);
            if (confirmacion) {
                try {
                    const response = await fetch('../../controllers/admedi.controller.php', {
                        method: "POST",
                        body: new URLSearchParams({
                            operation: 'borrarMedicamento',
                            idMedicamento: idMedicamento
                        })
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        showToast("Medicamento eliminado correctamente.", "SUCCESS");
                        loadMedicamentos();  // Recargar los medicamentos después de eliminar
                    } else {
                        showToast(result.message, "ERROR");
                    }
                } catch (error) {
                    showToast("Error al intentar eliminar el medicamento: " + error.message, "ERROR");
                }
            }
        };

        // **Nuevo - Validar combinaciones antes de registrar un medicamento**
        const validarCombinacion = async (params) => {
            try {
                // Log para ver los parámetros que se van a enviar
                console.log('Preparando los datos para validar combinación:', params);

                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({
                        operation: 'validarRegistrarCombinacion',
                        tipo: params.tipo,
                        presentacion: params.presentacion,
                        dosis: params.dosis,
                    })
                });

                // Verifica si hubo un error en la solicitud antes de procesar el JSON
                console.log('Estado de la respuesta HTTP:', response.status, response.statusText);

                if (!response.ok) {
                    console.log('Error en la solicitud: No se pudo conectar correctamente con el servidor.');
                    mostrarMensaje("Error al conectar con el servidor. Estado: " + response.status, 'error');
                    showToast("Error al conectar con el servidor", 'ERROR');
                    return false;
                }

                // Log para ver la respuesta completa del servidor
                const result = await response.json();
                console.log('Respuesta completa recibida del servidor:', result);

                // Validación exitosa
                if (result.status === "success") {
                    console.log('Validación exitosa: Combinación válida. Detalles:', result.data || 'Sin detalles adicionales.');
                    return true;  // Combinación válida
                } else {
                    // Si la validación falla
                    console.log('Error en la validación. Mensaje recibido del servidor:', result.message);
                    mostrarMensaje(result.message, 'error');
                    showToast(result.message, 'ERROR');
                    return false;
                }
            } catch (error) {
                // Log de errores en la solicitud
                console.log('Excepción capturada durante la validación:', error.message);
                console.log('Detalles de la excepción:', error);
                mostrarMensaje("Error al validar combinación: " + error.message, 'error');
                showToast("Error al validar combinación", 'ERROR');
                return false;
            }
        };



        // **Validar campos y mostrar mensajes específicos**
        const validarCampos = (formData) => {
            const errors = [];

            if (!formData.get('nombreMedicamento')) {
                errors.push('El nombre del medicamento es obligatorio.');
            }

            if (!formData.get('lote')) {
                errors.push('El lote es obligatorio.');
            }

            if (!formData.get('tipo')) {
                errors.push('El tipo de medicamento es obligatorio.');
            }

            if (!formData.get('presentacion')) {
                errors.push('La presentación es obligatoria.');
            }

            if (!formData.get('fechaCaducidad')) {
                errors.push('La fecha de caducidad es obligatoria.');
            }

            if (!formData.get('dosis')) {
                errors.push('La dosis es obligatoria.');
            }

            if (!formData.get('cantidad_stock') || formData.get('cantidad_stock') <= 0) {
                errors.push('La cantidad de stock debe ser mayor a 0.');
            }

            return errors;
        };

        // Registrar medicamento
        formRegistrarMedicamento.addEventListener("submit", async (event) => {
            event.preventDefault();

            // Validar el campo lote primero
            const loteInput = document.querySelector('#lote');
            console.log('Campo Lote ingresado:', loteInput.value);  // Muestra el valor del campo de lote
            const loteValido = await validarLote(loteInput);
            console.log('Resultado de la validación del lote:', loteValido);  // Verifica si el lote es válido

            if (!loteValido) {
                console.log('Lote inválido. Se detiene el registro.');  // Log para indicar que el lote es inválido
                return;  // Detener la operación si el lote no es válido
            }

            // Confirmar con ask antes de registrar el medicamento
            const confirmar = await ask("¿Estás seguro de que deseas registrar este medicamento?", "Registrar Medicamento");
            console.log('Confirmación del usuario para proceder con el registro:', confirmar);  // Verifica si el usuario confirma

            if (!confirmar) {
                console.log('Operación cancelada por el usuario.');  // Log cuando el usuario cancela la operación
                showToast("Operación cancelada.", "INFO");
                return; // Detener la operación si se cancela
            }

            const formData = new FormData(formRegistrarMedicamento);
            console.log('Datos del formulario antes de validación:', [...formData]);  // Log de todos los datos capturados del formulario

            const errores = validarCampos(formData);
            console.log('Errores de validación detectados:', errores);  // Muestra cualquier error de validación

            if (errores.length > 0) {
                console.log('Errores encontrados, deteniendo el registro.');  // Log para indicar que se encontraron errores
                mostrarMensaje(errores.join(' '), 'error');
                return; // Detener el registro hasta que se corrijan los errores
            }

            const tipo = tipoMedicamentoSelect.value;
            const presentacion = presentacionSelect.value;
            const dosis = document.querySelector("#dosis").value;
            console.log('Datos ingresados para validar combinación:', { tipo, presentacion, dosis });  // Muestra los datos que se están validando

            // Validar la combinación de tipo, presentación y dosis
            const esValido = await validarCombinacion({ tipo, presentacion, dosis });
            console.log('Resultado de la validación de combinación:', esValido);  // Muestra si la combinación es válida

            if (!esValido) {
                console.log('Combinación inválida. Se detiene el registro.');  // Log cuando la combinación no es válida
                return;  // Detener la operación si la combinación no es válida
            }

            // Proceder con el registro del medicamento si no hay errores
            const data = new URLSearchParams(formData);
            data.append('operation', 'registrar');
            console.log('Datos enviados al servidor:', [...data]);  // Muestra los datos enviados al servidor en formato de array

            try {
                console.log('Enviando solicitud de registro al servidor...');
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: data
                });

                console.log('Estado de la respuesta HTTP:', response.status, response.statusText);  // Verifica si la solicitud fue exitosa

                if (!response.ok) {
                    console.log('Error en la solicitud al servidor. Estado:', response.status);  // Log para errores de conexión
                    mostrarMensaje("Error al conectar con el servidor. Estado: " + response.status, 'error');
                    showToast("Error al conectar con el servidor", 'ERROR');
                    return;
                }

                const result = await response.json();
                console.log('Respuesta del servidor:', result);  // Muestra la respuesta que devuelve el servidor

                if (result.status === "success") {
                    console.log('Medicamento registrado correctamente.');  // Log cuando el medicamento se registra con éxito
                    mostrarMensaje(result.message, 'success');
                    showToast("Medicamento registrado correctamente", "SUCCESS");
                    formRegistrarMedicamento.reset();
                    loadMedicamentos();  // Recargar la lista de medicamentos
                } else {
                    console.log('Error en el registro del medicamento:', result.message);  // Log para cualquier error del servidor
                    mostrarMensaje("Error en el registro: " + result.message, 'error');
                    showToast("Error en el registro", "ERROR");
                }
            } catch (error) {
                console.error('Error en la solicitud de registro:', error);  // Log cuando ocurre un error en la solicitud
                mostrarMensaje("Error en la solicitud de registro: " + error.message, 'error');
                showToast("Error en la solicitud de registro", "ERROR");
            }
        });




        // Implementar para la entrada de medicamentos
        formEntrada.addEventListener("submit", async (event) => {
            event.preventDefault();

            const confirmar = await ask("¿Estás seguro de que deseas registrar la entrada de este medicamento?", "Registrar Entrada de Medicamento");

            if (!confirmar) {
                showToast("Operación cancelada.", "INFO");
                return;
            }

            const formData = new FormData(formEntrada);
            const data = new URLSearchParams(formData);
            data.append('operation', 'entrada');

            try {
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: data
                });
                const result = await response.json();

                if (result.status === "success") {
                    mostrarMensaje(result.message, 'success');
                    showToast("Entrada registrada correctamente", "SUCCESS");
                    formEntrada.reset();
                    loadMedicamentos();
                } else {
                    mostrarMensaje("Error en el registro de entrada: " + result.message, 'error');
                    showToast("Error en el registro de entrada", "ERROR");
                }
            } catch (error) {
                mostrarMensaje("Error en la solicitud de registro de entrada: " + error.message, 'error');
                showToast("Error en la solicitud de registro de entrada", 'ERROR');
            }
        });

        // Implementar para la salida de medicamentos
        formSalida.addEventListener("submit", async (event) => {
            event.preventDefault();

            const confirmar = await ask("¿Estás seguro de que deseas registrar la salida de este medicamento?", "Registrar Salida de Medicamento");

            if (!confirmar) {
                showToast("Operación cancelada.", "INFO");
                return;
            }

            const formData = new FormData(formSalida);
            const data = new URLSearchParams(formData);
            data.append('operation', 'salida');

            try {
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: data
                });
                const result = await response.json();

                if (result.status === "success") {
                    mostrarMensaje(result.message, 'success');
                    showToast("Salida registrada correctamente", "SUCCESS");
                    formSalida.reset();
                    loadMedicamentos();
                } else {
                    mostrarMensaje("Error en el registro de salida: " + result.message, 'error');
                    showToast("Error en el registro de salida", "ERROR");
                }
            } catch (error) {
                mostrarMensaje("Error en la solicitud de registro de salida: " + error.message, 'error');
                showToast("Error en la solicitud de registro de salida", "ERROR");
            }
        });

        // Cargar datos al iniciar la página
        loadSelectMedicamentos();
        loadMedicamentos();
        loadTiposMedicamentos();
        loadPresentaciones();
    });
</script>

