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


  <!-- Tabla de Alimentos Registrados -->
  <div class="card mb-4">
      <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
          <h5 class="text-center"><i class="fas fa-database"></i> Alimentos Registrados</h5>
      </div>
      <div class="card-body" style="background-color: #f9f9f9;">
          <table id="alimentos-table" class="table table-striped table-hover table-bordered">
              <thead style="background-color: #caf0f8; color: #003366;">
                  <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th>Tipo</th>
                      <th>U.M</th>
                      <th>Lote</th>
                      <th>Cantidad</th>
                      <th>Stock mínimo</th>
                      <th>Costo</th>
                      <th>Fecha Caducidad</th>
                      <th>Estado</th>
                      <th>Acciones</th>
                  </tr>
              </thead>
          </table>
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


<?php require_once '../footer.php'; ?>
<script src="/haras/vendor/alimentos/historial-alimentos.js" defer></script>
<script src="/haras/vendor/alimentos/listar-alimentos.js" defer></script>


<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../swalcustom.js"></script>



<script>
  document.addEventListener("DOMContentLoaded", () => {
    // Elementos del formulario
    const formRegistrarAlimento = document.querySelector("#form-registrar-alimento");
    const formEntradaAlimento = document.querySelector("#form-entrada-alimento");
    const formSalidaAlimento = document.querySelector("#form-salida-alimento");
    const alimentosTable = document.querySelector("#alimentos-table");
    const alimentoSelectEntrada = document.querySelector("#alimento-select-entrada");
    const alimentoSelectSalida = document.querySelector("#alimento-select-salida");
    const mensajeDiv = document.querySelector("#mensaje");  // Div para mostrar los mensajes dinámicos

    // Elementos del tipo de alimento y unidad de medida para ambos modales
    const tipoAlimentoElementRegistrar = document.getElementById('tipoAlimento');
    const unidadMedidaElementRegistrar = document.getElementById('unidadMedida');
    
    const unidadMedidaSelectEntrada = document.getElementById('unidadMedidaEntrada');
    const unidadMedidaSelectSalida  = document.querySelector("#unidadMedidaSalida");

    // Elementos de fecha de caducidad e ingreso
    const fechaCaducidadElement = document.getElementById('fechaCaducidad');
    
    const loteInput = document.querySelector('#lote');
    const nombreAlimentoInput = document.querySelector('#nombreAlimento');

    // Coloca la función `guardarTipoUnidad` aquí, fuera del bloque `DOMContentLoaded`
    // Verificar campos
    const verificarCampos = () => {
        const categoriaAlimento = document.getElementById("inputCategoriaAlimento")?.value?.trim();
        const unidadMedida = document.getElementById("inputUnidadMedida")?.value?.trim();
        const mensajeModal = document.getElementById("mensajeModal");

        mensajeModal.innerHTML = ""; // Limpiar mensajes previos

        if (!categoriaAlimento) {
            console.log("Campo 'Categoría de Alimento' vacío");
            mensajeModal.innerHTML = '<p class="text-danger">Por favor, complete el campo "Categoría de Alimento".</p>';
            return false;
        }
        if (!unidadMedida) {
            console.log("Campo 'Unidad de Medida' vacío");
            mensajeModal.innerHTML = '<p class="text-danger">Por favor, complete el campo "Unidad de Medida".</p>';
            return false;
        }
        return { categoriaAlimento, unidadMedida };
    };

    
    
    // Función de utilidad para realizar una solicitud GET y obtener datos
    const fetchData = async (url) => {
        try {
            const response = await fetch(url);
            const result = await response.json();
            console.log("Respuesta de la API para URL:", url, "->", result);
            // Asegúrate de que `result` tiene el formato esperado antes de devolver `result.data`
            return result.status === "success" && Array.isArray(result.data) ? result.data : null;
        } catch (error) {
            console.error("Error en la solicitud:", error.message);
            return null;
        }
    };

    // Función para llenar opciones en un select
    const fillSelect = (selectElement, options, placeholder) => {
        selectElement.innerHTML = `<option value="" disabled selected>${placeholder}</option>`;
        // Verificar si `options` es un array antes de usar `forEach`
        if (Array.isArray(options) && options.length > 0) {
            options.forEach(({ id, nombre }) => {
                selectElement.innerHTML += `<option value="${id}">${nombre}</option>`;
            });
        } else {
            console.warn("fillSelect: No se encontraron opciones para llenar el select.");
        }
    };

    // Cargar tipos de alimento en el formulario de registro
    const cargarTiposAlimento = async () => {
        const tipos = await fetchData(`../../controllers/alimento.controller.php?operation=obtenerTiposAlimento`);
        // Verificar si se encontraron tipos de alimento antes de pasarlos a `fillSelect`
        if (tipos) {
            fillSelect(tipoAlimentoElementRegistrar, tipos.map(tipo => ({ id: tipo.idTipoAlimento, nombre: tipo.tipoAlimento })), "Seleccione el tipo de alimento");
        } else {
            console.warn("cargarTiposAlimento: No se encontraron tipos de alimento.");
            fillSelect(tipoAlimentoElementRegistrar, [], "Seleccione el tipo de alimento");
        }
    };

    // Función para cargar las unidades de medida según tipo o nombre de alimento
    const cargarUnidadesMedida = async (param, selectElement, byType = true) => {
        const operation = byType ? `obtenerUnidadesPorTipoAlimento&idTipoAlimento=${param}` : `getUnidadesMedida&nombreAlimento=${param}`;
        
        console.log(`Ejecutando cargarUnidadesMedida con parámetro: ${param}, operación: ${operation}`); // Log para verificar la llamada

        const unidades = await fetchData(`../../controllers/alimento.controller.php?operation=${operation}`);

        console.log(`Unidades obtenidas para ${param} ->`, unidades); // Log para verificar los datos obtenidos

        if (unidades && unidades.length > 0) {
            // Crear un Set para almacenar solo unidades de medida únicas
            const uniqueUnitsSet = new Set();
            const uniqueUnits = [];

            // Filtrar duplicados usando el Set
            unidades.forEach(u => {
                const unitName = u.nombreUnidad;
                if (!uniqueUnitsSet.has(unitName)) {
                    uniqueUnitsSet.add(unitName);
                    uniqueUnits.push({ id: u.idUnidadMedida, nombre: unitName });
                }
            });

            // Llenar el select con las unidades únicas
            fillSelect(selectElement, uniqueUnits, "Seleccione la Unidad de Medida");
        } else {
            console.warn("cargarUnidadesMedida: No se encontraron unidades de medida.");
            fillSelect(selectElement, [], "Seleccione la Unidad de Medida");
        }
    };

    // Eventos de cambio para los selects de alimento y tipo de alimento
    document.getElementById("alimento-select-entrada")?.addEventListener("change", e => {
        console.log("Evento de cambio en alimento-select-entrada activado con valor:", e.target.value);
        cargarUnidadesMedida(e.target.value, document.getElementById("unidadMedidaEntrada"), false);
    });

    document.getElementById("alimento-select-salida")?.addEventListener("change", e => {
        console.log("Evento de cambio en alimento-select-salida activado con valor:", e.target.value);
        cargarUnidadesMedida(e.target.value, document.getElementById("unidadMedidaSalida"), false);
    });

    document.getElementById("tipoAlimento")?.addEventListener("change", e => {
        console.log("Evento de cambio en tipoAlimento activado con valor:", e.target.value);
        cargarUnidadesMedida(e.target.value, document.getElementById("unidadMedida"));
    });


    // Inicialización al cargar la página
    document.addEventListener("DOMContentLoaded", cargarTiposAlimento);


    // Guardar nueva categoría y unidad de medida
    const guardarCategoriaMedida = async () => {
        const valores = verificarCampos();
        if (!valores) return;

        const { categoriaAlimento, unidadMedida } = valores;

        try {
            console.log("Enviando datos...");
            const response = await fetch(`../../controllers/alimento.controller.php`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    operation: "agregarTipoUnidadMedidaNuevo",
                    tipoAlimento: categoriaAlimento,
                    nombreUnidad: unidadMedida
                })
            });

            const result = await response.json();

            const mensajeModal = document.getElementById("mensajeModal");
            mensajeModal.innerHTML = result.status === "success"
                ? '<p class="text-success">¡Agregado correctamente!</p>'
                : `<p class="text-danger">${result.message}</p>`;

            if (result.status === "success") {
                setTimeout(() => {
                    document.getElementById("formNuevaCategoriaMedida").reset();
                    mensajeModal.innerHTML = "";
                    bootstrap.Modal.getInstance(document.getElementById("modalAgregarCategoriaMedida")).hide();
                }, 1500);

                // Llamar a `cargarTiposAlimento` para actualizar la lista de tipos
                await cargarTiposAlimento();
            }
        } catch (error) {
            mensajeModal.innerHTML = '<p class="text-danger">Error al enviar los datos.</p>';
        }
    };

    // Asignar evento al botón de guardar
    const btnGuardar = document.getElementById("btnGuardarCategoriaMedida");
    if (btnGuardar) {
        console.log("Botón 'Guardar' encontrado");
        btnGuardar.addEventListener("click", guardarCategoriaMedida);
    } else {
        console.error("El botón #btnGuardarCategoriaMedida no se encontró.");
    }




    
    // **Función para mostrar notificaciones en el div `mensaje`**
    // **Función para mostrar notificaciones dinámicas para alimentos**
    const mostrarMensajeDinamico = (mensaje, tipo = 'INFO') => {
        const mensajeDiv = document.getElementById('mensaje'); // Asegúrate de tener un div con el id 'mensaje'

        if (mensajeDiv) {
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
            mensajeDiv.style.display = 'flex';
            mensajeDiv.style.alignItems = 'center';
            mensajeDiv.style.color = estilo.color;
            mensajeDiv.style.backgroundColor = estilo.bgColor;
            mensajeDiv.style.fontWeight = 'bold';
            mensajeDiv.style.padding = '15px';
            mensajeDiv.style.marginBottom = '15px';
            mensajeDiv.style.border = `1px solid ${estilo.color}`;
            mensajeDiv.style.borderRadius = '8px';
            mensajeDiv.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';

            // Mostrar el mensaje con un icono
            mensajeDiv.innerHTML = `<span style="margin-right: 10px; font-size: 1.2em;">${estilo.icon}</span>${mensaje}`;

            // Ocultar el mensaje después de 5 segundos
            setTimeout(() => {
                mensajeDiv.style.display = 'none';
                mensajeDiv.innerHTML = ''; // Limpiar contenido
                mensajeDiv.style.border = 'none';
                mensajeDiv.style.boxShadow = 'none';
                mensajeDiv.style.backgroundColor = 'transparent';
            }, 5000);
        } else {
            console.warn('El contenedor de mensajes para alimentos no está presente en el DOM.');
        }
    };


    // **Función para mostrar notificaciones usando showToast**
    const mostrarNotificacion = (mensaje, tipo = 'INFO') => {
      showToast(mensaje, tipo);
    };

    // Validar cantidad positiva en movimientos de entrada y salida
    const cantidadEntrada = document.querySelector("#cantidad-entrada");
    const cantidadSalida = document.querySelector("#cantidad-salida");


    

    // **Fecha de Caducidad**: No permitir fechas pasadas, establecer mínimo como hoy
    if (fechaCaducidadElement) {
        const hoy = new Date().toISOString().split('T')[0]; // Obtener solo la fecha en formato YYYY-MM-DD
        fechaCaducidadElement.setAttribute('min', hoy); // Establecer la fecha mínima como hoy
    }

    // Función para validar la fecha de caducidad
    const validarFechaCaducidad = () => {
      const fechaCaducidadStr = fechaCaducidadElement.value; // Obtener la fecha en formato YYYY-MM-DD
      const hoyStr = new Date().toISOString().split('T')[0]; // Obtener la fecha de hoy en formato YYYY-MM-DD

      // Comparar las fechas en formato YYYY-MM-DD
      if (fechaCaducidadStr < hoyStr) {
        mostrarMensajeDinamico("La fecha de caducidad no puede ser en el pasado.", 'ERROR');
        console.log('Error: La fecha de caducidad es menor que hoy.');
        return false;
      }
      return true;
    };


    // Función para cargar los alimentos registrados
    // Función para cargar los alimentos registrados y mostrarlos en la tabla y en los select de entrada/salida
    const loadAlimentos = async () => {
        try {
            const params = new URLSearchParams({ operation: 'getAllAlimentos' });
            const response = await fetch(`../../controllers/alimento.controller.php?${params.toString()}`, {
                method: "GET"
            });

            const textResponse = await response.text();
            if (textResponse.startsWith("<")) {
                mostrarMensajeDinamico("Error en la respuesta del servidor.", 'ERROR');
                showToast("Error en la respuesta del servidor", 'ERROR'); // Si `showToast` también es una función válida
                return;
            }

            const result = JSON.parse(textResponse);
            const alimentos = result.data;

            // Cargar datos en la tabla de alimentos
            if ($.fn.dataTable.isDataTable('#alimentos-table')) {
                $('#alimentos-table').DataTable().clear().rows.add(alimentos).draw();
            } else {
                configurarDataTableAlimentos(); // Inicializa DataTable si no está inicializado
            }

            // Llamar a función para cargar los select de entrada y salida con los alimentos
            cargarAlimentosEnSelects(alimentos);

        } catch (error) {
            mostrarMensajeDinamico("Error al cargar alimentos: " + error.message, 'ERROR');
            showToast("Error al cargar alimentos", 'ERROR'); // Si `showToast` también es una función válida
        }
    };

    // Función para cargar los alimentos en los select de entrada y salida
    const cargarAlimentosEnSelects = (alimentos) => {
        const alimentoSelectEntrada = document.getElementById('alimento-select-entrada');
        const alimentoSelectSalida = document.getElementById('alimento-select-salida');

        // Limpiar las opciones actuales en los select
        alimentoSelectEntrada.innerHTML = '<option value="">Seleccione un Alimento</option>';
        alimentoSelectSalida.innerHTML = '<option value="">Seleccione un Alimento</option>';

        // Añadir cada alimento a ambos select
        alimentos.forEach(alimento => {
            const optionEntrada = document.createElement('option');
            optionEntrada.value = alimento.nombreAlimento;  // Cambiar a `nombreAlimento`
            optionEntrada.textContent = alimento.nombreAlimento;

            const optionSalida = document.createElement('option');
            optionSalida.value = alimento.nombreAlimento;  // Cambiar a `nombreAlimento`
            optionSalida.textContent = alimento.nombreAlimento;

            // Agregar las opciones a los select
            alimentoSelectEntrada.appendChild(optionEntrada);
            alimentoSelectSalida.appendChild(optionSalida);
        });
    };


    


    // Función para cargar las categorías de equinos con sus cantidades en el select del modal
    const loadCategoriaEquinos = async () => {
        try {
            const response = await fetch('../../controllers/alimento.controller.php?operation=getTipoEquinos', {
                method: "GET"
            });

            const parsedResponse = await response.json();

            if (parsedResponse.status === 'success' && Array.isArray(parsedResponse.data)) {
                const categorias = parsedResponse.data;
                const idEquinoSelect = document.getElementById('idEquino');

                if (!idEquinoSelect) {
                    return;
                }

                // Limpiar opciones previas
                idEquinoSelect.innerHTML = '<option value="">Seleccione Categoría de Equino</option>';

                // Agregar opciones y verificar que `idEquino` sea válido
                categorias.forEach(categoria => {

                    if (categoria.idEquino) {  // Asegurarse de que el idEquino existe y no está undefined
                        const option = document.createElement('option');
                        option.value = categoria.idEquino;  // Confirmar que `idEquino` se usa correctamente
                        option.textContent = `${categoria.Categoria} (${categoria.Cantidad})`;
                        idEquinoSelect.appendChild(option);
                    } else {
                    }
                });
            } else {
                console.warn('No se encontraron categorías de equinos.');
            }
        } catch (error) {
            console.error("Error al cargar categorías de equinos:", error);
        }
    };



    // Función para validar si el lote ya existe para el mismo alimento y unidad de medida
    async function validarLote() {
        // Verificar si los elementos del formulario existen en el DOM
        if (!loteInput || !nombreAlimentoInput || !unidadMedidaElementRegistrar) {
            console.error('Uno o más elementos de entrada no están definidos.');
            mostrarMensajeDinamico('Error: Uno o más campos están vacíos o no existen.', 'ERROR');
            return false;
        }

        // Obtener los valores de lote, nombre del alimento y unidad de medida
        const lote = loteInput.value.trim();
        const nombreAlimento = nombreAlimentoInput.value.trim();
        const unidadMedida = unidadMedidaElementRegistrar.value.trim();
        console.log("Lote a validar:", lote);
        console.log("Nombre del alimento:", nombreAlimento);
        console.log("Unidad de medida:", unidadMedida);


        // Verificar si los campos están vacíos
        if (!lote || !nombreAlimento || !unidadMedida) {
            mostrarMensajeDinamico('El lote, nombre del alimento y la unidad de medida no pueden estar vacíos.', 'ERROR');
            return false;
        }

        try {
            // Petición al servidor para verificar si el lote ya está registrado
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: 'POST',
                body: new URLSearchParams({
                    operation: 'verificarLote',  // Operación para verificar lote, alimento y unidad
                    lote: lote,
                    nombreAlimento: nombreAlimento,
                    unidadMedida: unidadMedida
                })
            });

            const result = await response.json();

            // Si el lote ya existe, mostrar mensaje de error
            if (result.status === 'error') {
                mostrarMensajeDinamico(result.message, 'ERROR');
                return false;
            }

            // Si la combinación es válida, retornar true
            return true;

        } catch (error) {
            mostrarMensajeDinamico('Error al verificar el lote: ' + error.message, 'ERROR');
            return false;
        }
    }

    // Función para registrar un nuevo alimento
    if (formRegistrarAlimento) {
        formRegistrarAlimento.addEventListener("submit", async (event) => {
            event.preventDefault();

            console.log("Formulario enviado. Iniciando validaciones...");

            // Validar fecha de caducidad
            if (!validarFechaCaducidad()) {
                mostrarMensajeDinamico('Error en las fechas de caducidad.', 'ERROR');
                console.log("Error en la validación de la fecha de caducidad.");
                return;
            }

            // Validar si el lote es único para ese alimento y unidad
            console.log("Validando el lote...");
            const loteValido = await validarLote();
            if (!loteValido) {
                mostrarMensajeDinamico('Lote inválido o ya registrado. Verifica los datos.', 'ERROR');
                console.log("Error en la validación del lote.");
                return;
            }
            console.log("Lote válido.");

            // Crear un FormData a partir del formulario
            const formData = new FormData(formRegistrarAlimento);
            const stockActual = parseFloat(formData.get('stockActual'));
            const stockMinimo = parseFloat(formData.get('stockMinimo'));

            // Validar que el stock mínimo no supere al stock actual
            if (stockMinimo > stockActual) {
                mostrarMensajeDinamico("El stock mínimo no puede ser mayor que el stock actual.", 'ERROR');
                console.log("Error: Stock mínimo mayor que el stock actual.");
                return;
            }
            console.log("Stock válido.");

            // Confirmación antes de registrar el alimento
            if (await ask("¿Confirmar registro de nuevo alimento?")) {
                console.log("Confirmación del usuario recibida. Enviando datos...");
                const data = new URLSearchParams(formData);
                data.append('operation', 'registrar');

                try {
                    console.log("Enviando solicitud al servidor...");
                    const response = await fetch('../../controllers/alimento.controller.php', {
                        method: "POST",
                        body: data
                    });

                    const textResult = await response.text();
                    console.log("Respuesta en texto recibida:", textResult);

                    try {
                        const jsonResult = JSON.parse(textResult);
                        console.log("Respuesta en JSON recibida:", jsonResult);

                        // Verificar si el registro fue exitoso
                        if (jsonResult.status === "success") {
                            mostrarMensajeDinamico(jsonResult.message, 'SUCCESS');
                            showToast(jsonResult.message, 'SUCCESS');
                            formRegistrarAlimento.reset();
                            loadAlimentos();
                            console.log("Alimento registrado exitosamente.");
                            await cargarLotes();
                            console.log("Lotes actualizados en los selectores.");
                        } else {
                            mostrarMensajeDinamico(jsonResult.message || "Error en la operación.", 'ERROR');
                            console.log("Error en la respuesta del servidor:", jsonResult.message || "Error en la operación.");
                        }
                    } catch (jsonParseError) {
                        mostrarMensajeDinamico("Error inesperado en la respuesta del servidor. Ver consola.", 'ERROR');
                        console.log("Error al parsear el JSON. Respuesta cruda:", textResult);
                    }
                } catch (error) {
                    mostrarMensajeDinamico("Error en la solicitud: " + error.message, 'ERROR');
                    console.log("Error en la solicitud:", error);
                }
            } else {
                mostrarMensajeDinamico('El usuario canceló la operación.', 'INFO');
                console.log("El usuario canceló la operación.");
            }
        });
    }


    // Función para ajustar las fechas basadas en el filtro seleccionado
    const modalElement = document.getElementById('modalHistorialAlimentos');
    const modalAlimentos = new bootstrap.Modal(modalElement, {
        keyboard: false
    });


    const setFechaFiltroAlimentos = () => {
        const filtroRango = document.getElementById('filtroRangoAlimentos').value;
        const hoy = new Date();
        let fechaInicio, fechaFin;

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
            case 'todos':
                fechaInicio = '1900-01-01';
                fechaFin = new Date().toISOString().split('T')[0];
                break;
            default:
                fechaInicio = '';
                fechaFin = '';
        }

        document.getElementById('filtroRangoAlimentos').setAttribute('data-fecha-inicio', fechaInicio);
        document.getElementById('filtroRangoAlimentos').setAttribute('data-fecha-fin', fechaFin);
    };

    // Función para recargar las tablas de Entradas y Salidas
    const reloadHistorialAlimentos = () => {
        if ($.fn.DataTable.isDataTable('#tabla-entradas-alimentos')) {
            $('#tabla-entradas-alimentos').DataTable().ajax.reload(null, false);
        }
        if ($.fn.DataTable.isDataTable('#tabla-salidas-alimentos')) {
            $('#tabla-salidas-alimentos').DataTable().ajax.reload(null, false);
        }
    };


    // Evento para actualizar el filtro de fecha y recargar las tablas cuando el usuario selecciona un nuevo rango
    document.getElementById('filtroRangoAlimentos').addEventListener('change', () => {
        setFechaFiltroAlimentos();
        reloadHistorialAlimentos();
    });




    // **Función para manejar la notificación de stock bajo/agotado**
    const notificarStockBajo = async () => {
        try {
            // Realizar la solicitud al backend
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                body: new URLSearchParams({ operation: 'notificarStockBajo' })
            });

            const textResponse = await response.text();
            const result = JSON.parse(textResponse);

            // Verifica si 'data' es un array y contiene las notificaciones
            if (Array.isArray(result.data) && result.data.length > 0) {
                result.data.forEach(notificacion => {
                    // Crear el mensaje dinámico con espacios adicionales para claridad
                    const mensajeDinamico = `
                        <span class="text-primary">Alimento:</span> <strong>${notificacion.nombreAlimento}   , 
                        <span class="text-success">Lote:</span> ${notificacion.loteAlimento}  , 
                        <span class="text-warning">Stock:</span> ${notificacion.stockActual}  ,
                        <span class="text-danger">(Mínimo: ${notificacion.stockMinimo})   , 
                        <span class="text-info">Estado:</span> ${notificacion.mensaje} 
                    `.replace(/\s+/g, ' ').trim(); // Elimina espacios extra

                    mostrarMensajeDinamico(mensajeDinamico, 'WARNING');
                });
            } else {
                mostrarMensajeDinamico('No hay productos con stock bajo o agotados.', 'INFO');
            }
        } catch (error) {
            mostrarMensajeDinamico('Error al notificar stock bajo.', 'ERROR');
        }
    };



    // Función para cargar los lotes en los select de entrada y salida de alimentos
    const cargarLotes = async () => {
        const entradaLoteSelect = document.querySelector("#entradaLote");  
        const salidaLoteSelect = document.getElementById('salidaLote');

        try {
            const response = await fetch('../../controllers/alimento.controller.php?operation=listarLotes', {
                method: 'GET',
            });

            const result = await response.json();

            if (result.status === "success") {
                entradaLoteSelect.innerHTML = '<option value="">Seleccione un lote</option>';
                salidaLoteSelect.innerHTML = '<option value="">Seleccione un lote</option>';

                result.data.forEach(lote => {
                    const optionEntrada = document.createElement("option");
                    optionEntrada.value = lote.lote; // Usar el campo 'lote' como valor
                    optionEntrada.textContent = `${lote.lote} - ${lote.nombreAlimento}`;
                    entradaLoteSelect.appendChild(optionEntrada);

                    const optionSalida = document.createElement("option");
                    optionSalida.value = lote.lote;  // Usar el campo 'lote' como valor
                    optionSalida.textContent = `${lote.lote} - ${lote.nombreAlimento}`;
                    salidaLoteSelect.appendChild(optionSalida);
                });
            } else {
                mostrarMensajeDinamico("No se encontraron lotes registrados.", 'error');
            }
        } catch (error) {
            mostrarMensajeDinamico("Error al cargar los lotes: " + error.message, 'error');
        }
    };


    // Función para manejar entradas de alimentos
    const registrarEntrada = async () => {
        const cantidadField = document.getElementById('stockActual-entrada');
        const loteField = document.getElementById('entradaLote');
        const alimentoSelectEntrada = document.getElementById('alimento-select-entrada');
        const unidadMedidaEntrada = document.getElementById('unidadMedidaEntrada');
        const formEntradaAlimento = document.querySelector("#form-entrada-alimento");

        if (!cantidadField || !loteField || !alimentoSelectEntrada || !unidadMedidaEntrada) {
            console.error("Error: Uno o más elementos del formulario no se encontraron en el DOM.");
            showToast("Error en el formulario: faltan elementos.", 'ERROR');
            return;
        }

        const cantidad = parseFloat(cantidadField.value) || 0;
        const lote = loteField.value ? loteField.value : null;

        
        if (await ask("¿Confirmar entrada de alimento?")) {
            console.log("Usuario confirmó la entrada de alimento.");

            const params = {
                operation: 'entrada',
                nombreAlimento: alimentoSelectEntrada.value,
                unidadMedida: unidadMedidaEntrada.value,  // Asegúrate de que contiene el ID de la unidad de medida
                lote: lote,
                cantidad: cantidad
            };

            console.log("Parámetros enviados:", params);

            const data = JSON.stringify(params);

            try {
                const response = await fetch('../../controllers/alimento.controller.php', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: data
                });

                const textResponse = await response.text();
                console.log("Respuesta completa del servidor:", textResponse);

                const result = JSON.parse(textResponse);
                console.log("Respuesta procesada (JSON):", result);

                if (result.status === "success") {
                    showToast(result.message || "Entrada registrada exitosamente.", 'SUCCESS');
                    formEntradaAlimento.reset();
                    $('#modalEntradaAlimento').modal('hide');

                    await loadAlimentos();
                    await setFechaFiltroAlimentos();
                    console.log("Stock actualizado en la interfaz.");
                } else {
                    showToast(result.message || "Error al registrar la entrada.", 'ERROR');
                }
            } catch (error) {
                console.error("Error en la solicitud:", error.message);
                showToast("Error en la solicitud: " + error.message, 'ERROR');
            }
        } else {
            console.log("El usuario canceló la operación.");
        }
    };

    // Añadir el evento al botón de entrada
    document.getElementById("guardarEntrada").addEventListener("click", registrarEntrada);



    // Función para registrar la salida de alimento 
    // Función para registrar la salida de alimento 
    const registrarSalida = async () => {
        try {
            const nombreAlimento = document.getElementById('alimento-select-salida')?.value || '';
            const cantidad = parseFloat(document.getElementById('cantidad-salida')?.value || 0);
            const uso = parseFloat(document.getElementById('uso')?.value || 0);
            const merma = parseFloat(document.getElementById('merma')?.value || 0);
            const idEquino = document.getElementById('idEquino')?.value || '';
            const unidadMedida = document.getElementById('unidadMedidaSalida')?.value || '';
            const lote = document.getElementById('salidaLote')?.value || '';

            // Validar que la cantidad de uso y merma sumen la cantidad total de salida
            if (cantidad !== (uso + merma)) {
                console.warn("La cantidad total de salida debe ser igual a la suma de uso y merma.");
                showToast("La cantidad total debe ser igual a la suma de uso y merma.", 'WARNING');
                return;
            }

            if (!nombreAlimento || !cantidad || !unidadMedida || !lote || idEquino === '' || unidadMedida === '') {
                console.warn("Faltan datos necesarios para registrar la salida.");
                showToast("Por favor, complete todos los campos requeridos.", 'WARNING');
                return;
            }

            // Parámetros con el campo de uso incluido
            const params = {
                operation: 'salida',
                nombreAlimento,
                cantidad,
                uso,
                merma,
                idEquino,
                unidadMedida,
                lote
            };

            console.log("Parámetros que se enviarán al servidor:", params);

            const response = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(params)
            });

            console.log("Estado de la respuesta:", response.status, response.statusText);

            const result = await response.json();
            
            console.log("Respuesta de la API:", result);

            if (result.status === "success") {
                showToast(result.message || "Salida registrada exitosamente.", 'SUCCESS');
                document.getElementById("form-salida-alimento").reset();
                $('#modalSalidaAlimento').modal('hide');

                await loadAlimentos();
                await setFechaFiltroAlimentos();
                await notificarStockBajo();
                console.log("Stock y movimientos actualizados en la interfaz.");
            } else {
                showToast(result.message || "Error al registrar la salida.", 'ERROR');
            }
        } catch (error) {
            console.error("Error en registrarSalida:", error);
            showToast("Error en la solicitud: " + error.message, 'ERROR');
        }
    };

    // Función para actualizar automáticamente el valor de merma
    const actualizarMermaAutomatica = () => {
        const cantidadTotal = parseFloat(document.getElementById('cantidad-salida')?.value || 0);
        const uso = parseFloat(document.getElementById('uso')?.value || 0);
        const mermaField = document.getElementById('merma');

        // Calcular merma automáticamente y ajustar valores si se exceden los límites
        if (cantidadTotal >= uso) {
            const mermaCalculada = (cantidadTotal - uso).toFixed(2);

            // Si la merma calculada es mayor que la cantidad total, ajustarla
            if (parseFloat(mermaCalculada) > cantidadTotal) {
                mermaField.value = cantidadTotal;
            } else if (parseFloat(mermaCalculada) < 0) {
                // Si la merma es negativa, establecerla en cero
                mermaField.value = 0;
            } else {
                mermaField.value = mermaCalculada;
            }
        } else {
            mermaField.value = 0;
            console.warn("El valor de uso no puede ser mayor que la cantidad total.");
        }
    };

    // Añadir el evento al campo de uso para calcular automáticamente la merma
    document.getElementById("uso").addEventListener("input", actualizarMermaAutomatica);

    // Añadir el evento al campo de cantidad total para recalcular automáticamente la merma cuando cambia la cantidad
    document.getElementById("cantidad-salida").addEventListener("input", actualizarMermaAutomatica);

    // Añadir el evento al botón de salida
    document.getElementById("guardarSalida").addEventListener("click", registrarSalida);





    // Función para eliminar un alimento
    // Función para eliminar un alimento
    window.eliminarAlimento = async (idAlimento) => {
        if (await ask('¿Estás seguro de que deseas eliminar este alimento?')) {
            const data = new URLSearchParams();
            data.append('operation', 'eliminar');
            data.append('idAlimento', idAlimento);

            try {
                // Realizar la solicitud al backend
                const response = await fetch('../../controllers/alimento.controller.php', {
                    method: "POST",
                    body: data
                });

                const result = JSON.parse(await response.text());

                // Validar y mostrar el resultado de la operación
                if (result.status === "success" && result.data && result.data.status === "success") {
                    mostrarMensajeDinamico(result.data.message, 'SUCCESS');
                    loadAlimentos(); // Recargar la lista de alimentos
                } else {
                    mostrarMensajeDinamico(result.data?.message || result.message || "Error en la operación.", 'ERROR');
                }
            } catch (error) {
                mostrarMensajeDinamico("Error en la solicitud: " + error.message, 'ERROR');
            }
        }
    };


    // Cargar todos los datos al inicio
    cargarLotes();
    cargarTiposAlimento();
    loadAlimentos();
    
    loadCategoriaEquinos();
    notificarStockBajo();

    
  });
</script>
