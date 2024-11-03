<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título de la página -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">
        Gestionar Medicamentos
    </h1>
    

    <!-- Sección del formulario para registrar un medicamento -->
    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Medicamento</h5>
            <!-- Botón de Sugerencias integrado en el header -->
            <button type="button" class="btn btn-info btn-sm" 
                    style=" border: none; position: absolute; right: 1px; top: 1px; padding: 5px 8px; font-size: 1.2em;"
                    id="btnSugerencias" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalSugerencias">
                <i class="fas fa-lightbulb"></i>
            </button>
            <!-- Botón de Agregar en el header -->
            <button type="button" class="btn btn-success btn-sm" 
                    style="background-color: #28a745; border: none; position: absolute; right: 50px; top: 1px; padding: 5px 8px; font-size: 1.2em;"
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
                            <input type="text" name="nombreMedicamento" id="nombreMedicamento" class="form-control" required autofocus>
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
                            </select>
                            <label for="presentacion">
                                <i class="fas fa-prescription-bottle-alt" style="color: #8e44ad;"></i> Presentación
                            </label>
                        </div>
                    </div>

                    <!-- Composición (Dosis y Unidad) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="dosis" id="dosis" class="form-control" required 
                                pattern="^\d+(\.\d+)?\s?[a-zA-Z]+$"
                                title="Ingrese la cantidad seguida de la unidad (por ejemplo: 500 mg, 10 ml)">
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
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
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

    <!-- Modal para Agregar Nuevo Tipo, Presentación y Unidad de Medida -->
    <div class="modal fade" id="modalAgregarTipoPresentacion" tabindex="-1" aria-labelledby="modalAgregarTipoPresentacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: #004080; color: white; padding: 15px;">
                    <h5 class="modal-title" id="modalAgregarTipoPresentacionLabel" style="font-weight: bold;">
                        <i class="fas fa-plus-circle"></i> Agregar Nuevo Tipo, Presentación y Unidad de Medida
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row text-center">
                        <!-- Formulario para Agregar Tipo -->
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm border-0 h-100" style="padding: 20px;">
                                <div class="card-body">
                                    <h6 class="text-center mb-3" style="color: #004080;">
                                        <i class="fas fa-capsules"></i> Nuevo Tipo de Medicamento
                                    </h6>
                                    <form id="formAgregarTipo">
                                        <div class="form-floating mb-3">
                                            <input type="text" name="nuevoTipoMedicamento" id="nuevoTipoMedicamento" class="form-control" placeholder="Ingrese el tipo de medicamento" required>
                                            <label for="nuevoTipoMedicamento">Tipo de Medicamento</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Agregar Tipo</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Formulario para Agregar Presentación -->
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm border-0 h-100" style="padding: 20px;">
                                <div class="card-body">
                                    <h6 class="text-center mb-3" style="color: #004080;">
                                        <i class="fas fa-pills"></i> Nueva Presentación
                                    </h6>
                                    <form id="formAgregarPresentacion">
                                        <div class="form-floating mb-3">
                                            <input type="text" name="nuevaPresentacion" id="nuevaPresentacion" class="form-control" placeholder="Ingrese la presentación" required>
                                            <label for="nuevaPresentacion">Presentación</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Agregar Presentación</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Formulario para Agregar Unidad de Medida -->
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm border-0 h-100" style="padding: 20px;">
                                <div class="card-body">
                                    <h6 class="text-center mb-3" style="color: #004080;">
                                        <i class="fas fa-ruler-combined"></i> Nueva Unidad de Medida
                                    </h6>
                                    <form id="formAgregarUnidadMedida">
                                        <div class="form-floating mb-3">
                                            <input type="text" name="nuevaUnidadMedida" id="nuevaUnidadMedida" class="form-control" placeholder="Ingrese la unidad de medida" required>
                                            <label for="nuevaUnidadMedida">Unidad de Medida</label>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Agregar Unidad</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f1f1f1;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>



    





    

    <!-- Modal para Registrar Entrada de Medicamento -->
    <div class="modal fade" id="modalEntrada" tabindex="-1" aria-labelledby="modalEntradaLabel" aria-hidden="true">
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
    <div class="modal fade" id="modalSalida" tabindex="-1" aria-labelledby="modalSalidaLabel" aria-hidden="true">
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
                                <label for="tipoEquinoMovimiento" class="form-label fw-bold">Tipo de Equino</label>
                                <select id="tipoEquinoMovimiento" name="idTipoEquino" class="form-select form-select-lg" required>
                                    <option value="" disabled selected>Seleccione Tipo de Equino</option>
                                    <!-- Opciones cargadas dinámicamente -->
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
    <div class="modal fade" id="modalSugerencias" tabindex="-1" aria-labelledby="modalSugerenciasLabel" aria-hidden="true">
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
                                <th>Dosis</th>
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
    <div class="modal fade" id="modalEditarSugerencia" tabindex="-1" aria-labelledby="modalEditarSugerenciaLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #f39c12; color: white;">
                    <h5 class="modal-title" id="modalEditarSugerenciaLabel">Editar Sugerencia de Medicamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarSugerencia">
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
                        <input type="hidden" id="editarId"> <!-- Campo oculto para el ID de la sugerencia -->
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal para Historial de Movimientos de Medicamentos -->
    <div class="modal fade" id="modalHistorial" tabindex="-1" aria-labelledby="modalHistorialLabel" aria-hidden="true">
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
                        <button type="button" id="buscarHistorial" class="btn btn-primary ms-3"><i class="fas fa-search me-1"></i>Buscar</button>
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
                                            <th>Descripción</th>
                                            <th>Stock Actual</th>
                                            <th>Cantidad de Entrada</th>
                                            <th>Fecha de Movimiento</th>
                                        </tr>
                                    </thead>
                                    <tbody id="historial-entradas-table">
                                        <!-- Los datos se cargarán dinámicamente -->
                                    </tbody>
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
                                            <th>Descripción</th>
                                            <th>Tipo de Equino</th>
                                            <th>Cantidad de Salida</th>
                                            <th>Motivo</th>
                                            <th>Fecha de Movimiento</th>
                                        </tr>
                                    </thead>
                                    <tbody id="historial-salidas-table">
                                        <!-- Los datos se cargarán dinámicamente -->
                                    </tbody>
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
        <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
            <h5 class="text-center mb-0"><i class="fas fa-pills"></i> Medicamentos Registrados</h5>
        </div>
        <div class="card-body" style="background-color: #f9f9f9;">
            <table id="medicamentosTable" class="table table-striped table-hover table-bordered" style="width:100%">
                <thead style="background-color: #caf0f8; color: #003366;">
                    <tr>
                        <th>Nombre</th>
                        <th>Lote</th>
                        <th>Presentación</th>
                        <th>Dosis</th>
                        <th>Tipo</th>
                        <th>Fecha Caducidad</th>
                        <th>Cantidad Stock</th>
                        <th>Costo</th>
                        <th>Fecha Registro</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="medicamentos-table-body">
                    <!-- Datos dinámicos de la tabla se cargarán aquí -->
                </tbody>
            </table>
        </div>
    </div>


</div>

<?php require_once '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../swalcustom.js"></script>



<script>
    document.addEventListener("DOMContentLoaded", () => {

        let notificacionesMostradas = false;

        const formRegistrarMedicamento = document.querySelector("#form-registrar-medicamento");
        const formEntrada = document.querySelector("#formEntrada");
        const formSalida = document.querySelector("#formSalida");
        const medicamentosTable = document.querySelector("#medicamentos-table");
        const tipoMedicamentoSelect = document.querySelector("#tipo");
        const presentacionSelect = document.querySelector("#presentacion");
        const formAgregarPresentacion = document.querySelector("#formAgregarPresentacion");
        const formAgregarUnidadMedida = document.querySelector("#formAgregarUnidadMedida");
        const formAgregarTipo = document.querySelector("#formAgregarTipo");

        const messageArea = document.getElementById("message-area");
        const btnSugerencias = document.getElementById("btnSugerencias");

        const tipoEquinoMovimiento = document.querySelector("#tipoEquinoMovimiento");

        // Función para mostrar mensajes dinámicos para medicamentos
        function mostrarMensaje(mensaje, tipo = 'INFO') {
            const messageArea = document.getElementById("message-area"); // Asegúrate de tener un div con el id 'messageAreaMedicamento'

            if (messageArea) {
                // Definición de estilos para cada tipo de mensaje
                const estilos = {
                    'INFO': { color: '#3178c6', bgColor: '#e7f3ff', icon: 'ℹ️' },
                    'SUCCESS': { color: '#3c763d', bgColor: '#dff0d8', icon: '✅' },
                    'ERROR': { color: '#a94442', bgColor: '#f2dede', icon: '❌' },
                    'WARNING': { color: '#8a6d3b', bgColor: '#fcf8e3', icon: '⚠️' }
                };

                // Obtener los estilos correspondientes al tipo de mensaje
                const estilo = estilos[tipo] || estilos['INFO'];

                // Aplicar estilos al contenedor del mensaje
                messageArea.style.display = 'flex';
                messageArea.style.alignItems = 'center';
                messageArea.style.color = estilo.color;
                messageArea.style.backgroundColor = estilo.bgColor;
                messageArea.style.fontWeight = 'bold';
                messageArea.style.padding = '15px';
                messageArea.style.marginBottom = '15px';
                messageArea.style.border = `1px solid ${estilo.color}`;
                messageArea.style.borderRadius = '8px';
                messageArea.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';

                // Mostrar el mensaje con un icono
                messageArea.innerHTML = `<span style="margin-right: 10px; font-size: 1.2em;">${estilo.icon}</span>${mensaje}`;

                // Ocultar el mensaje después de 5 segundos
                setTimeout(() => {
                    messageArea.style.display = 'none';
                    messageArea.innerHTML = ''; // Limpiar contenido
                    messageArea.style.border = 'none';
                    messageArea.style.boxShadow = 'none';
                    messageArea.style.backgroundColor = 'transparent';
                }, 5000);
            } else {
                console.warn('El contenedor de mensajes para medicamentos no está presente en el DOM.');
            }
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
                // Hacer la solicitud al servidor para obtener todas las sugerencias usando GET
                const response = await fetch(`../../controllers/admedi.controller.php?operation=listarSugerenciasMedicamentos`, {
                    method: "GET",
                });

                const result = await response.json();

                if (result.status === "success") {
                    // Limpiar el contenido anterior de la tabla
                    const tableBody = document.getElementById('sugerenciasTableBody');
                    tableBody.innerHTML = '';

                    // Iterar sobre las sugerencias y agregarlas a la tabla con botón de edición
                    result.data.forEach(sugerencia => {
                        const row = `
                            <tr>
                                <td>${sugerencia.tipo}</td>
                                <td>${sugerencia.presentaciones}</td>
                                <td>${sugerencia.dosis}</td>
                                <td>
                                    <button onclick="editarSugerencia(${sugerencia.id}, '${sugerencia.tipo}', '${sugerencia.presentaciones}', '${sugerencia.dosis}')" class="btn btn-warning btn-sm">
                                        Editar
                                    </button>
                                </td>
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

        window.editarSugerencia = function(id, tipo, presentacion, dosis) {
            // Aquí va tu código para la función editarSugerencia
            console.log("Editando sugerencia:", { id, tipo, presentacion, dosis });

            // Ejemplo: abrir un modal y llenar campos con los datos
            document.getElementById('editarTipo').value = tipo;
            document.getElementById('editarPresentacion').value = presentacion;
            document.getElementById('editarDosis').value = dosis;

            // Establecer el ID de la sugerencia en el campo oculto, si lo necesitas
            document.getElementById('editarId').value = id;

            // Cerrar el modal de sugerencias
             $('#modalSugerencias').modal('hide');


            $('#modalEditarSugerencia').modal('show'); // Mostrar el modal de edición
        };





        // Función para cargar los tipos de equinos
        const loadTipoEquinos = async () => {
            try {
                // Hacemos la solicitud GET con los parámetros en la URL
                const response = await fetch('../../controllers/admedi.controller.php?operation=getTipoEquinos', {
                method: "GET"
                });

                const textResponse = await response.text();

                // Intentar convertir el texto en JSON
                const parsedResponse = JSON.parse(textResponse);

                // Verificar si la respuesta es exitosa y contiene los datos de tipos de equinos
                if (parsedResponse.status === 'success' && Array.isArray(parsedResponse.data)) {
                const tipos = parsedResponse.data;

                // Limpiar el select antes de añadir contenido nuevo
                tipoEquinoMovimiento.innerHTML = '<option value="">Seleccione Tipo de Equino</option>';

                // Añadir cada tipo de equino al select
                tipos.forEach(tipo => {
                    const option = document.createElement('option');
                    option.value = tipo.idTipoEquino;
                    option.textContent = tipo.tipoEquino;
                    tipoEquinoMovimiento.appendChild(option);
                });
                } else {
                mostrarMensajeDinamico('No se encontraron tipos de equinos.', 'INFO');
                }
            } catch (error) {
                console.error("Error al cargar tipos de equinos:", error);
                mostrarMensajeDinamico('Error al cargar tipos de equinos.', 'ERROR');
            }
        };

        // Historial de movimientos de medicamentos
        const loadHistorialMovimientos = async () => {
            try {

                const filtroRango = document.getElementById('filtroRango').value;
                let fechaInicio, fechaFin;
                const hoy = new Date();

                // Definir el rango de fechas basado en el filtro
                switch (filtroRango) {
                    case 'hoy':
                        fechaInicio = fechaFin = hoy.toISOString().split('T')[0];
                        break;
                    case 'ultimaSemana':
                        fechaInicio = new Date(hoy.setDate(hoy.getDate() - 7)).toISOString().split('T')[0];
                        fechaFin = new Date().toISOString().split('T')[0];
                        break;
                    case 'ultimoMes':
                        fechaInicio = new Date(hoy.setMonth(hoy.getMonth() - 1)).toISOString().split('T')[0];
                        fechaFin = new Date().toISOString().split('T')[0];
                        break;
                    default:
                        fechaInicio = '';
                        fechaFin = '';
                }


                // Solicitud para Entradas de Medicamentos usando ruta relativa
                const entradasURL = `../../controllers/admedi.controller.php?operation=obtenerHistorialMovimientosMedicamentos&tipoMovimiento=Entrada&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`;

                const responseEntradas = await fetch(entradasURL, { method: "GET" });
                const parsedEntradas = await responseEntradas.json();

                // Verificar que haya datos para Entradas de Medicamentos y cargarlos en DataTable
                if (parsedEntradas.status === 'success' && Array.isArray(parsedEntradas.data)) {
                    if (parsedEntradas.data.length > 0) {
                        $('#tabla-entradas').DataTable().clear().destroy();
                        $('#tabla-entradas').DataTable({
                            data: parsedEntradas.data,
                            columns: [
                                { data: 'idMedicamento' },
                                { data: 'nombreMedicamento' },
                                { data: 'descripcion' },
                                { data: 'stockActual' },
                                { data: 'cantidad' },
                                { data: 'fechaMovimiento' }
                            ],
                            responsive: true,
                            autoWidth: false,
                            paging: true,
                            searching: true,
                            language: {
                                url: '/haras/data/es_es.json'
                            }
                        });
                    }
                }

                // Solicitud para Salidas de Medicamentos usando ruta relativa
                const salidasURL = `../../controllers/admedi.controller.php?operation=obtenerHistorialMovimientosMedicamentos&tipoMovimiento=Salida&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`;

                const responseSalidas = await fetch(salidasURL, { method: "GET" });
                const parsedSalidas = await responseSalidas.json();

                // Verificar que haya datos para Salidas de Medicamentos y cargarlos en DataTable
                if (parsedSalidas.status === 'success' && Array.isArray(parsedSalidas.data)) {
                    if (parsedSalidas.data.length > 0) {
                        $('#tabla-salidas').DataTable().clear().destroy();
                        $('#tabla-salidas').DataTable({
                            data: parsedSalidas.data,
                            columns: [
                                { data: 'idMedicamento' },
                                { data: 'nombreMedicamento' },
                                { data: 'descripcion' },
                                { data: 'tipoEquino' },
                                { data: 'cantidad' },
                                { data: 'motivo' },
                                { data: 'fechaMovimiento' }
                            ],
                            responsive: true,
                            autoWidth: false,
                            paging: true,
                            searching: true,
                            language: {
                                url: '/haras/data/es_es.json'
                            }
                        });
                    } 
                } 

            } catch (error) {
                console.error('Error al cargar historial de movimientos de medicamentos:', error);
                mostrarMensajeDinamico('Error al cargar historial de movimientos de medicamentos.', 'ERROR');
            }
        };

        // Vincular la función al cambio en el filtro de rango
        document.getElementById('filtroRango').addEventListener('change', loadHistorialMovimientos);
        document.getElementById('buscarHistorial').addEventListener('click', loadHistorialMovimientos);

        // Cargar los tipos de medicamentos desde el servidor
        // Cargar los tipos de medicamentos desde el servidor
        const loadTiposMedicamentos = async () => {
            try {
                const response = await fetch(`../../controllers/admedi.controller.php?operation=listarTiposMedicamentos`, {
                    method: "GET",
                });
                const result = await response.json();

                // Limpiar el select y agregar opciones
                tipoMedicamentoSelect.innerHTML = '<option value="">Seleccione el Tipo de Medicamento</option>';
                
                result.data.forEach(tipo => {
                    const option = document.createElement("option");
                    option.value = tipo.tipo;
                    option.textContent = tipo.tipo;
                    tipoMedicamentoSelect.appendChild(option);
                });
            } catch (error) {
                mostrarMensaje("Error al cargar tipos de medicamentos: " + error.message, 'error');
            }
        };

       
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
                    $('#modalAgregarTipoPresentacion').modal('hide'); // Cierra el modal combinado
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

        // Procesar la adición de una nueva unidad de medida (dosis)
        formAgregarUnidadMedida.addEventListener('submit', async (event) => {
            event.preventDefault();
            const nuevaUnidadMedida = document.querySelector("#nuevaUnidadMedida").value;

            try {
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({ operation: 'agregarUnidadMedida', unidad: nuevaUnidadMedida })
                });

                const result = await response.json();
                if (result.status === "success") {
                    mostrarMensaje(result.message, 'success');
                    showToast(result.message, 'SUCCESS');
                    $('#modalAgregarTipoPresentacion').modal('hide'); // Cierra el modal
                    loadUnidadesMedida(); // Llama a la función para recargar las unidades de medida si es necesario
                } else {
                    mostrarMensaje(result.message, 'error');
                    showToast(result.message, 'ERROR');
                }
            } catch (error) {
                mostrarMensaje("Error al agregar unidad de medida: " + error.message, 'error');
                showToast("Error al agregar unidad de medida", 'ERROR');
            }
        });


        // Cargar presentaciones de medicamentos desde el servidor
        const loadPresentaciones = async () => {
            try {
                const response = await fetch(`../../controllers/admedi.controller.php?operation=listarPresentacionesMedicamentos`, {
                    method: "GET",
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
            } catch (error) {
                mostrarMensaje("Error al cargar presentaciones: " + error.message, 'error');
            }
        };



        // Procesar la adición de una nueva presentación de medicamento
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
                    $('#modalAgregarTipoPresentacion').modal('hide'); // Cierra el modal combinado
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

<<<<<<< HEAD

=======
>>>>>>> 3b0773652d20759ff70ba715b0f9d572c72e47fd
        // Cargar medicamentos en los selectores de entrada y salida
        const loadSelectMedicamentos = async () => {
            try {
                // Definir los parámetros en la URL para el método GET
                const params = new URLSearchParams({ operation: 'getAllMedicamentos' });
                const response = await fetch(`../../controllers/admedi.controller.php?${params.toString()}`, {
                    method: "GET"  // Cambiar el método a GET
                });

                const textResponse = await response.text();
                const result = JSON.parse(textResponse);
                const medicamentos = result.data;

                // Crear un Set para almacenar nombres únicos de medicamentos
                const medicamentosUnicos = new Set();

                // Limpiar los selectores
                document.querySelector("#entradaMedicamento").innerHTML = '<option value="">Seleccione un Medicamento</option>';
                document.querySelector("#salidaMedicamento").innerHTML = '<option value="">Seleccione un Medicamento</option>';

                // Recorrer los medicamentos y agregar solo los nombres únicos
                medicamentos.forEach(med => {
                    if (!medicamentosUnicos.has(med.nombreMedicamento)) {
                        medicamentosUnicos.add(med.nombreMedicamento);

                        // Crear las opciones para los selectores
                        const optionEntrada = document.createElement("option");
                        optionEntrada.value = med.nombreMedicamento;
                        optionEntrada.textContent = med.nombreMedicamento;

                        const optionSalida = document.createElement("option");
                        optionSalida.value = med.nombreMedicamento;
                        optionSalida.textContent = med.nombreMedicamento;

                        // Añadir las opciones a los selectores
                        document.querySelector("#entradaMedicamento").appendChild(optionEntrada);
                        document.querySelector("#salidaMedicamento").appendChild(optionSalida);
                    }
                });

                

            } catch (error) {
                mostrarMensaje("Error al cargar medicamentos: " + error.message, 'error');
                showToast("Error al cargar medicamentos", 'ERROR');
            }
        };
        
        

        // Cargar lista de medicamentos en la tabla
<<<<<<< HEAD
        // Cargar lista de medicamentos en la tabla con DataTable
=======
>>>>>>> 3b0773652d20759ff70ba715b0f9d572c72e47fd
        const loadMedicamentos = async () => {
            try {
                const params = new URLSearchParams({ operation: 'getAllMedicamentos' });
                const response = await fetch(`../../controllers/admedi.controller.php?${params.toString()}`, {
                    method: "GET"
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
                        { data: 'nombreTipo', defaultContent: 'N/A' },
                        { data: 'fechaCaducidad', defaultContent: 'N/A' },
                        { data: 'cantidad_stock', defaultContent: 'N/A' },
                        { data: 'precioUnitario', defaultContent: 'N/A' },
                        { data: 'fechaIngreso', defaultContent: 'N/A' },
                        { data: 'estado', defaultContent: 'N/A' },
                        {
                            data: null,
                            title: 'Acciones',
                            render: function(data, type, row) {
                                return `<button class="btn btn-danger btn-sm shadow-sm" onclick="borrarMedicamento('${row.idMedicamento}')">
                                            <i class="fas fa-trash"></i> Borrar
                                        </button>`;
                            },
                            orderable: false
                        }
                    ],
                    pageLength: 5,
                    autoWidth: false,
                    responsive: true,
                    scrollX: true,
                    language: {
                        url: '/haras/data/es_es.json'
                    },
                    createdRow: function(row, data, dataIndex) {
                        $(row).css({
                            'font-weight': 'bold',
                            'color': '#333',
                            'background-color': (dataIndex % 2 === 0) ? '#f7f9fc' : '#ffffff',
                            'border-bottom': '2px solid #0077b6'
                        });
                    },
                    initComplete: function() {
                        $('#medicamentosTable').css({
                            'border-collapse': 'collapse',
                            'width': '100%',
                            'font-size': '16px',
                            'table-layout': 'fixed'
                        });

                        $('th').css({
                            'background-color': '#3498db',
                            'color': '#ffffff',
                            'font-size': '14px',
                            'text-align': 'center'
                        });

                        $('td').css({
                            'padding': '10px',
                            'text-align': 'center'
                        });

                        $('.btn-danger').css({
                            'border-radius': '5px',
                            'font-weight': 'bold',
                            'color': '#ffffff'
                        });
                    }
                });


            } catch (error) {
                mostrarMensaje("Error al cargar medicamentos: " + error.message, 'error');
                showToast("Error al cargar medicamentos", 'ERROR');
            }
        };


        // **Función para manejar la notificación de stock bajo/agotado para medicamentos**
        const notificarStockBajo = async () => {
            try {
                // Realizar la solicitud GET al controlador de medicamentos
                const response = await fetch('../../controllers/admedi.controller.php?operation=notificarStockBajo', {
                method: "GET"
                });

                // Leer la respuesta y parsear a JSON
                const textResponse = await response.text();
                const result = JSON.parse(textResponse);

                // Verificar si hay datos y recorrer los resultados
                if (result.status === 'success' && result.data) {
                const { agotados, bajoStock } = result.data;

                // Mostrar notificaciones de medicamentos agotados
                agotados.forEach(notificacion => {
                    mostrarMensaje(notificacion.Notificacion, 'ERROR'); // Puedes usar 'ERROR' para más énfasis
                });

                // Mostrar notificaciones de medicamentos con stock bajo
                bajoStock.forEach(notificacion => {
                    mostrarMensaje(notificacion.Notificacion, 'WARNING');
                });
                } else if (result.status === 'info') {
                    mostrarMensaje(result.message, 'INFO');
                }
            } catch (error) {
                mostrarMensaje('Error al notificar stock bajo.', 'ERROR');
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
                            operation: 'deleteMedicamento',
                            idMedicamento: idMedicamento
                        })
                    });
                    const result = await response.json();
                    if (result.status === 'success') {
                        showToast("Medicamento eliminado correctamente.", "SUCCESS");
                        await loadSelectMedicamentos();
                        await cargarLotes();
                        await loadMedicamentos();  // Recargar los medicamentos después de eliminar
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
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({
                        operation: 'validarRegistrarCombinacion',
                        tipoMedicamento: params.tipo,        
                        presentacionMedicamento: params.presentacion,  
                        dosisMedicamento: params.dosis       
                    })
                });

                const result = await response.json();

                if (result.status === "success") {
                    return true;  // La combinación es válida
                } else {
                    mostrarMensaje(result.message, 'error');
                    return false;  // La combinación es inválida
                }
            } catch (error) {
                mostrarMensaje("Error al validar combinación: " + error.message, 'error');
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
        // Registrar medicamento
        formRegistrarMedicamento.addEventListener("submit", async (event) => {
            event.preventDefault();


            // Obtener el valor combinado de dosis y unidad
            const dosisCompleta = document.querySelector("#dosis").value;

            // Usar una expresión regular para separar la cantidad de la unidad
            const match = dosisCompleta.match(/^(\d+(\.\d+)?)(\s?[a-zA-Z]+)$/);
            if (!match) {
                mostrarMensaje("Formato de dosis inválido. Use un número seguido de una unidad (ej. 500 mg)", "error");
                return;
            }

            const dosis = parseFloat(match[1]);
            const unidad = match[3].trim();

            // Validar que ambos elementos estén presentes
            if (!dosis || !unidad) {
                mostrarMensaje("Debe ingresar una dosis válida con su unidad", "error");
                return;
            }

            // Crear los datos para enviar al backend
            const formData = new FormData(formRegistrarMedicamento);
            formData.append('dosis', dosis);
            formData.append('unidad', unidad);
            formData.append('operation', 'registrar');

            formData.forEach((value, key) => {
            });

            try {
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: formData
                });

                const text = await response.text();
                let result;
                try {
                    result = JSON.parse(text);
                } catch (jsonError) {
                    mostrarMensaje("Error al interpretar la respuesta del servidor. Respuesta no válida.", 'error');
                    return;
                }

                if (result.status === "success") {
                    showToast("Medicamento registrado correctamente", "SUCCESS");
                    formRegistrarMedicamento.reset();
                    // Llamar a las funciones para recargar los selectores de medicamentos y lotes
                    // Llamar a las funciones para recargar los selectores de medicamentos y lotes
                    await loadSelectMedicamentos();
                    await cargarLotes();
                    await loadMedicamentos();
                } else {
                    mostrarMensaje("Error en el registro: " + result.message, 'error');
                }
            } catch (error) {
                mostrarMensaje("Error al registrar el medicamento: " + error.message, 'error');
            }
        });

<<<<<<< HEAD

=======
>>>>>>> 3b0773652d20759ff70ba715b0f9d572c72e47fd
        // Implementar para la entrada de medicamentos
        formEntrada.addEventListener("submit", async (event) => {
            event.preventDefault();

            // Confirmar la operación
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
                    showToast("Entrada registrada correctamente", "SUCCESS");
                    formEntrada.reset();
                    cargarLotes();
                    loadMedicamentos();
                } else {
                    showToast("Error en el registro de entrada: " + result.message, 'error');
                }
            } catch (error) {
                showToast("Error en la solicitud de registro de entrada: " + error.message, 'error');
            }
        });

        // Función para cargar los lotes en los select de entrada y salida de medicamentos
        const cargarLotes = async () => {
            const entradaLoteSelect = document.querySelector("#entradaLote");  
            const salidaLoteSelect = document.getElementById('salidaLote');

            try {
                const response = await fetch('../../controllers/admedi.controller.php?operation=listarLotes', {
                    method: 'GET',
                });

                const result = await response.json();

                if (result.status === "success") {
                    entradaLoteSelect.innerHTML = '<option value="">Seleccione un lote</option>';
                    salidaLoteSelect.innerHTML = '<option value="">Seleccione un lote</option>';

                    result.data.forEach(lote => {
                        const optionEntrada = document.createElement("option");
                        optionEntrada.value = lote.lote;
                        optionEntrada.textContent = `${lote.lote} - ${lote.nombreMedicamento}`;
                        entradaLoteSelect.appendChild(optionEntrada);

                        const optionSalida = document.createElement("option");
                        optionSalida.value = lote.lote;
                        optionSalida.textContent = `${lote.lote} - ${lote.nombreMedicamento}`;
                        salidaLoteSelect.appendChild(optionSalida);
                    });
                } else {
                    mostrarMensaje("No se encontraron lotes registrados.", 'error');
                }
            } catch (error) {
                mostrarMensaje("Error al cargar los lotes: " + error.message, 'error');
            }
        };


        // Implementar para la salida de medicamentos
        // Implementar para la salida de medicamentos
        if (formSalida) {
            formSalida.addEventListener("submit", async (event) => {
                event.preventDefault();

<<<<<<< HEAD
=======
            const cantidadField = document.getElementById('salidaCantidad');
            const cantidad = parseFloat(cantidadField.value) || 0;
            if (cantidad <= 0) {
            showToast("La cantidad debe ser mayor a 0.", 'ERROR');
                return;
            }
>>>>>>> 3b0773652d20759ff70ba715b0f9d572c72e47fd

                const cantidadField = document.getElementById('salidaCantidad');
                const motivoField = document.getElementById('motivoSalida');
                const loteField = document.getElementById('salidaLote');
                const medicamentoField = document.getElementById('salidaMedicamento');
                const tipoEquinoField = document.getElementById('tipoEquinoMovimiento');

<<<<<<< HEAD
                // Verificar existencia de todos los elementos requeridos
                if (!cantidadField || !motivoField || !loteField || !medicamentoField || !tipoEquinoField) {
                    return;
=======
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
                    showToast("Salida registrada correctamente", "SUCCESS");
                    formSalida.reset();
                    await loadMedicamentos();
                    await notificarStockBajo();

                } else {
                    showToast("Error en el registro de salida", "ERROR");
>>>>>>> 3b0773652d20759ff70ba715b0f9d572c72e47fd
                }
                

                const cantidad = parseFloat(cantidadField.value) || 0;
                const motivo = motivoField.value.trim();
                const lote = loteField.value.trim() || null;



                // Validación adicional en JavaScript
                if (cantidad <= 0) {
                    showToast("La cantidad debe ser mayor a 0.", 'ERROR');
                    return;
                }
                if (!motivo) {
                    showToast("Debe especificar un motivo para la salida del medicamento.", 'ERROR');
                    return;
                }

                // Confirmación de la operación
                const confirmar = await ask("¿Estás seguro de que deseas registrar la salida de este medicamento?", "Registrar Salida de Medicamento");
                if (!confirmar) {
                    showToast("Operación cancelada.", "INFO");
                    return;
                }


                // Preparar datos para enviar
                const formData = new FormData();
                formData.append('operation', 'salida');
                formData.append('nombreMedicamento', medicamentoField.value);
                formData.append('cantidad', cantidad);
                formData.append('idTipoEquino', tipoEquinoField.value);
                formData.append('motivo', motivo);

                if (lote !== null) {
                    formData.append('lote', lote);
                }


                try {
                    const response = await fetch('../../controllers/admedi.controller.php', {
                        method: "POST",
                        body: formData
                    });

                    const result = await response.json();

                    if (result.status === "success") {
                        showToast("Salida registrada correctamente", "SUCCESS");
                        formSalida.reset();
                        await loadMedicamentos();
                        await notificarStockBajo();
                        await loadSelectMedicamentos();
                        await cargarLotes();
                    } else {
                        showToast(result.message || "Error en el registro de salida", "ERROR");
                    }
                } catch (error) {
                    showToast("Error en la solicitud de registro de salida", "ERROR");
                }
            });
        }




        // Cargar datos al iniciar la página
        cargarLotes();
        loadTipoEquinos();
        loadHistorialMovimientos();
        loadSelectMedicamentos();
        loadMedicamentos();
        loadTiposMedicamentos();
        loadPresentaciones();
    });
</script>

