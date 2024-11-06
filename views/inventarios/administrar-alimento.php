<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
  <!-- Título principal -->
  <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">
    Gestionar Alimentos
  </h1>

  <!-- Formulario para Registrar Nuevo Alimento -->
  <div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
      <h5 class="text-center"><i class="fas fa-apple-alt" style="color: #3498db;"></i> Registrar Nuevo Alimento</h5>
    </div>
    <div class="card-body" style="background-color: #f9f9f9;">
      <form action="" id="form-registrar-alimento" autocomplete="off">
        <div class="row g-3">
          <!-- Campos del formulario para registrar alimento -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" name="nombreAlimento" id="nombreAlimento" class="form-control" required autofocus>
              <label for="nombreAlimento"><i class="fas fa-seedling" style="color: #3498db;"></i> Nombre del Alimento</label>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-floating">
              <select id="tipoAlimento" name="tipoAlimento" class="form-select" required>
                <option value="" disabled selected>Seleccione el tipo de alimento</option>

                <!-- Separador visual para Granos -->
                <option disabled>─────────── Cereales y Granos ───────────</option>
                <option value="Grano">🌾 Grano (Avena, Cebada, Maíz, Trigo)</option>

                <!-- Separador visual para Heno y Forraje -->
                <option disabled>───────── Heno y Forraje ─────────</option>
                <option value="Heno">🌿 Heno (Alfalfa, Ryegrass, Timothy)</option>
                <option value="Forraje">🌿 Forraje fresco (Alfalfa, Ryegrass, Festuca)</option>

                <!-- Separador visual para Suplementos y Concentrados -->
                <option disabled>─────── Suplementos y Concentrados ───────</option>
                <option value="Suplemento">💊 Suplemento (Vitaminas, Minerales, Proteínas)</option>
                <option value="Concentrado">🧬 Concentrado (Potrillos, Caballos preñados)</option>

                <!-- Separador visual para Fibras -->
                <option disabled>─────────── Fibras ───────────</option>
                <option value="Fibras">🪵 Fibras (Pulpa de remolacha, Paja)</option>

                <!-- Separador visual para Líquidos y Complementos -->
                <option disabled>──────── Líquidos y Complementos ────────</option>
                <option value="Líquido">💧 Líquido (Aceite de linaza, Melaza)</option>
                <option value="Complemento">🥕 Complemento (Zanahorias, Manzanas)</option>

              </select>
              <label for="tipoAlimento"><i class="fas fa-carrot" style="color: #3498db;"></i> Tipo de Alimento</label>
            </div>
          </div>

          <!-- Estilo CSS para mejorar la apariencia visual -->
          <style>
            option[disabled] {
              color: #95a5a6;
              font-weight: bold;
              font-style: italic;
              background-color: #ecf0f1;
              padding: 10px 0;
            }

            option {
              padding: 8px;
            }
          </style>

          <div class="col-md-4">
            <div class="form-floating">
              <input type="number" name="stockActual" id="stockActual" class="form-control" required min="0">
              <label for="stockActual"><i class="fas fa-weight" style="color: #3498db;"></i> Stock Actual</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="number" name="stockMinimo" id="stockMinimo" class="form-control" value="10" required min="0">
              <label for="stockMinimo"><i class="fas fa-exclamation-triangle" style="color: #e74c3c;"></i> Stock Mínimo</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <select id="unidadMedida" name="unidadMedida" class="form-select" required>
                <option value="">Seleccione la Unidad de Medida</option>
              </select>
              <label for="unidadMedida"><i class="fas fa-balance-scale" style="color: #3498db;"></i> Unidad de Medida</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="number" step="0.01" name="costo" id="costo" class="form-control" required>
              <label for="costo"><i class="fas fa-dollar-sign" style="color: #3498db;"></i> Costo</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" name="lote" id="lote" class="form-control" value="LOTE-"  required>
              <label for="lote"><i class="fas fa-box" style="color: #3498db;"></i> Lote</label>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-floating">
              <input type="date" name="fechaCaducidad" id="fechaCaducidad" class="form-control" required>
              <label for="fechaCaducidad"><i class="fas fa-calendar-alt" style="color: #3498db;"></i> Fecha de Caducidad</label>
            </div>
          </div>

          <div id="mensaje" style="margin-top: 10px; color: green; font-weight: bold;"></div>

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
    <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
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
    <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
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
                      <input type="number" name="cantidad" id="cantidad-salida" class="form-control" required min="1">
                      <label for="cantidad-salida">Cantidad</label>
                  </div>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <select id="tipoEquinoMovimiento" name="idTipoEquino" class="form-select" required>
                    <option value="">Seleccione Tipo de Equino</option>
                  </select>
                  <label for="tipoEquinoMovimiento">Tipo de Equino</label>
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
                      <th>Cantidad</th>
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
    const tipoEquinoMovimiento = document.querySelector("#tipoEquinoMovimiento");
    const mensajeDiv = document.querySelector("#mensaje");  // Div para mostrar los mensajes dinámicos

    // Elementos del tipo de alimento y unidad de medida para ambos modales
    const tipoAlimentoElementRegistrar = document.getElementById('tipoAlimento');
    const unidadMedidaElementRegistrar = document.getElementById('unidadMedida');
    
    const unidadMedidaSelectEntrada = document.getElementById('unidadMedidaEntrada');
    const unidadMedidaSelect = document.querySelector("#unidadMedidaSalida");

    // Elementos de fecha de caducidad e ingreso
    const fechaCaducidadElement = document.getElementById('fechaCaducidad');
    
    const loteInput = document.querySelector('#lote');
    const nombreAlimentoInput = document.querySelector('#nombreAlimento');

    // Asegúrate de que los elementos existen antes de acceder a sus propiedades
    if (!loteInput || !nombreAlimentoInput) {
        console.error("El campo 'lote' o 'nombreAlimento' no está definido.");
    } else {
        // Si existen, accede a sus propiedades
        console.log(loteInput.value, nombreAlimentoInput.value);
    }


    
    // **Función para mostrar notificaciones en el div `mensaje`**
    // **Función para mostrar notificaciones en el div `mensaje`**
    const mostrarMensajeDinamico = (mensaje, tipo = 'INFO') => {
        const mensajeDiv = document.getElementById('mensaje'); // Asegúrate de tener un div con el id 'mensaje'
        
        if (mensajeDiv) {
            // Colores y iconos según el tipo de mensaje
            const estilos = {
                'INFO': { color: '#3178c6', bgColor: '#e7f3ff', icon: 'ℹ️' },
                'SUCCESS': { color: '#3c763d', bgColor: '#dff0d8', icon: '✅' },
                'ERROR': { color: '#a94442', bgColor: '#f2dede', icon: '❌' },
                'WARNING': { color: '#8a6d3b', bgColor: '#fcf8e3', icon: '⚠️' }
            };

            // Obtener los estilos correspondientes al tipo de mensaje
            const estilo = estilos[tipo] || estilos['INFO'];

            // Aplicar estilos al contenedor del mensaje
            mensajeDiv.style.color = estilo.color;
            mensajeDiv.style.backgroundColor = estilo.bgColor;
            mensajeDiv.style.fontWeight = 'bold';
            mensajeDiv.style.padding = '15px';
            mensajeDiv.style.marginBottom = '15px';
            mensajeDiv.style.border = `1px solid ${estilo.color}`;
            mensajeDiv.style.borderRadius = '8px';
            mensajeDiv.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
            mensajeDiv.style.display = 'flex';
            mensajeDiv.style.alignItems = 'center';

            // Mostrar el mensaje con un icono
            mensajeDiv.innerHTML = `<span style="margin-right: 10px; font-size: 1.2em;">${estilo.icon}</span>${mensaje}`;

            // Eliminar el mensaje después de 5 segundos
            setTimeout(() => {
                mensajeDiv.innerHTML = '';
                mensajeDiv.style.border = 'none';
                mensajeDiv.style.boxShadow = 'none';
                mensajeDiv.style.backgroundColor = 'transparent';
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


    // Función para cargar unidades de medida cuando se selecciona un alimento en la entrada
    alimentoSelectEntrada.addEventListener("change", async function() {
        const nombreAlimento = this.value;  // Capturar el alimento seleccionado

        if (nombreAlimento) {
            try {
                const response = await fetch(`../../controllers/alimento.controller.php?operation=getUnidadesMedida&nombreAlimento=${nombreAlimento}`, {
                    method: 'GET'
                });
                const result = await response.json();

                if (result.status === "success") {
                    // Limpiar las opciones anteriores
                    unidadMedidaSelectEntrada.innerHTML = '';

                    // Usar un Set para evitar duplicados
                    const unidadesUnicas = new Set(result.data.unidadesMedida);

                    // Agregar las nuevas opciones de unidades de medida al select
                    unidadesUnicas.forEach((unidad) => {
                        unidadMedidaSelectEntrada.innerHTML += `<option value="${unidad}">${unidad}</option>`;
                    });

                    // Si no hay unidades de medida, mostrar un mensaje
                    if (unidadesUnicas.size === 0) {
                        unidadMedidaSelectEntrada.innerHTML = '<option value="">No hay unidades disponibles</option>';
                    }
                } else {
                    console.error("Error al cargar las unidades de medida:", result.message);
                    unidadMedidaSelectEntrada.innerHTML = '<option value="">Error al cargar unidades</option>';
                }
            } catch (error) {
                unidadMedidaSelectEntrada.innerHTML = '<option value="">Error en la solicitud</option>';
            }
        } else {
            // Si no se ha seleccionado un alimento, limpiar el select de unidades de medida
            unidadMedidaSelectEntrada.innerHTML = '<option value="">Seleccione un alimento primero</option>';
        }
    });


    // Función para cargar unidades de medida cuando se selecciona un alimento de salida
    alimentoSelectSalida.addEventListener("change", async function() {
        const nombreAlimento = this.value;  // Capturar el alimento seleccionado

        if (nombreAlimento) {
            try {
                const response = await fetch(`../../controllers/alimento.controller.php?operation=getUnidadesMedida&nombreAlimento=${nombreAlimento}`, {
                    method: 'GET'
                });
                const result = await response.json();

                if (result.status === "success") {
                    // Limpiar las opciones anteriores
                    unidadMedidaSelect.innerHTML = '';

                    // Usar un Set para evitar duplicados
                    const unidadesUnicas = new Set(result.data.unidadesMedida);

                    // Agregar las nuevas opciones de unidades de medida al select
                    unidadesUnicas.forEach((unidad) => {
                        unidadMedidaSelect.innerHTML += `<option value="${unidad}">${unidad}</option>`;
                    });

                    // Si no hay unidades de medida, mostrar un mensaje
                    if (unidadesUnicas.size === 0) {
                        unidadMedidaSelect.innerHTML = '<option value="">No hay unidades disponibles</option>';
                    }
                } else {
                    unidadMedidaSelect.innerHTML = '<option value="">Error al cargar unidades</option>';
                }
            } catch (error) {
                unidadMedidaSelect.innerHTML = '<option value="">Error en la solicitud</option>';
            }
        } else {
            // Si no se ha seleccionado un alimento, limpiar el select de unidades de medida
            unidadMedidaSelect.innerHTML = '<option value="">Seleccione un alimento primero</option>';
        }
    });

    

    if (cantidadEntrada) {
      cantidadEntrada.addEventListener("input", (e) => {
        if (e.target.value < 0) e.target.value = 0; // Establecer el valor mínimo a 1
      });
    }

  


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

        const textResponse = await response.text();

        // Intentar convertir el texto en JSON
        const parsedResponse = JSON.parse(textResponse);

        // Verificar si la respuesta es exitosa y contiene los datos de alimentos
        if (parsedResponse.status === 'success' && Array.isArray(parsedResponse.data)) {
          const alimentos = parsedResponse.data;

          // Limpiar la tabla antes de añadir contenido nuevo
          alimentosTable.innerHTML = alimentos.map(alim => `
            <tr>
              <td>${alim.idAlimento}</td>
              <td>${alim.nombreAlimento}</td>
              <td>${alim.tipoAlimento}</td>
              <td>${alim.unidadMedida}</td>
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

          // Usar un Set para evitar duplicados en los selects
          const uniqueAlimentos = new Set();

          // Limpiar los selects antes de añadir nuevas opciones
          alimentoSelectEntrada.innerHTML = '<option value="">Seleccione un Alimento</option>';
          alimentoSelectSalida.innerHTML = '<option value="">Seleccione un Alimento</option>';

          // Añadir alimentos únicos a los selects
          alimentos.forEach(alim => {
            if (!uniqueAlimentos.has(alim.nombreAlimento)) {
              uniqueAlimentos.add(alim.nombreAlimento); // Añadir al set para evitar duplicados

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




    // Función para cargar las categorías de equinos
    // Función para cargar los tipos de equinos
    const loadTipoEquinos = async () => {
      try {
        // Hacemos la solicitud GET con los parámetros en la URL
        const response = await fetch('../../controllers/alimento.controller.php?operation=getTipoEquinos', {
          method: "GET"
        });

        const textResponse = await response.text();

        // Intentar convertir el texto en JSON
        const parsedResponse = JSON.parse(textResponse);

        // Verificar si la respuesta es exitosa y contiene los datos de tipos de equinos
        if (parsedResponse.status === 'success' && Array.isArray(parsedResponse.data)) {
          const tiposEquinos = parsedResponse.data;

          // Limpiar el select antes de añadir contenido nuevo
          tipoEquinoMovimiento.innerHTML = '<option value="">Seleccione Tipo de Equino</option>';

          // Añadir cada tipo de equino al select
          tiposEquinos.forEach(tipo => {
            const option = document.createElement('option');
            option.value = tipo.idTipoEquino; // Usamos el idTipoEquino como valor
            option.textContent = tipo.tipoEquino; // Mostramos el tipo de equino
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

    // Código adicional en el frontend para mostrar el valor seleccionado
    tipoEquinoMovimiento.addEventListener("change", (e) => {
        console.log("Tipo de equino seleccionado (id):", e.target.value); // Mostrar el valor seleccionado
    });




    // Función para validar si el lote ya existe para el mismo alimento y unidad de medida
    async function validarLote() {
        // Verificar si los inputs existen en el DOM
        if (!loteInput || !nombreAlimentoInput || !unidadMedidaElementRegistrar) {
            console.error('Uno o más elementos de entrada no están definidos.');
            mostrarMensajeDinamico('Error: Uno o más campos están vacíos o no existen.', 'ERROR');
            return false;
        }

        // Obtener los valores de lote, nombreAlimento y unidadMedida
        const lote = loteInput.value.trim();
        const nombreAlimento = nombreAlimentoInput.value.trim();
        const unidadMedida = unidadMedidaElementRegistrar.value.trim();  // Usar la variable correcta

        // Verificar si los campos están vacíos
        if (!lote || !nombreAlimento || !unidadMedida) {
            mostrarMensajeDinamico('El lote, nombre del alimento y la unidad de medida no pueden estar vacíos.', 'ERROR');
            return false;
        }

        try {
            // Hacer una petición al servidor para verificar si el lote ya está registrado para el mismo alimento y unidad de medida
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

            // Si hay un error en la validación (combinación ya existente o lote con unidad de medida diferente)
            if (result.status === 'error') {
                mostrarMensajeDinamico(result.message, 'ERROR');
                return false;
            }

            // Si la combinación es válida
            return true;

        } catch (error) {
            mostrarMensajeDinamico('Error al verificar el lote: ' + error.message, 'ERROR');
            return false;
        }
    }


    // **Función para registrar un nuevo alimento**
    if (formRegistrarAlimento) {
        formRegistrarAlimento.addEventListener("submit", async (event) => {
            event.preventDefault();

            console.log("Formulario enviado. Iniciando validaciones...");

            if (!validarFechaCaducidad()) {
                mostrarMensajeDinamico('Error en las fechas de caducidad.', 'ERROR');
                console.log("Error en la validación de la fecha de caducidad.");
                return;
            }

            console.log("Validando el lote...");
            const loteValido = await validarLote(loteInput, nombreAlimentoInput);
            if (!loteValido) {
                mostrarMensajeDinamico('Lote inválido o ya registrado. Verifica los datos.', 'ERROR');
                console.log("Error en la validación del lote.");
                return;
            }
            console.log("Lote válido.");

            const formData = new FormData(formRegistrarAlimento);
            const stockActual = parseFloat(formData.get('stockActual'));
            const stockMinimo = parseFloat(formData.get('stockMinimo'));

            if (stockMinimo > stockActual) {
                mostrarMensajeDinamico("El stock mínimo no puede ser mayor que el stock actual.", 'ERROR');
                console.log("Error: Stock mínimo mayor que el stock actual.");
                return;
            }
            console.log("Stock válido.");

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
                console.log("El usuario canceló la operación.");2314105089
            }
        });
    }
    
    //historial de movimientos
    const loadHistorialMovimientos = async () => {
        try {
            console.log("Iniciando carga de historial de movimientos...");

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

            console.log(`Rango de fechas: ${fechaInicio} a ${fechaFin}`);

            // Configuración de la solicitud para Entradas
            const entradasURL = `../../controllers/alimento.controller.php?operation=historial&tipoMovimiento=Entrada&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`;
            console.log(`Realizando solicitud GET para Entradas en: ${entradasURL}`);

            const responseEntradas = await fetch(entradasURL, { method: "GET" });
            console.log("Respuesta de la solicitud para Entradas recibida.");

            const parsedEntradas = await responseEntradas.json();
            console.log("Datos de Entradas parseados:", parsedEntradas);

            // Verificar que haya datos para Entradas y cargarlos en DataTable
            if (parsedEntradas.status === 'success' && Array.isArray(parsedEntradas.data)) {
                if (parsedEntradas.data.length > 0) {
                    console.log("Entradas encontradas, inicializando DataTable para Entradas...");
                    $('#tabla-entradas').DataTable().clear().destroy();
                    $('#tabla-entradas').DataTable({
                        data: parsedEntradas.data,
                        columns: [
                            { data: 'idAlimento' },
                            { data: 'nombreAlimento' },
                            { data: 'tipoAlimento' },
                            { data: 'unidadMedida' },
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
                    console.log("No se encontraron Entradas en el rango de fechas seleccionado.");
                }
            } else {
                console.warn("La respuesta para Entradas no contiene datos válidos:", parsedEntradas.data);
            }

            // Configuración de la solicitud para Salidas
            const salidasURL = `../../controllers/alimento.controller.php?operation=historial&tipoMovimiento=Salida&fechaInicio=${fechaInicio}&fechaFin=${fechaFin}`;
            console.log(`Realizando solicitud GET para Salidas en: ${salidasURL}`);

            const responseSalidas = await fetch(salidasURL, { method: "GET" });
            console.log("Respuesta de la solicitud para Salidas recibida.");

            const parsedSalidas = await responseSalidas.json();
            console.log("Datos de Salidas parseados:", parsedSalidas);

            // Verificar que haya datos para Salidas y cargarlos en DataTable
            if (parsedSalidas.status === 'success' && Array.isArray(parsedSalidas.data)) {
                if (parsedSalidas.data.length > 0) {
                    console.log("Salidas encontradas, inicializando DataTable para Salidas...");
                    $('#tabla-salidas').DataTable().clear().destroy();
                    $('#tabla-salidas').DataTable({
                        data: parsedSalidas.data,
                        columns: [
                            { data: 'idAlimento' },
                            { data: 'nombreAlimento' },
                            { data: 'tipoEquino' },
                            { data: 'cantidad' },
                            { data: 'unidadMedida' },
                            { data: 'merma' },
                            { data: 'lote' },
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
                    console.log("No se encontraron Salidas en el rango de fechas seleccionado.");
                }
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





   
    // Función dinámica para cambiar las unidades de medida según el tipo de alimento
    const actualizarOpcionesUnidadMedida = (tipoAlimento, unidadMedidaSelect) => {
      // Limpiar las opciones anteriores
      unidadMedidaSelect.innerHTML = '';

      switch(tipoAlimento) {
        case 'Grano':
        case 'Avena':
        case 'Cebada':
          unidadMedidaSelect.innerHTML += '<option value="Kilos">Kilos</option>';
          unidadMedidaSelect.innerHTML += '<option value="Gramos">Gramos</option>';
          break;
        
        case 'Heno':
        case 'Forraje':
          unidadMedidaSelect.innerHTML += '<option value="Kilos">Kilos</option>';
          unidadMedidaSelect.innerHTML += '<option value="Fardos">Fardos</option>';
          break;
        
        case 'Concentrado':
        case 'Suplemento':
          unidadMedidaSelect.innerHTML += '<option value="Gramos">Gramos</option>';
          unidadMedidaSelect.innerHTML += '<option value="Kilos">Kilos</option>';
          break;
        
        case 'Líquido':
          unidadMedidaSelect.innerHTML += '<option value="Litros">Litros</option>';
          unidadMedidaSelect.innerHTML += '<option value="Mililitros">Mililitros</option>';
          break;

        case 'Subproducto':
        case 'Fibras':
          unidadMedidaSelect.innerHTML += '<option value="Kilos">Kilos</option>';
          unidadMedidaSelect.innerHTML += '<option value="Toneladas">Toneladas</option>';
          break;

        default:
          unidadMedidaSelect.innerHTML += '<option value="">Seleccione la unidad de medida</option>';
          break;
      }
    };


    // Aplicar la funcionalidad dinámica al cambiar el tipo de alimento
    tipoAlimentoElementRegistrar.addEventListener('input', () => {
      actualizarOpcionesUnidadMedida(tipoAlimentoElementRegistrar.value, unidadMedidaElementRegistrar);
    });



    // **Función para manejar la notificación de stock bajo/agotado**
    const notificarStockBajo = async () => {
      try {
        // Realizar la solicitud GET en lugar de POST
        const response = await fetch('../../controllers/alimento.controller.php?operation=notificarStockBajo', {
          method: "GET"
        });

        // Leer la respuesta y parsear a JSON
        const textResponse = await response.text();
        const result = JSON.parse(textResponse);

        // Verificar si hay datos y recorrer los resultados
        if (result.status === 'success' && result.data) {
          const { agotados, bajoStock } = result.data;

          // Mostrar notificaciones de alimentos agotados
          agotados.forEach(notificacion => {
            mostrarMensajeDinamico(notificacion.Notificacion, 'ERROR'); // Puedes usar 'ERROR' para más énfasis
          });

          // Mostrar notificaciones de alimentos con stock bajo
          bajoStock.forEach(notificacion => {
            mostrarMensajeDinamico(notificacion.Notificacion, 'WARNING');
          });
        } else if (result.status === 'info') {
          mostrarMensajeDinamico(result.message, 'INFO');
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
        // Seleccionar los elementos del DOM
        const cantidadField = document.getElementById('stockActual-entrada'); // Ajustado al ID correcto
        const loteField = document.getElementById('entradaLote');
        const alimentoSelectEntrada = document.getElementById('alimento-select-entrada');
        const unidadMedidaEntrada = document.getElementById('unidadMedidaEntrada');
        const formEntradaAlimento = document.querySelector("#form-entrada-alimento");

        // Validar si los elementos necesarios están en el DOM
        if (!cantidadField || !loteField || !alimentoSelectEntrada || !unidadMedidaEntrada) {
            showToast("Error en el formulario: faltan elementos.", 'ERROR');
            return;
        }

        // Obtener valores asegurándose de que los elementos existen
        const cantidad = parseFloat(cantidadField.value) || 0;
        const lote = loteField.value ? loteField.value : null;

        // Validaciones básicas
        if (!alimentoSelectEntrada.value) {
            showToast("Por favor, seleccione un alimento.", 'ERROR');
            return;
        }

        if (!unidadMedidaEntrada.value) {
            showToast("Seleccione una unidad de medida.", 'ERROR');
            return;
        }

        if (!cantidad || isNaN(cantidad) || cantidad <= 0) {
            showToast("Por favor, ingrese una cantidad válida.", 'ERROR');
            return;
        }

        // Confirmación del usuario
        if (await ask("¿Confirmar entrada de alimento?")) {
            console.log("Usuario confirmó la entrada de alimento.");

            const params = {
                operation: 'entrada',
                nombreAlimento: alimentoSelectEntrada.value,
                unidadMedida: unidadMedidaEntrada.value,
                lote: lote,
                cantidad: cantidad
            };

            const data = JSON.stringify(params);

            try {
                const response = await fetch('../../controllers/alimento.controller.php', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: data
                });

                const result = await response.json();

                if (result.status === "success") {
                    showToast(result.message || "Entrada registrada exitosamente.", 'SUCCESS');
                    formEntradaAlimento.reset();
                    $('#modalEntradaAlimento').modal('hide');

                    // Recargar los datos actualizados de alimentos y movimientos
                    await loadAlimentos();  
                    await loadHistorialMovimientos();
                } else {
                    showToast(result.message || "Error al registrar la entrada.", 'ERROR');
                }
            } catch (error) {
                showToast("Error en la solicitud: " + error.message, 'ERROR');
            }
        } else {
            console.log("El usuario canceló la operación.");
        }
    };



    // Función para manejar la salida de alimentos
    // Función para manejar la salida de alimentos
    const registrarSalida = async () => {
        const cantidadField = document.getElementById('cantidad-salida');
        const cantidad = parseFloat(cantidadField.value) || 0;
        
        // Validación adicional en JavaScript
        if (cantidad <= 0) {
            showToast("La cantidad debe ser mayor a 0.", 'ERROR');
            return;
        }

        const mermaField = document.getElementById('merma'); // Cambia a `merma` en lugar de `merma-salida`
        let merma = mermaField && mermaField.value ? parseFloat(mermaField.value) : 0; // Convertir a número o 0 si no hay valor

        const loteField = document.getElementById('salidaLote');
        const lote = loteField && loteField.value ? loteField.value : null;

        // Validaciones básicas con console.log para verificar cada paso
        console.log("Alimento seleccionado:", alimentoSelectSalida.value);
        if (!alimentoSelectSalida.value) {
            showToast("Por favor, seleccione un alimento.", 'ERROR');
            return;
        }

        console.log("Tipo de equino seleccionado:", tipoEquinoMovimiento.value);
        if (!tipoEquinoMovimiento.value) {
            showToast("Seleccione un tipo de equino para la salida.", 'ERROR');
            return;
        }

        console.log("Unidad de medida seleccionada:", unidadMedidaSalida.value);
        if (!unidadMedidaSalida.value) {
            showToast("Seleccione una unidad de medida.", 'ERROR');
            return;
        }

        console.log("Cantidad ingresada:", cantidad);
        if (!cantidad || isNaN(cantidad) || cantidad <= 0) {
            showToast("Por favor, ingrese una cantidad válida.", 'ERROR');
            return;
        }

        // Confirmación del usuario usando SweetAlert (ask)
        if (await ask("¿Confirmar salida de alimento?")) {
            const params = {
                operation: 'salida',
                nombreAlimento: alimentoSelectSalida.value,
                idTipoEquino: tipoEquinoMovimiento.value,
                unidadMedida: unidadMedidaSalida.value,
                lote: lote,
                cantidad: cantidad,
                merma: merma // Asegúrate de que `merma` tenga el valor capturado
            };

            // Log de los parámetros que se enviarán al servidor
            console.log("Parámetros a enviar:", params);

            const data = JSON.stringify(params);

            try {
                // Log para verificar la solicitud antes de enviarla
                console.log("Datos JSON a enviar:", data);

                const response = await fetch('../../controllers/alimento.controller.php', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: data
                });

                // Log para verificar la respuesta cruda antes de procesarla
                console.log("Respuesta sin procesar:", response);

                const result = await response.json();

                // Log para verificar el resultado después de convertir a JSON
                console.log("Resultado de la solicitud:", result);

                if (result.status === "success") {
                    showToast(result.message || "Salida registrada exitosamente.", 'SUCCESS');
                    formSalidaAlimento.reset();
                    $('#modalSalidaAlimento').modal('hide');

                    // Recargar los datos actualizados de alimentos y movimientos
                    await loadAlimentos();
                    await loadHistorialMovimientos();
                    // Llamar a notificarStockBajo después de registrar la salida
                    await notificarStockBajo(); // Aquí se verifica si el stock está bajo o agotado

                } else {
                    showToast(result.message || "Error al registrar la salida.", 'ERROR');
                }
            } catch (error) {
                console.error("Error en la solicitud fetch:", error);
                showToast("Error en la solicitud: " + error.message, 'ERROR');
            }
        } else {
            console.log("El usuario canceló la operación.");
        }
    };




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
    loadAlimentos();
    loadTipoEquinos();
    loadHistorialMovimientos();
    notificarStockBajo();

    // Eventos para botones de guardar
    const guardarEntradaBtn = document.querySelector("#guardarEntrada");
    if (guardarEntradaBtn) {
      guardarEntradaBtn.addEventListener("click", registrarEntrada);
    }

    const guardarSalidaBtn = document.querySelector("#guardarSalida");
    if (guardarSalidaBtn) {
      guardarSalidaBtn.addEventListener("click", registrarSalida);
    }
  });
</script>
