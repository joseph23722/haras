<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
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

                    <!-- Descripción del Medicamento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="descripcion" id="descripcion" class="form-control">
                            <label for="descripcion"><i class="fas fa-info-circle" style="color: #6d6875;"></i> Descripción</label>
                        </div>
                    </div>

                    <!-- Lote del Medicamento (con prefijo "LOTE-") -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="lote" id="lote" class="form-control" value="LOTE-"  required>
                            <label for="lote"><i class="fas fa-box" style="color: #6d6875;"></i> Lote del Medicamento (LOTE-)</label>
                        </div>
                    </div>

                    <!-- Presentación -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="presentacion" id="presentacion" class="form-control" required>
                            <label for="presentacion"><i class="fas fa-prescription-bottle-alt" style="color: #8e44ad;"></i> Presentación</label>
                        </div>
                    </div>

                    <!-- Dosis (Validación numérica) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="dosis" id="dosis" class="form-control" required pattern="^[0-9]+(mg|g|ml|kg)$" title="Formato válido: número seguido de mg, g, ml, o kg">
                            <label for="dosis"><i class="fas fa-weight" style="color: #0096c7;"></i> Dosis (ej. 500 mg)</label>
                        </div>
                    </div>

                    <!-- Tipo de Medicamento (Menú Desplegable con Opción de Agregar) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="tipo" id="tipo" class="form-select" required>
                                <option value="">Seleccione el Tipo de Medicamento</option>
                                <option value="nuevo">Agregar nuevo tipo...</option>
                            </select>
                            <label for="tipo"><i class="fas fa-pills" style="color: #ff6b6b;"></i> Tipo de Medicamento</label>
                        </div>
                    </div>


                    <!-- Cantidad en Stock -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="cantidad_stock" id="cantidad_stock" class="form-control" required>
                            <label for="cantidad_stock"><i class="fas fa-balance-scale" style="color: #0096c7;"></i> Cantidad en Stock</label>
                        </div>
                    </div>

                    <!-- Stock Mínimo -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="stockMinimo" id="stockMinimo" class="form-control" required>
                            <label for="stockMinimo"><i class="fas fa-battery-quarter" style="color: #ff0000;"></i> Stock Mínimo</label>
                        </div>
                    </div>

                    <!-- Fecha de Caducidad (Con Validación de Fecha Futura) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fechaCaducidad" id="fechaCaducidad" class="form-control" required min="<?= date('Y-m-d'); ?>">
                            <label for="fechaCaducidad"><i class="fas fa-calendar-alt" style="color: #ba55d3;"></i> Fecha de Caducidad</label>
                        </div>
                    </div>

                    <!-- Precio Unitario -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" step="0.01" name="precioUnitario" id="precioUnitario" class="form-control" required>
                            <label for="precioUnitario"><i class="fas fa-dollar-sign" style="color: #0077b6;"></i> Precio Unitario</label>
                        </div>
                    </div>

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

    <!-- Botones para Entrada y Salida -->
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
                        <th>dosis</th>
                        <th>tipo</th>
                        <th>descripcion</th>
                        <th>fecha_caducidad</th>
                        <th>cantidad_stock</th>
                        <th>estado</th>
                    </tr>
                </thead>
                <tbody id="medicamentos-table">
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para Agregar un Nuevo Tipo de Medicamento -->
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
                        <!-- Medicamento -->
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
                            <label for="entradaDosis" class="form-label">Dosis</label>
                            <input type="text" name="dosis" id="entradaDosis" class="form-control" required>
                        </div>
                        
                        <!-- Tipo de medicamento -->
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
                            <input type="date" name="fechaCaducidad" id="entradaFechaCaducidad" class="form-control" required>
                        </div>
                        
                        <!-- Precio Unitario -->
                        <div class="form-group mb-3">
                            <label for="entradaPrecio" class="form-label">Precio Unitario</label>
                            <input type="number" step="0.01" name="nuevoPrecio" id="entradaPrecio" class="form-control" required>
                        </div>
                        
                        <!-- Stock mínimo -->
                        <div class="form-group mb-3">
                            <label for="entradaStockMinimo" class="form-label">Stock Mínimo</label>
                            <input type="number" name="stockMinimo" id="entradaStockMinimo" class="form-control" required>
                        </div>

                        <!-- Botón para enviar el formulario -->
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
                        <button type="submit" class="btn btn-danger">Registrar Salida</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once '../../footer.php'; ?>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const formRegistrarMedicamento = document.querySelector("#form-registrar-medicamento");
        const formEntrada = document.querySelector("#formEntrada");
        const formSalida = document.querySelector("#formSalida");
        const medicamentosTable = document.querySelector("#medicamentos-table");
        const tipoMedicamentoSelect = document.querySelector("#tipo");
        const formAgregarTipo = document.querySelector("#formAgregarTipo");

        console.log("Log 1: Scripts cargados correctamente.");

        // Cargar los tipos de medicamentos desde el servidor
        const loadTiposMedicamentos = async () => {
            try {
                console.log("Log 2: Cargando tipos de medicamentos...");
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({ operation: 'listarTiposMedicamentos' })
                });
                const result = await response.json();
                const tipos = result.data;

                console.log("Log 3: Tipos de medicamentos recibidos:", tipos);
                tipoMedicamentoSelect.innerHTML = '<option value="">Seleccione el Tipo de Medicamento</option>';
                tipos.forEach(tipo => {
                    const option = document.createElement("option");
                    option.value = tipo.tipo;
                    option.textContent = tipo.tipo;
                    tipoMedicamentoSelect.appendChild(option);
                });

                tipoMedicamentoSelect.innerHTML += '<option value="nuevo">Agregar nuevo tipo...</option>';
                console.log("Log 4: Tipos de medicamentos cargados en el selector.");
            } catch (error) {
                console.error("Log 5: Error al cargar tipos de medicamentos:", error.message);
                alert("Error en la solicitud: " + error.message);
            }
        };

        // Mostrar modal para agregar nuevo tipo
        tipoMedicamentoSelect.addEventListener('change', function () {
            if (this.value === 'nuevo') {
                console.log("Log 6: Usuario seleccionó agregar nuevo tipo.");
                $('#modalAgregarTipo').modal('show');
            }
        });

        // Procesar la adición de un nuevo tipo de medicamento
        formAgregarTipo.addEventListener('submit', async (event) => {
            event.preventDefault();
            const nuevoTipoMedicamento = document.querySelector("#nuevoTipoMedicamento").value;
            console.log("Log 7: Nuevo tipo de medicamento enviado:", nuevoTipoMedicamento);

            try {
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({ operation: 'agregarTipoMedicamento', tipo: nuevoTipoMedicamento })
                });

                const result = await response.json();
                console.log("Log 8: Resultado de agregar tipo de medicamento:", result);
                if (result.status === "success") {
                    alert(result.message);
                    $('#modalAgregarTipo').modal('hide');
                    loadTiposMedicamentos();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error("Log 9: Error al agregar tipo de medicamento:", error.message);
                alert("Error en la solicitud: " + error.message);
            }
        });

        // Cargar medicamentos en los selectores de entrada y salida
        const loadSelectMedicamentos = async () => {
            try {
                console.log("Log 10: Cargando medicamentos en selectores...");
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({ operation: 'getAllMedicamentos' })
                });
                const textResponse = await response.text();
                console.log("Log 11: Texto de respuesta recibido:", textResponse);
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

                console.log("Log 12: Medicamentos cargados en selectores.");
            } catch (error) {
                console.error("Log 13: Error al cargar medicamentos en selectores:", error.message);
                alert("Error en la solicitud: " + error.message);
            }
        };

        const loadMedicamentos = async () => {
            try {
                console.log("Log 14: Cargando lista de medicamentos...");
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({ operation: 'getAllMedicamentos' })
                });
                const textResponse = await response.text();
                if (textResponse.startsWith("<")) {
                    console.error("Log 15: Error en la respuesta del servidor, se recibió HTML.");
                    alert("Error en la respuesta del servidor.");
                    return;
                }

                const result = JSON.parse(textResponse);
                const medicamentos = result.data;

                console.log("Log 16: Medicamentos recibidos:", medicamentos);
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

                console.log("Log 17: Medicamentos cargados en DataTable.");
            } catch (error) {
                console.error("Log 18: Error al cargar medicamentos:", error.message);
                alert("Error en la solicitud: " + error.message);
            }
        };

        // Registrar medicamento
        formRegistrarMedicamento.addEventListener("submit", async (event) => {
            event.preventDefault();
            console.log("Log 19: Enviando formulario de registro de medicamento.");

            const formData = new FormData(formRegistrarMedicamento);
            const data = new URLSearchParams(formData);
            data.append('operation', 'registrar');

            console.log("Log 20: Datos del formulario:", [...data]);

            try {
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: data
                });
                const result = await response.json();

                console.log("Log 21: Resultado del registro de medicamento:", result);

                if (result.status === "success") {
                    alert(result.message);
                    formRegistrarMedicamento.reset();
                    loadMedicamentos();
                } else {
                    alert("Error en el registro: " + result.message);
                    console.error("Log 22: Error en el registro:", result);
                }
            } catch (error) {
                console.error("Log 23: Error en la solicitud de registro:", error.message);
                alert("Error en la solicitud: " + error.message);
            }
        });

        // Cargar medicamentos al iniciar
        console.log("Log 24: Cargando medicamentos al iniciar la página.");
        loadMedicamentos();

        // Cargar tipos de medicamentos al cargar la página
        console.log("Log 25: Cargando tipos de medicamentos al iniciar la página.");
        loadTiposMedicamentos();
    });
</script>
