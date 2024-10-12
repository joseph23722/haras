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

                    <!-- Dosis -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="dosis" id="dosis" class="form-control" required pattern="^[0-9]+(mg|g|ml|kg)$" title="Formato válido: número seguido de mg, g, ml, o kg">
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
                            <input type="number" name="cantidad_stock" id="cantidad_stock" class="form-control" required>
                            <label for="cantidad_stock">
                                <i class="fas fa-balance-scale" style="color: #0096c7;"></i> Cantidad en Stock
                            </label>
                        </div>
                    </div>

                    <!-- Stock Mínimo -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="stockMinimo" id="stockMinimo" class="form-control" required>
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
                            <input type="number" step="0.01" name="precioUnitario" id="precioUnitario" class="form-control" required>
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
        <div class="card-body" style="background-color: #f9f9f9;">
            <table id="medicamentosTable" class="table table-striped table-hover table-bordered">
                <thead style="background-color: #caf0f8; color: #003366;">
                    <tr>
                        <th>Nombre</th>
                        <th>Lote</th>
                        <th>Presentación</th>
                        <th>Dosis</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Fecha Caducidad</th>
                        <th>Cantidad Stock</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody id="medicamentos-table"></tbody>
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
                            <input type="text" name="presentacion" id="entradaPresentacion" class="form-control" required>
                        </div>
                        <!-- Dosis -->
                        <div class="form-group mb-3">
                            <label for="entradaDosis" class="form-label">Composición</label>
                            <input type="text" name="dosis" id="entradaDosis" class="form-control" required>
                        </div>
                        <!-- Tipo de Medicamento -->
                        <div class="form-group mb-3">
                            <label for="entradaTipo" class="form-label">Tipo</label>
                            <input type="text" name="tipo" id="entradaTipo" class="form-control" required>
                        </div>
                        <!-- Lote -->
                        <div class="form-group mb-3">
                            <label for="entradaLote" class="form-label">Lote</label>
                            <input type="text" name="lote" id="entradaLote" class="form-control" required>
                        </div>
                        <!-- Cantidad -->
                        <div class="form-group mb-3">
                            <label for="entradaCantidad" class="form-label">Cantidad</label>
                            <input type="number" name="cantidad" id="entradaCantidad" class="form-control" required>
                        </div>
                        <!-- Fecha de Caducidad -->
                        <div class="form-group mb-3">
                            <label for="entradaFechaCaducidad" class="form-label">Fecha de Caducidad</label>
                            <input type="date" name="fechaCaducidad" id="entradaFechaCaducidad" class="form-control" required min="<?= date('Y-m-d'); ?>">>
                        </div>
                        <!-- Precio Unitario -->
                        <div class="form-group mb-3">
                            <label for="entradaPrecio" class="form-label">Precio Unitario</label>
                            <input type="number" step="0.01" name="nuevoPrecio" id="entradaPrecio" class="form-control" required>
                        </div>
                        <!-- Stock Mínimo -->
                        <div class="form-group mb-3">
                            <label for="entradaStockMinimo" class="form-label">Stock Mínimo</label>
                            <input type="number" name="stockMinimo" id="entradaStockMinimo" class="form-control" required>
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
                        <div class="form-group mb-3">
                            <label for="salidaCantidad" class="form-label">Cantidad</label>
                            <input type="number" name="cantidad" id="salidaCantidad" class="form-control" required>
                        </div>
                        <!-- Botón para registrar la salida -->
                        <button type="submit" class="btn btn-danger">Registrar Salida</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Incluye SweetAlert -->

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const formRegistrarMedicamento = document.querySelector("#form-registrar-medicamento");
        const formEntrada = document.querySelector("#formEntrada");
        const formSalida = document.querySelector("#formSalida");
        const medicamentosTable = document.querySelector("#medicamentos-table");
        const tipoMedicamentoSelect = document.querySelector("#tipo");
        const formAgregarTipo = document.querySelector("#formAgregarTipo");
        const presentacionSelect = document.querySelector("#presentacion");
        const formAgregarPresentacion = document.querySelector("#formAgregarPresentacion");

        const messageArea = document.getElementById("message-area");

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

        // Función ask para confirmar acciones
        async function ask(pregunta = ``, modulo = `Haras Rancho Sur`){
          const respuesta = await Swal.fire({
            title: pregunta,
            text: modulo,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3498db',
            footer: 'Haras Rancho Sur App Ver. 1.0'
          });

          return respuesta.isConfirmed;
        }

        // Función showToast para mostrar notificaciones
        function showToast(message = ``, type = `INFO`, duration = 2500, url = null){
          const bgColor = {
            'INFO'    : '#22a6b3',
            'WARNING' : '#f9ca24',
            'SUCCESS' : '#6ab04c',
            'ERROR'   : '#eb4d4b'
          };

          Swal.fire({
            toast: true,
            icon: type.toLowerCase(),
            iconColor: 'white',
            color: 'white',
            text: message,
            timer: duration,
            timerProgressBar: true,
            position: 'top-end',
            showConfirmButton: false,
            background: bgColor[type]
          }).then(() => {
            if (url != null){
              window.location.href = url;
            }
          });
        }

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

                presentacionSelect.innerHTML = '<option value="">Seleccione la Presentación</option>';
                presentaciones.forEach(presentacion => {
                    const option = document.createElement("option");
                    option.value = presentacion.presentacion;
                    option.textContent = presentacion.presentacion;
                    presentacionSelect.appendChild(option);
                });

                presentacionSelect.innerHTML += '<option value="nueva">Agregar nueva presentación...</option>';
            } catch (error) {
                mostrarMensaje("Error al cargar presentaciones: " + error.message, 'error');
                showToast("Error al cargar presentaciones", 'ERROR');
            }
        };

        // Mostrar modal para agregar nueva presentación
        presentacionSelect.addEventListener('change', function () {
            if (this.value === 'nueva') {
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

                // **Nueva sección para notificar sobre medicamentos con stock bajo**
                const stockBajo = medicamentos.filter(med => med.cantidad_stock < med.stockMinimo);
                if (stockBajo.length > 0) {
                    stockBajo.forEach(med => {
                        showToast(`Stock bajo: ${med.nombreMedicamento} (Lote: ${med.lote})`, "WARNING");
                    });
                }

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
                        { data: 'nombreMedicamento' },
                        { data: 'lote' },
                        { data: 'presentacion', defaultContent: 'N/A' },
                        { data: 'dosis', defaultContent: 'N/A' },
                        { data: 'tipoMedicamento', defaultContent: 'N/A' },
                        { data: 'descripcion', defaultContent: 'N/A' },
                        { data: 'fecha_caducidad', defaultContent: 'N/A' },
                        { data: 'cantidad_stock', defaultContent: 'N/A' },
                        { data: 'estado', defaultContent: 'N/A' },
                    ],
                    pageLength: 9,
                    destroy: true,
                    language: {
                        url: '/haras/data/es_es.json'
                    }
                });

                // **Nueva sección para notificar sobre medicamentos con stock bajo**
                const stockBajo = medicamentos.filter(med => med.cantidad_stock < med.stockMinimo);
                if (stockBajo.length > 0) {
                    stockBajo.forEach(med => {
                        showToast(`Stock bajo: ${med.nombreMedicamento} (Lote: ${med.lote})`, "WARNING");
                    });
                }

            } catch (error) {
                mostrarMensaje("Error al cargar medicamentos: " + error.message, 'error');
                showToast("Error al cargar medicamentos", 'ERROR');
            }
        };

        // Registrar medicamento
        formRegistrarMedicamento.addEventListener("submit", async (event) => {
            event.preventDefault();

            // Confirmar con ask antes de registrar el medicamento
            const confirmar = await ask("¿Estás seguro de que deseas registrar este medicamento?", "Registrar Medicamento");

            if (!confirmar) {
                showToast("Operación cancelada.", "INFO");
                return; // Detener la operación si se cancela
            }

            const formData = new FormData(formRegistrarMedicamento);
            const data = new URLSearchParams(formData);
            data.append('operation', 'registrar');

            try {
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: data
                });
                const result = await response.json();

                if (result.status === "success") {
                    mostrarMensaje(result.message, 'success');
                    showToast("Medicamento registrado correctamente", "SUCCESS");
                    formRegistrarMedicamento.reset();
                    loadMedicamentos();
                } else {
                    mostrarMensaje("Error en el registro: " + result.message, 'error');
                    showToast("Error en el registro", "ERROR");
                }
            } catch (error) {
                mostrarMensaje("Error en la solicitud de registro: " + error.message, 'error');
                showToast("Error en la solicitud de registro", "ERROR");
            }
        });

        // **Implementar para la entrada de medicamentos**
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
                showToast("Error en la solicitud de registro de entrada", "ERROR");
            }
        });

        // **Implementar para la salida de medicamentos**
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
