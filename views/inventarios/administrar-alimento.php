<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
  <!-- Título principal -->
  <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">
    Gestionar Alimentos
  </h1>

  <!-- Formulario para Registrar Nuevo Alimento -->
  <div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
      <h5 class="text-center"><i class="fas fa-apple-alt" style="color: #3498db;"></i> Registrar Nuevo Alimento</h5>
      <!-- Botón de Agregar en el header -->
      <button type="button" class="btn btn-success btn-sm"
        style="background-color: #28a745; border: none; position: absolute; right: 5px; top: 5px; padding: 10px 15px; font-size: 1.2em;"
        id="btnAgregar"
        data-bs-toggle="modal"
        data-bs-target="#modalAgregarCategoriaMedida">
        <i class="fas fa-plus"></i>
      </button>

      <!-- Botón para abrir el modal de listar Tipos de Alimentos y Unidades de Medida -->
      <button type="button" class="btn btn-info btn-sm"
        style="background-color: #17a2b8; border: none; position: absolute; right: 60px; top: 5px; padding: 10px 15px; font-size: 1.2em;"
        id="btnListarAlimentos"
        data-bs-toggle="modal"
        data-bs-target="#modalListarAlimentos">
        <i class="fas fa-list"></i>
      </button>
    </div>
    <div class="card-body" style="background-color: #f9f9f9;">
      <form action="" id="form-registrar-alimento" autocomplete="off">
        <div class="row g-3">

          <!-- Campo: Nombre del Alimento -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" name="nombreAlimento" id="nombreAlimento" class="form-control" required placeholder="">
              <label for="nombreAlimento"><i class="fas fa-seedling" style="color: #3498db;"></i> Nombre del Alimento</label>
            </div>
          </div>

          <!-- Campo: Tipo de Alimento (Carga dinámica) -->
          <div class="col-md-4">
            <div class="form-floating">
              <select id="tipoAlimento" name="tipoAlimento" class="form-select" required>
                <option value="" disabled selected>Seleccione el tipo de alimento</option>
                <!-- Las opciones se agregarán dinámicamente aquí -->
              </select>
              <label for="tipoAlimento"><i class="fas fa-carrot" style="color: #3498db;"></i> Tipo de Alimento</label>
            </div>
          </div>

          <!-- Campo: Stock Actual -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="number" name="stockActual" id="stockActual" class="form-control" required min="0" placeholder="">
              <label for="stockActual"><i class="fas fa-weight" style="color: #3498db;"></i> Stock Actual</label>
            </div>
          </div>

          <!-- Campo: Stock Mínimo -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="number" name="stockMinimo" id="stockMinimo" class="form-control" value="10" required min="0">
              <label for="stockMinimo"><i class="fas fa-exclamation-triangle" style="color: #e74c3c;"></i> Stock Mínimo</label>
            </div>
          </div>

          <!-- Campo: Unidad de Medida (Carga dinámica) -->
          <div class="col-md-4">
            <div class="form-floating">
              <select id="unidadMedida" name="unidadMedida" class="form-select" required>
                <option value="">Seleccione la Unidad de Medida</option>
                <!-- Las opciones se agregarán dinámicamente aquí -->
              </select>
              <label for="unidadMedida"><i class="fas fa-balance-scale" style="color: #3498db;"></i> Unidad de Medida</label>
            </div>
          </div>

          <!-- Campo: Costo -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="number" step="0.01" name="costo" id="costo" class="form-control" required placeholder="">
              <label for="costo"><i class="fas fa-dollar-sign" style="color: #3498db;"></i> Costo</label>
            </div>
          </div>

          <!-- Campo: Lote -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" name="lote" id="lote" class="form-control" placeholder="" required>
              <label for="lote"><i class="fas fa-box" style="color: #3498db;"></i> Lote--</label>
            </div>
          </div>

          <!-- Campo: Fecha de Caducidad -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="date" name="fechaCaducidad" id="fechaCaducidad" class="form-control" required>
              <label for="fechaCaducidad"><i class="fas fa-calendar-alt" style="color: #3498db;"></i> Fecha de Caducidad</label>
            </div>
          </div>

          <!-- Mensaje de confirmación -->
          <div id="mensaje" style="margin-top: 10px; color: green; font-weight: bold;"></div>

          <!-- Botones -->
          <div class="col-md-12 text-end">
            <a href="./listar-alimento" class="btn btn-primary btn-lg" style="background-color: #3498db; border-color: #3498db;">
              <i class="fas fa-save"></i> Listado Alimentos
            </a>
            <button type="submit" class="btn btn-primary btn-lg" style="background-color: #3498db; border-color: #3498db;">
              <i class="fas fa-save"></i> Registrar Alimento
            </button>
            <button type="button" class="btn btn-secondary btn-lg">
              <i class="fas fa-times"></i> Cancelar
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
      <button class="btn btn-outline-primary btn-lg me-3" style="border-color: #007bff;" data-bs-toggle="modal" data-bs-target="#modalEntradaAlimento">
        <i class="fas fa-arrow-up"></i> Registrar Entrada de Alimento
      </button>
      <button class="btn btn-outline-danger btn-lg me-3" style="border-color: #dc3545;" data-bs-toggle="modal" data-bs-target="#modalSalidaAlimento">
        <i class="fas fa-arrow-down"></i> Registrar Salida de Alimento
      </button>
      <!-- Botón para abrir el modal -->
      <button class="btn btn-outline-info btn-lg" style="border-color: #17a2b8;" data-bs-toggle="modal" data-bs-target="#modalHistorialAlimentos">
        <i class="fas fa-history"></i> Ver Historial de Movimientos
      </button>

    </div>
  </div>

  <!-- Modal para listar Tipos de Alimentos y Unidades de Medida -->
  <div class="modal fade" id="modalListarAlimentos" tabindex="-1" aria-labelledby="modalListarAlimentosLabel" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title" id="modalListarAlimentosLabel">Listado de Tipos de Alimentos y Unidades de Medida</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Tabla manejada por DataTables -->
          <table id="tablaAlimentos" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Tipo de Alimento</th>
                <th>Unidad de Medida</th>
                <th>Acciones</th>
              </tr>
            </thead>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para editar un Tipo de Alimento y Unidad de Medida -->
  <div class="modal fade" id="modalEditarAlimento" tabindex="-1" aria-labelledby="modalEditarAlimentoLabel" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-warning text-white">
          <h5 class="modal-title" id="modalEditarAlimentoLabel">Editar Tipo de Alimento y Unidad de Medida</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="formEditarAlimento">
            <!-- Campo oculto para ID de Tipo de Alimento -->
            <input type="hidden" id="editarIdTipoAlimento">
            <input type="hidden" id="editarIdTipoAlimentoUnidad">

            <!-- Campo para Tipo de Alimento -->
            <div class="mb-3">
              <label for="editarTipoAlimento" class="form-label">Tipo de Alimento</label>
              <input type="text" class="form-control" id="editarTipoAlimento" placeholder="Escriba el tipo de alimento" required>
            </div>

            <!-- Campo para Unidad de Medida -->
            <div class="mb-3">
              <label for="editarUnidadMedida" class="form-label">Unidad de Medida</label>
              <select id="editarUnidadMedida" class="form-select" required>
                <!-- Las opciones se cargarán dinámicamente -->
              </select>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para Agregar Nueva Categoría de Alimento y Medida -->
  <div class="modal fade" id="modalAgregarCategoriaMedida" tabindex="-1" aria-labelledby="labelAgregarCategoriaMedida" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-md">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #28a745; color: white;">
          <h5 class="modal-title" id="labelAgregarCategoriaMedida">
            <i class="fas fa-plus-circle"></i> Agregar Categoría de Alimento y Unidad de Medida
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color: white;"></button>
        </div>
        <div class="modal-body">
          <form id="formNuevaCategoriaMedida" autocomplete="off">
            <div class="mb-3">
              <label for="inputCategoriaAlimento" class="form-label fw-bold">Categoría de Alimento</label>
              <input type="text" class="form-control" id="inputCategoriaAlimento" name="inputCategoriaAlimento" placeholder="Ingrese la categoría de alimento" required>
            </div>
            <div class="mb-3">
              <label for="inputUnidadMedida" class="form-label fw-bold">Unidad de Medida</label>
              <input type="text" class="form-control" id="inputUnidadMedida" name="inputUnidadMedida" placeholder="Ejemplo: kg, L, paca" required>
            </div>
            <div id="mensajeModal" class="text-center mt-3"></div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success" id="btnGuardarCategoriaMedida">Guardar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para Movimientos de Entrada -->
  <div class="modal fade" id="modalEntradaAlimento" tabindex="-1" aria-labelledby="modalEntradaAlimentoLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background: #48cae4; color: white;">
          <h5 class="modal-title" id="modalEntradaAlimentoLabel">Registrar Entrada de Alimento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="" id="form-entrada-alimento" autocomplete="off">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-floating">
                  <select id="alimento-select-entrada" name="nombreAlimento" class="form-select" required>
                    <option value="">Seleccione un Alimento</option>
                  </select>
                  <label for="alimento-select-entrada">Alimento</label>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-floating">
                  <select id="unidadMedidaEntrada" name="unidadMedida" class="form-select" required>
                    <option value="">Seleccione la Unidad de Medida</option>
                  </select>
                  <label for="unidadMedidaEntrada"><i class="fas fa-balance-scale" style="color: #3498db;"></i> Unidad de Medida</label>
                </div>
              </div>


              <div class="col-md-6">
                <div class="form-floating">
                  <input type="number" name="cantidad" id="stockActual-entrada" class="form-control" required min="0">
                  <label for="stockActual-entrada">Cantidad</label>
                </div>
              </div>

              <!-- Lote -->
              <div class="form-group mb-3">
                <label for="entradaLote" class="form-label">Lote</label>
                <select name="lote" id="entradaLote" class="form-select" required>
                  <option value="">Seleccione un Lote</option>
                  <!-- Aquí se cargarán los lotes dinámicamente -->
                </select>
              </div>
            </div>
          </form>
        </div>
        <div class="col-12">
          <span id="mensaje-stock" style="color: red;"></span>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success" id="guardarEntrada">Guardar Entrada</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para Movimientos de Salida -->
  <div class="modal fade" id="modalSalidaAlimento" tabindex="-1" aria-labelledby="modalSalidaAlimentoLabel" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background: #ff6b6b; color: white;">
          <h5 class="modal-title" id="modalSalidaAlimentoLabel">Registrar Salida de Alimento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="" id="form-salida-alimento" autocomplete="off">
            <div class="row g-3">
              <!-- Selección de Alimento -->
              <div class="col-md-6">
                <div class="form-floating">
                  <select id="alimento-select-salida" name="nombreAlimento" class="form-select" required>
                    <option value="">Seleccione un Alimento</option>
                  </select>
                  <label for="alimento-select-salida">Alimento</label>
                </div>
              </div>

              <!-- Cantidad Total de Salida -->
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="number" name="cantidad" id="cantidad-salida" class="form-control" required min="0" step="0.01">
                  <label for="cantidad-salida">Cantidad Total de Salida</label>
                </div>
              </div>

              <!-- Cantidad de Uso -->
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="number" name="uso" id="uso" class="form-control" required min="0" step="0.01">
                  <label for="uso">Cantidad para Uso</label>
                </div>
              </div>

              <!-- Cantidad de Merma -->
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="number" step="0.01" name="merma" id="merma" class="form-control" required min="0" readonly>
                  <label for="merma">Merma</label>
                </div>
              </div>

              <!-- Selección de Equino -->
              <div class="col-md-6">
                <div class="form-floating">
                  <select id="idEquino" name="idEquino" class="form-select" required>
                    <option value="">Seleccione Categoría de Equino</option>
                    <!-- Opciones se cargarán dinámicamente -->
                  </select>
                  <label for="idEquino">Categoría de Equino</label>
                </div>
              </div>

              <!-- Unidad de Medida -->
              <div class="col-md-4">
                <div class="form-floating">
                  <select id="unidadMedidaSalida" name="unidadMedida" class="form-select" required>
                    <option value="">Seleccione la Unidad de Medida</option>
                  </select>
                  <label for="unidadMedidaSalida"><i class="fas fa-balance-scale" style="color: #3498db;"></i> Unidad de Medida</label>
                </div>
              </div>

              <!-- Lote para Salida -->
              <div class="form-group mb-3">
                <label for="salidaLote" class="form-label">Lote</label>
                <select name="lote" id="salidaLote" class="form-select" required>
                  <option value="">Seleccione un Lote</option>
                  <!-- Aquí se cargarán los lotes dinámicamente -->
                </select>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger" id="guardarSalida">Guardar Salida</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para Historial de Movimientos de Alimentos -->
  <div class="modal fade" id="modalHistorialAlimentos" tabindex="-1" aria-labelledby="modalHistorialAlimentosLabel" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title" id="modalHistorialAlimentosLabel">Historial de Movimientos de Alimentos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Opciones de Filtrado Rápido -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
              <label for="filtroRangoAlimentos" class="me-2 fw-bold">Ver movimientos de:</label>
              <select id="filtroRangoAlimentos" class="form-select form-select-sm">
                <option value="hoy">Hoy</option>
                <option value="ultimaSemana">Última semana</option>
                <option value="ultimoMes">Último mes</option>
                <option value="todos">Todos</option>
              </select>
            </div>
            <button type="button" id="buscarHistorialAlimentos" class="btn btn-primary ms-3" onclick="reloadHistorialAlimentos();"><i class="fas fa-search me-1"></i>Buscar</button>
          </div>

          <!-- Pestañas para Entrada y Salida -->
          <ul class="nav nav-tabs mb-3" id="historialTabAlimentos" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="entradas-tab-alimentos" data-bs-toggle="tab" data-bs-target="#entradasAlimentos" type="button" role="tab" aria-controls="entradasAlimentos" aria-selected="true">Entradas</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="salidas-tab-alimentos" data-bs-toggle="tab" data-bs-target="#salidasAlimentos" type="button" role="tab" aria-controls="salidasAlimentos" aria-selected="false">Salidas</button>
            </li>
          </ul>

          <!-- Contenido de las Pestañas -->
          <div class="tab-content">
            <!-- Tabla de Entradas de Alimentos -->
            <div class="tab-pane fade show active" id="entradasAlimentos" role="tabpanel" aria-labelledby="entradas-tab-alimentos">
              <div class="table-responsive">
                <table id="tabla-entradas-alimentos" class="table table-bordered table-hover table-striped">
                  <thead class="table-primary">
                    <tr class="text-center">
                      <th>ID Alimento</th>
                      <th>Nombre Alimento</th>
                      <th>Tipo</th>
                      <th>Unidad</th>
                      <th>Cantidad</th>
                      <th>Lote</th>
                      <th>Fecha de Movimiento</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>

            <!-- Tabla de Salidas de Alimentos -->
            <div class="tab-pane fade" id="salidasAlimentos" role="tabpanel" aria-labelledby="salidas-tab-alimentos">
              <div class="table-responsive">
                <table id="tabla-salidas-alimentos" class="table table-bordered table-hover table-striped">
                  <thead class="table-danger">
                    <tr class="text-center">
                      <th>ID Alimento</th>
                      <th>Nombre Alimento</th>
                      <th>Tipo</th>
                      <th>Unidad</th>
                      <th>Cantidad de Salida</th>
                      <th>Unidad Medida</th>
                      <th>Merma</th>
                      <th>Lote</th>
                      <th>Fecha de Movimiento</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once '../footer.php'; ?>
<script src="/haras/vendor/alimentos/historial-alimentos.js" defer></script>
<script src="/haras/vendor/alimentos/listar-alimentos.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../swalcustom.js"></script>
<script src="../../JS/administrar-alimento.js"></script>