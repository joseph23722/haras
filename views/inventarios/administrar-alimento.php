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
        data-bs-target="#modalSugerenciasAlimentos">
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
      <div class="row justify-content-center">
        <!-- Botón Listado Alimentos -->
        <div class="col-12 col-md-3 mb-3">
          <button class="btn btn-outline-primary btn-lg w-100" style="border-color: #3498db;" onclick="window.location.href='./listar-alimento'">
            <i class="fas fa-save"></i> Listado Alimentos
          </button>
        </div>

        <!-- Botón Registrar Entrada de Alimento -->
        <div class="col-12 col-md-3 mb-3">
          <button class="btn btn-outline-primary btn-lg w-100" style="border-color: #007bff;" data-bs-toggle="modal" data-bs-target="#modalEntradaAlimento">
            <i class="fas fa-arrow-up"></i> Registrar Entrada de Alimento
          </button>
        </div>

        <!-- Botón Registrar Salida de Alimento -->
        <div class="col-12 col-md-3 mb-3">
          <button class="btn btn-outline-danger btn-lg w-100" style="border-color: #dc3545;" data-bs-toggle="modal" data-bs-target="#modalSalidaAlimento">
            <i class="fas fa-arrow-down"></i> Registrar Salida de Alimento
          </button>
        </div>

        <!-- Botón Historial de Movimientos -->
        <div class="col-12 col-md-3 mb-3">
          <button class="btn btn-outline-info btn-lg w-100" style="border-color: #17a2b8;" onclick="window.location.href='./listar-historial-alimento'">
            <i class="fas fa-history"></i> Historial de Movimientos (E/S)
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para Sugerencias de Alimentos -->
  <div class="modal fade" id="modalSugerenciasAlimentos" tabindex="-1" aria-labelledby="modalSugerenciasAlimentosLabel" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalSugerenciasAlimentosLabel">Sugerencias de Tipos de Alimentos y Unidades de Medida</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table id="tablaAlimentos" class="table table-hover table-bordered mt-3" style="width: 100%;">
            <thead class="table-light">
              <tr class="text-center">
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

  <!-- Modal para Editar Sugerencias -->
  <div class="modal fade" id="modalEditarSugerenciaAlimento" tabindex="-1" aria-labelledby="modalEditarSugerenciaAlimentoLabel" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-warning text-white">
          <h5 class="modal-title" id="modalEditarSugerenciaAlimentoLabel">Editar Sugerencia de Alimento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formEditarSugerenciaAlimento">
            <input type="hidden" id="editarIdTipoAlimento">
            <input type="hidden" id="editarIdUnidadMedida">
            <div class="mb-3">
              <label for="editarTipoAlimento" class="form-label">Tipo de Alimento</label>
              <input type="text" class="form-control" id="editarTipoAlimento" required>
            </div>
            <div class="mb-3">
              <label for="editarUnidadMedida" class="form-label">Unidad de Medida</label>
              <input type="text" class="form-control" id="editarUnidadMedida" required>
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
</div>

<?php require_once '../footer.php'; ?>
<!-- jsPDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<!-- jsPDF-AutoTable -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.1/jspdf.plugin.autotable.min.js"></script>





<script src="/haras/vendor/alimentos/historial-alimentos.js" defer></script>
<script src="/haras/vendor/alimentos/listar-alimentos.js" defer></script>
<script id="jqueryScript" src="https://code.jquery.com/jquery-3.6.4.min.js" defer></script>
<script id="dataTableScript" src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js" defer></script>
<script src="../../JS/administrar-alimento.js"></script>
