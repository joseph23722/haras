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
    </div>
    <div class="card-body" style="background-color: #f9f9f9;">
      <form action="" id="form-registrar-alimento" autocomplete="off">
        <div class="row g-3">
          
          <!-- Campo: Nombre del Alimento -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" name="nombreAlimento" id="nombreAlimento" class="form-control" required>
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
              <input type="number" name="stockActual" id="stockActual" class="form-control" required min="0">
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
              <input type="number" step="0.01" name="costo" id="costo" class="form-control" required>
              <label for="costo"><i class="fas fa-dollar-sign" style="color: #3498db;"></i> Costo</label>
            </div>
          </div>

          <!-- Campo: Lote -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" name="lote" id="lote" class="form-control" value="LOTE-" required>
              <label for="lote"><i class="fas fa-box" style="color: #3498db;"></i> Lote</label>
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
      <button class="btn btn-outline-info btn-lg" style="border-color: #17a2b8;" data-bs-toggle="modal" data-bs-target="#modalHistorial">
        <i class="fas fa-history"></i> Ver Historial de Movimientos
      </button>
    </div>
  </div>

  <!-- Tabla de Alimentos Registrados -->
  <div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
      <h5 class="text-center"><i class="fas fa-database"></i> Alimentos Registrados</h5>
    </div>
    <div class="card-body" style="background-color: #f9f9f9;">
      <table class="table table-striped table-hover table-bordered">
        <thead style="background-color: #caf0f8; color: #003366;">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>U.M</th>
            <th>Lote</th>
            <th>Cantidad</th>
            <th>Stock minimo</th>
            <th>Costo</th>
            <th>Fecha Caducidad</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="alimentos-table"></tbody>
      </table>
    </div>
  </div>

  <!-- Modal para Movimientos de Entrada -->
  <div class="modal fade" id="modalEntradaAlimento" tabindex="-1" aria-labelledby="modalEntradaAlimentoLabel" aria-hidden="true">
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
  <div class="modal fade" id="modalSalidaAlimento" tabindex="-1" aria-labelledby="modalSalidaAlimentoLabel">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background: #ff6b6b; color: white;">
          <h5 class="modal-title" id="modalSalidaAlimentoLabel">Registrar Salida de Alimento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="" id="form-salida-alimento" autocomplete="off">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-floating">
                  <select id="alimento-select-salida" name="nombreAlimento" class="form-select" required>
                    <option value="">Seleccione un Alimento</option>
                  </select>
                  <label for="alimento-select-salida">Alimento</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="number" name="cantidad" id="cantidad-salida" class="form-control" required min="0">
                  <label for="cantidad-salida">Cantidad</label>
                </div>
              </div>

              <div class="col-md-6">
                  <div class="form-floating">
                      <select id="idEquino" name="idEquino" class="form-select" required>
                          <option value="">Seleccione Categoría de Equino</option>
                          <!-- Opciones se cargarán dinámicamente -->
                      </select>
                      <label for="idEquino">Categoría de Equino</label>
                  </div>
              </div>




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



              <div class="col-md-6">
                <div class="form-floating">
                  <input type="number" step="0.01" name="merma" id="merma" class="form-control">
                  <label for="merma">Merma</label>
                </div>
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

  <!-- Modal para Historial de Movimientos Mejorado -->
  <div class="modal fade" id="modalHistorial" tabindex="-1" aria-labelledby="modalHistorialLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title" id="modalHistorialLabel">Historial de Movimientos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
          <!-- Opciones de Filtrado Rápido -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
              <label for="filtroRango" class="me-2">Ver movimientos de:</label>
              <select id="filtroRango" class="form-select">
                <option value="hoy">Hoy</option>
                <option value="ultimaSemana">Última semana</option>
                <option value="ultimoMes">Último mes</option>
                <option value="todos">Todos</option>
              </select>
            </div>
            <button type="button" id="buscarHistorial" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
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

          <!-- Contenido de las pestañas -->
          <div class="tab-content">
            <!-- Tabla de Entradas -->
            <div class="tab-pane fade show active" id="entradas" role="tabpanel" aria-labelledby="entradas-tab">
              <div class="table-responsive">
                <table id="tabla-entradas" class="table table-bordered table-hover table-striped">
                  <thead class="table-primary">
                    <tr>
                      <th>ID</th>
                      <th>Alimento</th>
                      <th>Tipo Alimento</th>
                      <th>Unidad</th>
                      <th>Cantidad</th>
                      <th>Lote</th>
                      <th>Fecha Caducidad</th>
                      <th>Fecha de Entrada</th>
                    </tr>
                  </thead>
                  <tbody id="historial-entradas-table"></tbody>
                </table>
              </div>
            </div>

            <!-- Tabla de Salidas -->
            <div class="tab-pane fade" id="salidas" role="tabpanel" aria-labelledby="salidas-tab">
              <div class="table-responsive">
                <table id="tabla-salidas" class="table table-bordered table-hover table-striped">
                  <thead class="table-danger">
                    <tr>
                      <th>ID</th>
                      <th>Alimento</th>
                      <th>Tipo Equino</th>
                      <th>Cantidad Equino</th>
                      <th>Cantidad Salida</th>
                      <th>Unidad</th>
                      <th>Merma</th>
                      <th>Lote</th>
                      <th>Fecha de Salida</th>
                    </tr>
                  </thead>
                  <tbody id="historial-salidas-table"></tbody>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Incluye SweetAlert -->
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


    // Función para cargar tipos de alimento en el formulario de registro
    // Función para cargar tipos de alimento en el formulario de registro
    // Función de utilidad para realizar una solicitud GET y obtener datos
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

    const cargarUnidadesMedida = async (param, selectElement, byType = true) => {
        const operation = byType ? `obtenerUnidadesPorTipoAlimento&idTipoAlimento=${param}` : `getUnidadesMedida&nombreAlimento=${param}`;
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





    // Cambiar evento para seleccionar alimento y cargar unidades de medida relacionadas
    alimentoSelectEntrada.addEventListener("change", e => cargarUnidadesMedida(e.target.value, unidadMedidaSelectEntrada, false));
    alimentoSelectSalida.addEventListener("change", e => cargarUnidadesMedida(e.target.value, unidadMedidaSelectSalida, false));

    // Eventos de cambio para cargar las unidades según tipo o nombre de alimento
    tipoAlimentoElementRegistrar.addEventListener("change", e => cargarUnidadesMedida(e.target.value, unidadMedidaElementRegistrar));

    // Inicialización al cargar la página
    document.addEventListener("DOMContentLoaded", cargarTiposAlimento);






    



    








    
    // **Función para mostrar notificaciones en el div `mensaje`**
    const mostrarMensajeDinamico = (mensaje, tipo = 'INFO') => {
        const mensajeDiv = document.getElementById('mensaje'); // Asegúrate de tener un div con el id 'mensaje'
        
        if (mensajeDiv) {
            const colores = {
                'INFO': 'blue',
                'SUCCESS': 'green',
                'ERROR': 'red',
                'WARNING': 'orange'
            };
            
            // Estilos del mensaje
            mensajeDiv.style.color = colores[tipo] || 'black';
            mensajeDiv.style.fontWeight = 'bold';
            mensajeDiv.style.padding = '10px';
            mensajeDiv.style.marginBottom = '15px';
            mensajeDiv.style.border = `2px solid ${colores[tipo] || 'black'}`;
            mensajeDiv.style.backgroundColor = '#f9f9f9';
            
            // Mostrar el mensaje
            mensajeDiv.innerHTML = mensaje;

            // Eliminar el mensaje después de 5 segundos
            setTimeout(() => {
                mensajeDiv.innerHTML = '';
                mensajeDiv.style.border = 'none';
            }, 5000);
        } else {
            console.warn('El contenedor de mensajes no está presente en el DOM.');
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
    const loadAlimentos = async () => {
        try {
            // Hacemos la solicitud GET para obtener los alimentos registrados
            const response = await fetch('../../controllers/alimento.controller.php?operation=getAllAlimentos', {
                method: 'GET',
            });

            const parsedResponse = await response.json();

            // Verificar si la respuesta es exitosa y contiene los datos de alimentos
            if (parsedResponse.status === 'success' && Array.isArray(parsedResponse.data)) {
                const alimentos = parsedResponse.data;

                // Limpiar la tabla antes de añadir contenido nuevo
                alimentosTable.innerHTML = alimentos.map(alim => `
                    <tr>
                        <td>${alim.idAlimento}</td>
                        <td>${alim.nombreAlimento}</td>
                        <td>${alim.nombreTipoAlimento}</td>
                        <td>${alim.unidadMedidaNombre}</td>
                        <td>${alim.lote}</td>
                        <td>${alim.stockActual}</td>
                        <td>${alim.stockMinimo}</td>
                        <td>${alim.costo}</td>
                        <td>${alim.fechaCaducidad}</td>
                        <td>${alim.estado}</td>
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm" onclick="eliminarAlimento(${alim.idAlimento})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');

                // Actualizar los selects de entrada y salida sin duplicados
                const uniqueAlimentos = new Set();
                alimentoSelectEntrada.innerHTML = '<option value="">Seleccione un Alimento</option>';
                alimentoSelectSalida.innerHTML = '<option value="">Seleccione un Alimento</option>';

                alimentos.forEach(alim => {
                    if (!uniqueAlimentos.has(alim.nombreAlimento)) {
                        uniqueAlimentos.add(alim.nombreAlimento);

                        // Añadir alimento a select de entrada
                        const optionEntrada = document.createElement('option');
                        optionEntrada.value = alim.nombreAlimento;
                        optionEntrada.textContent = alim.nombreAlimento;
                        alimentoSelectEntrada.appendChild(optionEntrada);

                        // Añadir alimento a select de salida
                        const optionSalida = document.createElement('option');
                        optionSalida.value = alim.nombreAlimento;
                        optionSalida.textContent = alim.nombreAlimento;
                        alimentoSelectSalida.appendChild(optionSalida);
                    }
                });
            } else {
                mostrarMensajeDinamico('No se encontraron alimentos.', 'INFO');
                alimentosTable.innerHTML = '<tr><td colspan="9">No se encontraron alimentos.</td></tr>';
            }
        } catch (error) {
            console.error('Error al cargar alimentos:', error);
            mostrarMensajeDinamico('Error al cargar alimentos.', 'ERROR');
        }
    };



    // Función para cargar los tipos de equinos
    // Función para cargar las categorías de equinos con sus cantidades en el select del modal
    // Función para cargar las categorías de equinos con sus cantidades en el select del modal
    const loadCategoriaEquinos = async () => {
        try {
            const response = await fetch('../../controllers/alimento.controller.php?operation=getTipoEquinos', {
                method: "GET"
            });

            const parsedResponse = await response.json();
            console.log("Respuesta de la API para cargar categorías de equinos:", parsedResponse);

            if (parsedResponse.status === 'success' && Array.isArray(parsedResponse.data)) {
                const categorias = parsedResponse.data;
                const idEquinoSelect = document.getElementById('idEquino');

                if (!idEquinoSelect) {
                    console.error("El select con ID 'idEquino' no existe en el DOM.");
                    return;
                }

                // Limpiar opciones previas
                idEquinoSelect.innerHTML = '<option value="">Seleccione Categoría de Equino</option>';

                // Agregar opciones y verificar que `idEquino` sea válido
                categorias.forEach(categoria => {
                    console.log("Verificando categoría:", categoria);  // Añadir este log

                    if (categoria.idEquino) {  // Asegurarse de que el idEquino existe y no está undefined
                        const option = document.createElement('option');
                        option.value = categoria.idEquino;  // Confirmar que `idEquino` se usa correctamente
                        option.textContent = `${categoria.Categoria} (${categoria.Cantidad})`;
                        idEquinoSelect.appendChild(option);
                    } else {
                        console.warn(`Categoría sin idEquino:`, categoria);  // Log en caso de un valor faltante
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


    
    
    //historial de movimientos
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

            // Configuración de la solicitud para Entradas
            const entradasURL = `../../controllers/alimento.controller.php?operation=historial&tipoMovimiento=Entrada&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`;
            const responseEntradas = await fetch(entradasURL, { method: "GET" });
            const parsedEntradas = await responseEntradas.json();

            // Verificar que haya datos para Entradas y cargarlos en DataTable
            if (parsedEntradas.status === 'success' && Array.isArray(parsedEntradas.data)) {
                $('#tabla-entradas').DataTable().clear().destroy();
                $('#tabla-entradas').DataTable({
                    data: parsedEntradas.data,
                    columns: [
                        { data: 'idAlimento' },
                        { data: 'nombreAlimento' },
                        { data: 'nombreTipoAlimento' },
                        { data: 'nombreUnidadMedida' },
                        { data: 'cantidad' },
                        { data: 'lote' },
                        { data: 'fechaCaducidad' },
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
            } else {
                console.warn("La respuesta para Entradas no contiene datos válidos:", parsedEntradas.data);
            }

            // Configuración de la solicitud para Salidas
            const salidasURL = `../../controllers/alimento.controller.php?operation=historial&tipoMovimiento=Salida&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`;
            const responseSalidas = await fetch(salidasURL, { method: "GET" });
            const parsedSalidas = await responseSalidas.json();

            // Verificar que haya datos para Salidas y cargarlos en DataTable
            if (parsedSalidas.status === 'success' && Array.isArray(parsedSalidas.data)) {
                $('#tabla-salidas').DataTable().clear().destroy();
                $('#tabla-salidas').DataTable({
                    data: parsedSalidas.data,
                    columns: [
                        { data: 'ID' },
                        { data: 'Alimento' },
                        { data: 'TipoEquino' },  // Tipo de equino según el estado
                        { data: 'CantidadEquino' },  // Cantidad de equinos por categoría
                        { data: 'Cantidad' },  // Cantidad de salida
                        { data: 'Unidad' },  // Unidad de medida
                        { data: 'Merma' },  // Merma (si aplica)
                        { data: 'Lote' },  // Lote del alimento
                        { data: 'FechaSalida' }  // Fecha del movimiento
                    ],
                    responsive: true,
                    autoWidth: false,
                    paging: true,
                    searching: true,
                    language: {
                        url: '/haras/data/es_es.json'
                    }
                });
            } else {
                console.warn("La respuesta para Salidas no contiene datos válidos:", parsedSalidas.data);
            }

        } catch (error) {
            console.error('Error al cargar historial de movimientos:', error);
            mostrarMensajeDinamico('Error al cargar historial de movimientos.', 'ERROR');
        }
    };



    // Vincular la función al cambio en el filtro de rango
    document.getElementById('filtroRango').addEventListener('change', loadHistorialMovimientos);
    document.getElementById('buscarHistorial').addEventListener('click', loadHistorialMovimientos);



    // **Función para manejar la notificación de stock bajo/agotado**
    const notificarStockBajo = async () => {
      try {
        const response = await fetch('../../controllers/alimento.controller.php', {
          method: "POST",
          body: new URLSearchParams({ operation: 'notificarStockBajo' })
        });

        const textResponse = await response.text();
        const result = JSON.parse(textResponse);

        if (Array.isArray(result)) {
          result.forEach(notificacion => {
            mostrarMensajeDinamico(notificacion.Notificacion, 'WARNING');
          });
        }
      } catch (error) {
        mostrarMensajeDinamico('Error al notificar stock bajo.', 'ERROR');
      }
    };

    // Función para cargar los lotes en los select de entrada y salida de alimentos
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
                    await loadHistorialMovimientos();
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







    // Función para registrar la salida de alimento
    // Función para registrar la salida de alimento
    const registrarSalida = async () => {
        try {
            const nombreAlimento = document.getElementById('alimento-select-salida')?.value || '';
            const cantidad = parseFloat(document.getElementById('cantidad-salida')?.value || 0);
            const idEquino = document.getElementById('idEquino')?.value || '';
            const unidadMedida = document.getElementById('unidadMedidaSalida')?.value || '';
            const lote = document.getElementById('salidaLote')?.value || '';
            const merma = parseFloat(document.getElementById('merma')?.value || 0);

            // Verificar que todos los datos necesarios estén completos
            if (!nombreAlimento || !cantidad || !idEquino || !unidadMedida || !lote || idEquino === 'undefined') {
                console.warn("Faltan datos necesarios o idEquino es undefined para registrar la salida.");
                showToast("Faltan datos necesarios para registrar la salida o idEquino no está definido.", 'WARNING');
                return;
            }

            const params = {
                operation: 'salida',
                nombreAlimento,
                cantidad,
                idEquino,
                unidadMedida,
                lote,
                merma
            };

            console.log("Parámetros enviados:", params);

            const response = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(params)
            });

            const result = await response.json();
            console.log("Respuesta de la API:", result);

            if (result.status === "success") {
                showToast(result.message || "Salida registrada exitosamente.", 'SUCCESS');
                document.getElementById("form-salida-alimento").reset();
                $('#modalSalidaAlimento').modal('hide');

                // Actualizar la lista de alimentos y el historial de movimientos
                await loadAlimentos();  // Actualiza el stock de los alimentos en la interfaz
                await loadHistorialMovimientos();  // Refresca el historial de movimientos
                console.log("Stock y movimientos actualizados en la interfaz.");
            } else {
                showToast(result.message || "Error al registrar la salida.", 'ERROR');
            }
        } catch (error) {
            console.error("Error en registrarSalida:", error);
            showToast("Error en la solicitud: " + error.message, 'ERROR');
        }
    };

    // Añadir el evento al botón de salida
    document.getElementById("guardarSalida").addEventListener("click", registrarSalida);





    





    // Función para eliminar un alimento
    window.eliminarAlimento = async (idAlimento) => {
      if (await ask('¿Estás seguro de que deseas eliminar este alimento?')) {
        const data = new URLSearchParams();
        data.append('operation', 'eliminar');
        data.append('idAlimento', idAlimento);

        try {
          const response = await fetch('../../controllers/alimento.controller.php', {
            method: "POST",
            body: data
          });

          const result = JSON.parse(await response.text());

          if (result.status === "success" && result.data && result.data.status === "success") {
            mostrarMensajeDinamico(result.data.message, 'SUCCESS');
            loadAlimentos();
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
    loadHistorialMovimientos();
    notificarStockBajo();

    
  });
</script>
