<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título de la página -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">
        Gestionar Medicamentos
    </h1>


    <!-- Sección del formulario para registrar un medicamento -->
    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Medicamento</h5>
            <!-- Botón de Sugerencias integrado en el header -->
            <button type="button" class="btn btn-info btn-sm"
                style=" border: none; position: absolute; right: 1px; top: 2px; padding: 10px 12px; font-size: 1.2em;"
                id="btnSugerencias"
                data-bs-toggle="modal"
                data-bs-target="#modalSugerencias">
                <i class="fas fa-lightbulb"></i>
            </button>
            <!-- Botón de Agregar en el header -->
            <button type="button" class="btn btn-success btn-sm"
                style="background-color: #28a745; border: none; position: absolute; right: 50px; top: 1px; padding: 10px 12px; font-size: 1.2em;"
                id="btnAgregar"
                data-bs-toggle="modal"
                data-bs-target="#modalAgregarTipoPresentacion">
                <i class="fas fa-plus"></i>
            </button>

        </div>
        <div class="card-body p-4" style="background-color: #f9f9f9;">

            <form action="" id="form-registrar-medicamento" autocomplete="off">
                <div class="row g-3">
                    <!-- Nombre del Medicamento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nombreMedicamento" id="nombreMedicamento" placeholder="" class="form-control" required autofocus>
                            <label for="nombreMedicamento">
                                <i class="fas fa-capsules" style="color: #00b4d8;"></i> Nombre del Medicamento
                            </label>
                        </div>
                    </div>

                    <!-- Descripción del Medicamento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="descripcion" id="descripcion" placeholder="" class="form-control">
                            <label for="descripcion">
                                <i class="fas fa-info-circle" style="color: #6d6875;"></i> Descripción
                            </label>
                        </div>
                    </div>

                    <!-- Lote del Medicamento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="lote" id="lote" class="form-control" placeholder="" required>
                            <label for="lote">
                                <i class="fas fa-box" style="color: #6d6875;"></i> Lote del Medicamento (LOTE-)
                            </label>
                        </div>
                    </div>



                    <!-- Tipo de Medicamento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="tipo" id="tipo" class="form-select" required>
                                <option value="">Seleccione el Tipo de Medicamento</option>
                            </select>
                            <label for="tipo">
                                <i class="fas fa-pills" style="color: #ff6b6b;"></i> Tipo de Medicamento
                            </label>
                        </div>
                    </div>

                    <!-- Composición (Dosis y Unidad) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="dosis" id="dosis" class="form-control" placeholder="" required
                                pattern="^\d+(\.\d+)?\s?[a-zA-Z]+$"
                                title="Ingrese la cantidad seguida de la unidad (por ejemplo: 500 mg, 10 ml)">
                            <label for="dosis">
                                <i class="fas fa-weight" style="color: #0096c7;"></i> Composición (ej. 500 mg)
                            </label>
                        </div>
                    </div>

                    <!-- Presentación -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="presentacion" id="presentacion" class="form-select" required>
                                <option value="">Seleccione la Presentación</option>
                            </select>
                            <label for="presentacion">
                                <i class="fas fa-prescription-bottle-alt" style="color: #8e44ad;"></i> Presentación
                            </label>
                        </div>
                    </div>





                    <!-- Cantidad en Stock -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="cantidad_stock" id="cantidad_stock" class="form-control" required min="0" placeholder="">
                            <label for="cantidad_stock">
                                <i class="fas fa-balance-scale" style="color: #0096c7;"></i> Cantidad en Stock
                            </label>
                        </div>
                    </div>

                    <!-- Stock Mínimo -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="stockMinimo" id="stockMinimo" class="form-control" value="10" required min="0" placeholder="">
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
                            <input type="number" step="0.01" name="precioUnitario" placeholder="" id="precioUnitario" class="form-control" required min="0">
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

                        <button class="btn btn-outline-info btn-lg" style="border-color: #17a2b8;" data-bs-toggle="modal" data-bs-target="#modalHistorial">
                            <i class="fas fa-history"></i> Ver Historial de Movimientos
                        </button>

                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Opciones de Movimiento -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
            <h5 class="text-center"><i class="fas fa-exchange-alt"></i> Opciones de Movimiento</h5>
        </div>
        <div class="card-body text-center" style="background-color: #f9f9f9;">
            <button class="btn btn-outline-primary btn-lg me-3" style="border-color: #007bff;" data-bs-toggle="modal" data-bs-target="#modalEntrada">
                <i class="fas fa-arrow-up"></i> Registrar Entrada de Medicamento
            </button>
            <button class="btn btn-outline-danger btn-lg me-3 btn-custom-single" data-bs-toggle="modal" data-bs-target="#modalSalida">
                <i class="fas fa-arrow-down"></i> Registrar Salida de Medicamento
            </button>
        </div>
    </div>

    <!-- Modal para Agregar Nueva Combinación -->
    <div class="modal fade" id="modalAgregarTipoPresentacion" tabindex="-1" aria-labelledby="modalAgregarTipoPresentacionLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #004080; color: white; padding: 15px;">
                    <h5 class="modal-title" id="modalAgregarTipoPresentacionLabel" style="font-weight: bold;">
                        <i class="fas fa-plus-circle"></i> Agregar Nueva Combinación de Medicamento
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAgregarTipoPresentacion">
                    <div class="modal-body p-4">
                        <div class="row">
                            <!-- Formulario para Agregar Tipo -->
                            <div class="col-md-4 mb-4">
                                <label for="nuevoTipoMedicamento">Tipo de Medicamento</label>
                                <input type="text" name="nuevoTipoMedicamento" id="nuevoTipoMedicamento" class="form-control" required>
                            </div>

                            <!-- Formulario para Agregar Presentación -->
                            <div class="col-md-4 mb-4">
                                <label for="nuevaPresentacion">Presentación</label>
                                <input type="text" name="nuevaPresentacion" id="nuevaPresentacion" class="form-control" required>
                            </div>

                            <!-- Formulario para Agregar Unidad de Medida -->
                            <div class="col-md-4 mb-4">
                                <label for="nuevaUnidadMedida">Unidad de Medida</label>
                                <input type="text" name="nuevaUnidadMedida" id="nuevaUnidadMedida" class="form-control" required>
                            </div>

                            <!-- Campo de Dosis Oculto -->
                            <input type="hidden" name="dosisMedicamento" id="dosisMedicamento" value="10">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Agregar Combinación</button>
                    </div>
                </form>
            </div>
        </div>
    </div>




    <!-- Modal para Registrar Entrada de Medicamento -->
    <div class="modal fade" id="modalEntrada" tabindex="-1" aria-labelledby="modalEntradaLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Encabezado del Modal -->
                <div class="modal-header" style="background-color: #4CAF50; color: white; border-top-left-radius: .3rem; border-top-right-radius: .3rem;">
                    <h5 class="modal-title" id="modalEntradaLabel">Registrar Entrada de Medicamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color: white;"></button>
                </div>

                <!-- Cuerpo del Modal -->
                <div class="modal-body px-4 py-3">
                    <form id="formEntrada">
                        <!-- Selección de Medicamento -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="entradaMedicamento" class="form-label fw-bold">Medicamento</label>
                                <select name="nombreMedicamento" id="entradaMedicamento" class="form-select form-select-lg" required>
                                    <option value="" disabled selected>Seleccione un medicamento</option>
                                    <!-- Opciones cargadas dinámicamente -->
                                </select>
                            </div>
                        </div>

                        <!-- Lote -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="entradaLote" class="form-label fw-bold">Lote</label>
                                <select name="lote" id="entradaLote" class="form-select form-select-lg" required>
                                    <option value="" disabled selected>Seleccione un Lote</option>
                                    <!-- Opciones cargadas dinámicamente -->
                                </select>
                            </div>
                        </div>

                        <!-- Cantidad -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="entradaCantidad" class="form-label fw-bold">Cantidad</label>
                                <input type="number" name="cantidad" id="entradaCantidad" class="form-control form-control-lg" required min="1" placeholder="Ingrese cantidad">
                            </div>
                        </div>

                        <!-- Botón para registrar la entrada -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success px-4">Registrar Entrada</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal para Registrar Salida de Medicamento -->
    <div class="modal fade" id="modalSalida" tabindex="-1" aria-labelledby="modalSalidaLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Encabezado del Modal -->
                <div class="modal-header" style="background-color: #5a67d8; color: white; border-top-left-radius: .3rem; border-top-right-radius: .3rem;">
                    <h5 class="modal-title" id="modalSalidaLabel">Registrar Salida de Medicamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color: white;"></button>
                </div>

                <!-- Cuerpo del Modal -->
                <div class="modal-body px-4 py-3">
                    <form id="formSalida">
                        <!-- Selección de Medicamento -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="salidaMedicamento" class="form-label fw-bold">Medicamento</label>
                                <select name="nombreMedicamento" id="salidaMedicamento" class="form-select form-select-lg" required>
                                    <option value="" disabled selected>Seleccione un medicamento</option>
                                    <!-- Opciones cargadas dinámicamente -->
                                </select>
                            </div>
                        </div>

                        <!-- Cantidad y Tipo de Equino -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="salidaCantidad" class="form-label fw-bold">Cantidad</label>
                                <input type="number" name="cantidad" id="salidaCantidad" class="form-control form-control-lg" required min="1" placeholder="Ingrese cantidad">
                            </div>


                            <div class="col-md-6">
                                <label for="idEquino" class="form-label fw-bold">Categoría de Equino</label> <!-- Etiqueta agregada para consistencia -->
                                <select id="idEquino" name="idEquino" class="form-select form-control-lg" required>
                                    <option value="">Seleccione Categoría de Equino</option>
                                    <!-- Opciones se cargarán dinámicamente -->
                                </select>
                            </div>
                        </div>

                        <!-- Lote -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="salidaLote" class="form-label fw-bold">Lote (opcional)</label>
                                <select name="lote" id="salidaLote" class="form-select form-select-lg">
                                    <option value="" disabled selected>Seleccione un lote</option>
                                    <!-- Opciones cargadas dinámicamente -->
                                </select>
                            </div>
                        </div>

                        <!-- Motivo de la salida -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="motivoSalida" class="form-label fw-bold">Motivo</label>
                                <textarea name="motivo" id="motivoSalida" class="form-control form-control-lg" required placeholder="Ingrese el motivo de la salida"></textarea>
                            </div>
                        </div>

                        <!-- Botón para registrar la salida -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger px-4">Registrar Salida</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal para Sugerencias de Medicamentos -->
    <div class="modal fade" id="modalSugerencias" tabindex="-1" aria-labelledby="modalSugerenciasLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Encabezado del Modal -->
                <div class="modal-header" style="background-color: #3498db; color: white; border-top-left-radius: .3rem; border-top-right-radius: .3rem;">
                    <h5 class="modal-title" id="modalSugerenciasLabel">Sugerencias de Medicamentos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color: white;"></button>
                </div>

                <!-- Cuerpo del Modal -->
                <div class="modal-body">
                    <table class="table table-hover table-bordered mt-3">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>Tipo de Medicamento</th>
                                <th>Presentación</th>
                                <th>Composición</th>
                                <th>Acciones</th> <!-- Nueva columna para acciones -->
                            </tr>
                        </thead>
                        <tbody id="sugerenciasTableBody">
                            <!-- Aquí se insertarán las sugerencias dinámicamente -->
                        </tbody>
                    </table>
                </div>

                <!-- Pie del Modal -->
                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Editar Sugerencia de Medicamento -->
    <div class="modal fade" id="modalEditarSugerencia" tabindex="-1" aria-labelledby="modalEditarSugerenciaLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #f39c12; color: white;">
                    <h5 class="modal-title" id="modalEditarSugerenciaLabel">Editar Sugerencia de Medicamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarSugerencia">
                        <!-- Campo oculto para el ID de la sugerencia (ID de la combinación) -->

                        <input type="hidden" id="editarId"> <!-- Aquí va el ID de la sugerencia -->
                        <div class="mb-3">
                            <label for="editarTipo" class="form-label">Tipo de Medicamento</label>
                            <input type="text" class="form-control" id="editarTipo" required>
                        </div>
                        <div class="mb-3">
                            <label for="editarPresentacion" class="form-label">Presentación</label>
                            <input type="text" class="form-control" id="editarPresentacion" required>
                        </div>
                        <div class="mb-3">
                            <label for="editarDosis" class="form-label">Dosis</label>
                            <input type="text" class="form-control" id="editarDosis" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal para Historial de Movimientos de Medicamentos -->
    <div class="modal fade" id="modalHistorial" tabindex="-1" aria-labelledby="modalHistorialLabel" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- Encabezado del Modal -->
                <div class="modal-header" style="background-color: #17a2b8; color: white; border-top-left-radius: .3rem; border-top-right-radius: .3rem;">
                    <h5 class="modal-title" id="modalHistorialLabel">Historial de Movimientos de Medicamentos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color: white;"></button>
                </div>

                <!-- Cuerpo del Modal -->
                <div class="modal-body px-4 py-3">
                    <!-- Opciones de Filtrado Rápido -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            <label for="filtroRango" class="me-2 fw-bold">Ver movimientos de:</label>
                            <select id="filtroRango" class="form-select form-select-sm">
                                <option value="hoy">Hoy</option>
                                <option value="ultimaSemana">Última semana</option>
                                <option value="ultimoMes">Último mes</option>
                                <option value="todos">Todos</option>
                            </select>
                        </div>
                        <button type="button" id="buscarHistorial" class="btn btn-primary ms-3" onclick="reloadHistorialMovimientos();"><i class="fas fa-search me-1"></i>Buscar</button>
                    </div>

                    <!-- Pestañas para Entrada y Salida -->
                    <ul class="nav nav-tabs mb-3" id="historialTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="entradas-tab" data-bs-toggle="tab" data-bs-target="#entradas" type="button" role="tab" aria-controls="entradas" aria-selected="true">Entradas</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="salidas-tab" data-bs-toggle="tab" data-bs-target="#salidas" type="button" role="tab" aria-controls="salidas" aria-selected="false">Salidas</button>
                        </li>
                    </ul>

                    <!-- Contenido de las Pestañas -->
                    <div class="tab-content">
                        <!-- Tabla de Entradas de Medicamentos -->
                        <div class="tab-pane fade show active" id="entradas" role="tabpanel" aria-labelledby="entradas-tab">
                            <div class="table-responsive">
                                <table id="tabla-entradas" class="table table-bordered table-hover table-striped">
                                    <thead class="table-primary">
                                        <tr class="text-center">
                                            <th>ID Medicamento</th>
                                            <th>Nombre Medicamento</th>
                                            <th>Stock Actual</th>
                                            <th>Lote</th>
                                            <th>Cantidad de Entrada</th>
                                            <th>Fecha de Movimiento</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                        <!-- Tabla de Salidas de Medicamentos -->
                        <div class="tab-pane fade" id="salidas" role="tabpanel" aria-labelledby="salidas-tab">
                            <div class="table-responsive">
                                <table id="tabla-salidas" class="table table-bordered table-hover table-striped">
                                    <thead class="table-danger">
                                        <tr class="text-center">
                                            <th>ID Medicamento</th>
                                            <th>Nombre Medicamento</th>
                                            <th>Lote</th>
                                            <th>Tipo de Equino</th>
                                            <th>Cantidad de Equinos</th> <!-- Este es el conteo de equinos -->
                                            <th>Cantidad de Salida</th>
                                            <th>Motivo</th>
                                            <th>Fecha de Movimiento</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pie del Modal -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Medicamentos Registrados -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
            <h5 class="text-center mb-0"><i class="fas fa-pills"></i> Medicamentos Registrados</h5>
        </div>
        <div class="card-body" style="background-color: #f9f9f9;">
            <!-- Tabla de medicamentos con botones de exportación y búsqueda integrados en la parte superior -->
            <table id="tabla-medicamentos" class="table table-striped table-hover table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Lote</th>
                        <th>Presentación</th>
                        <th>Dosis</th>
                        <th>Tipo</th>
                        <th>Fecha Caducidad</th>
                        <th>Cantidad Stock</th>
                        <th>Costo Unitario</th>
                        <th>Fecha Registro</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>
<script src="/haras/vendor/medicamento/historial-medicamento.js"></script>
<script src="/haras/vendor/medicamento/listar-medicamento.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../swalcustom.js"></script>
<script src="../../JS/administrar-medicamento.js"></script>