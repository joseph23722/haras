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
          <!-- Campos del formulario para registrar alimento -->
          <div class="col-md-4">
            <div class="form-floating">
              <input type="text" name="nombreAlimento" id="nombreAlimento" class="form-control" required>
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
            <th>Fecha Caducidad</th>
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

  <!-- Modal para Historial de Movimientos -->
  <div class="modal fade" id="modalHistorial" tabindex="-1" aria-labelledby="modalHistorialLabel" aria-hidden="true" >
    <div class="modal-dialog modal-xl"> <!-- Aumentamos el tamaño del modal -->
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title" id="modalHistorialLabel">Historial de Movimientos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Filtro de fechas con estilo -->
          <div class="row align-items-center mb-4">
            <div class="col-md-4">
              <label for="fechaInicio" class="form-label">Fecha de Inicio:</label>
              <input type="date" id="fechaInicio" class="form-control">
            </div>
            <div class="col-md-4">
              <label for="fechaFin" class="form-label">Fecha de Fin:</label>
              <input type="date" id="fechaFin" class="form-control">
            </div>
            <div class="col-md-4 d-flex justify-content-start align-items-end">
              <button type="button" id="buscarHistorial" class="btn btn-primary ms-2">Buscar</button>
            </div>
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
                <table id="tabla-entradas" class="table table-bordered table-hover" style="width:100%">
                  <thead class="table-light">
                    <tr>
                      <th style="width: 10%;">ID</th>
                      <th style="width: 24%;">Alimento</th> <!-- Más espacio para nombre del alimento -->
                      <th style="width: 15%;">TipoAlimento</th>
                      <th style="width: 15%;">Unidad</th>
                      <th style="width: 10%;">Cantidad</th>
                      <th style="width: 12%;">Lote</th>
                      <th style="width: 15%;">Fecha Caducidad</th>
                      <th style="width: 20%;">Fecha de Entrada</th>
                    </tr>
                  </thead>
                  <tbody id="historial-entradas-table"></tbody>
                </table>
              </div>
            </div>

            <!-- Tabla de Salidas -->
            <div class="tab-pane fade" id="salidas" role="tabpanel" aria-labelledby="salidas-tab">
              <div class="table-responsive">
                <table id="tabla-salidas" class="table table-bordered table-hover" style="width:100%">
                  <thead class="table-light">
                    <tr>
                      <th style="width: 10%;">ID</th>
                      <th style="width: 20%;">Alimento</th>
                      <th style="width: 15%;">Tipo Equino</th> <!-- Ampliamos esta columna -->
                      <th style="width: 9%;">Cantidad</th>
                      <th style="width: 8%;">Unidad</th>
                      <th style="width: 9%;">Merma</th>
                      <th style="width: 15%;">Lote</th>
                      <th style="width: 15%;">Fecha de Salida</th>
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
    const historialMovimientosTable = document.querySelector("#historial-movimientos-table");
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
                console.error("Error en la solicitud:", error.message);
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
                    console.error("Error al cargar las unidades de medida:", result.message);
                    unidadMedidaSelect.innerHTML = '<option value="">Error al cargar unidades</option>';
                }
            } catch (error) {
                console.error("Error en la solicitud:", error.message);
                unidadMedidaSelect.innerHTML = '<option value="">Error en la solicitud</option>';
            }
        } else {
            // Si no se ha seleccionado un alimento, limpiar el select de unidades de medida
            unidadMedidaSelect.innerHTML = '<option value="">Seleccione un alimento primero</option>';
        }
    });

    

    if (cantidadEntrada) {
      cantidadEntrada.addEventListener("input", (e) => {
        if (e.target.value < 0) e.target.value = 0;
      });
    }

    if (cantidadSalida) {
      cantidadSalida.addEventListener("input", (e) => {
        if (e.target.value < 0) e.target.value = 0;
      });
    }

    // **Fecha de Caducidad**: No permitir fechas pasadas
    if (fechaCaducidadElement) {
      fechaCaducidadElement.setAttribute('min', new Date().toISOString().split('T')[0]);
    }

    // Función para validar la fecha de caducidad
    const validarFechaCaducidad = () => {
      const fechaCaducidad = new Date(fechaCaducidadElement.value);
      const hoy = new Date();
      hoy.setHours(0, 0, 0, 0);

      if (fechaCaducidad < hoy) {
        mostrarMensajeDinamico("La fecha de caducidad no puede ser en el pasado.", 'ERROR');
        return false;
      }
      return true;
    };

    // Función para cargar los alimentos registrados
    const loadAlimentos = async () => {
      try {
        // Hacemos la solicitud GET con los parámetros en la URL
        const response = await fetch('../../controllers/alimento.controller.php?operation=getAllAlimentos', {
          method: "GET",
        });

        const textResponse = await response.text();

        // Intentar convertir el texto en JSON
        const parsedResponse = JSON.parse(textResponse);

        // Verificar si la respuesta es exitosa y contiene los datos de alimentos
        if (parsedResponse.status === 'success' && Array.isArray(parsedResponse.data)) {
          const alimentos = parsedResponse.data;

          // Limpiar las tablas antes de añadir contenido nuevo
          alimentosTable.innerHTML = alimentos.map(alim => `
            <tr>
              <td>${alim.idAlimento}</td>
              <td>${alim.nombreAlimento}</td>
              <td>${alim.tipoAlimento}</td>
              <td>${alim.unidadMedida}</td>
              <td>${alim.lote}</td>
              <td>${alim.stockActual}</td>
              <td>${alim.stockMinimo}</td>
              <td>${alim.fechaCaducidad}</td>
              <td class="text-center">
                <button class="btn btn-danger btn-sm" onclick="eliminarAlimento(${alim.idAlimento})">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          `).join('');

          // Usar un Set para evitar duplicados en los selects
          const uniqueAlimentos = new Set();

          // Limpiar el contenido de los selects antes de añadir nuevas opciones
          alimentoSelectEntrada.innerHTML = '<option value="">Seleccione un Alimento</option>';
          alimentoSelectSalida.innerHTML = '<option value="">Seleccione un Alimento</option>';

          // Añadir los alimentos únicos a los selects
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
        console.error("Error al cargar alimentos:", error);
        mostrarMensajeDinamico('Error al cargar alimentos.', 'ERROR');
      }
    };


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

    // Función para validar si el lote ya existe o es válido
    async function validarLote(loteInput) {
        const lote = loteInput.value.trim(); // Obtener el valor del campo lote

        // Verificar si el campo está vacío
        if (!lote) {
            mostrarMensajeDinamico('El lote no puede estar vacío.', 'ERROR');
            return false;
        }

        try {
            // Hacer una petición al servidor para verificar si el lote ya existe
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: 'POST',
                body: new URLSearchParams({
                    operation: 'verificarLote',  // Operación que maneja el servidor para verificar lotes
                    lote: lote
                })
            });

            const result = await response.json();

            // Si hay un error en la validación del lote
            if (result.status === 'error') {
                mostrarMensajeDinamico(result.message, 'ERROR');
                return false;
            }

            return true; // El lote es válido
        } catch (error) {
            mostrarMensajeDinamico('Error al verificar el lote: ' + error.message, 'ERROR');
            return false;
        }
    }



    // Función para registrar un nuevo alimento
    const loteInput = document.querySelector('#lote'); // Asegúrate de que este campo existe en el formulario

    if (formRegistrarAlimento) {
        formRegistrarAlimento.addEventListener("submit", async (event) => {
            event.preventDefault(); // Previene la recarga de la página

            // Validar que la fecha de caducidad sea correcta
            if (!validarFechaCaducidad()) {
                mostrarMensajeDinamico('Error en las fechas de caducidad.', 'ERROR');
                return;
            }

            // Validar que el lote no esté vacío o inválido
            const loteValido = await validarLote(loteInput);
            if (!loteValido) {
                mostrarMensajeDinamico('Lote inválido o ya registrado. Verifica los datos.', 'ERROR');
                return;
            }

            // Obtener los valores de stock actual y stock mínimo directamente del formulario
            const formData = new FormData(formRegistrarAlimento);
            const stockActual = parseFloat(formData.get('stockActual')); // Campo dentro del form
            const stockMinimo = parseFloat(formData.get('stockMinimo')); // Campo dentro del form

            // Validación para asegurar que el stock mínimo no sea mayor que el stock actual
            if (stockMinimo > stockActual) {
                mostrarMensajeDinamico("El stock mínimo no puede ser mayor que el stock actual.", 'ERROR');
                return;
            }

            // Confirmar la operación con el usuario solo para operaciones exitosas
            if (await ask("¿Confirmar registro de nuevo alimento?")) {
                const data = new URLSearchParams(formData); // Convertir el FormData a un formato URLSearchParams
                data.append('operation', 'registrar'); // Agregar la operación

                try {
                    const response = await fetch('../../controllers/alimento.controller.php', {
                        method: "POST",
                        body: data
                    });

                    const result = await response.json();

                    if (result.status === "success" && result.data.status === "success") {
                        mostrarMensajeDinamico(result.data.message, 'SUCCESS');
                        showToast(result.data.message, 'SUCCESS'); // Solo mostrar en Toast si es exitoso
                        formRegistrarAlimento.reset();
                        loadAlimentos();
                    } else {
                        mostrarMensajeDinamico(result.data.message || "Error en la operación.", 'ERROR');
                    }
                } catch (error) {
                    mostrarMensajeDinamico("Error en la solicitud: " + error.message, 'ERROR');
                }
            } else {
                mostrarMensajeDinamico('El usuario canceló la operación.', 'INFO'); // Información de que el usuario canceló
            }
        });
    }




    // Función para cargar el historial de movimientos de entradas y salidas
    const loadHistorialMovimientos = async () => {
        try {
            // Obtener valores de fecha de inicio y fin
            const fechaInicio = document.getElementById('fechaInicio').value;
            const fechaFin = document.getElementById('fechaFin').value;



            if (!fechaInicio || !fechaFin) {
                return;
            }

            // Cargar el historial de Entradas

            const responseEntradas = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                body: new URLSearchParams({
                    operation: 'historial',
                    tipoMovimiento: 'Entrada',
                    fechaInicio: fechaInicio,
                    fechaFin: fechaFin
                }) // Pasamos también las fechas
            });

            const textResponseEntradas = await responseEntradas.text();
            console.log('Respuesta recibida para entradas:', textResponseEntradas);

            const parsedEntradas = JSON.parse(textResponseEntradas); // Convertir a JSON
            console.log('Datos de entradas parseados:', parsedEntradas);

            // Cambia la validación para verificar "data" dentro de la respuesta
            if (Array.isArray(parsedEntradas.data) && parsedEntradas.data.length > 0) {
                console.log('Entradas encontradas, inicializando DataTable...');
                $('#tabla-entradas').DataTable().clear().destroy(); // Limpiar DataTable antes de recargar
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
                    // Aquí aplicamos la traducción utilizando tu archivo JSON
                    language: {
                        url: '/haras/data/es_es.json' // Ruta correcta al archivo JSON de traducción
                    }
                });
                mostrarMensajeDinamico('Historial de entradas cargado exitosamente.', 'SUCCESS');
            } else {
                console.log('No se encontraron entradas en el rango de fechas seleccionado.');
                mostrarMensajeDinamico('No se encontraron movimientos de entradas en el rango seleccionado.', 'WARNING');
            }

            // Cargar el historial de Salidas
            console.log('Cargando historial de salidas...');
            const responseSalidas = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                body: new URLSearchParams({
                    operation: 'historial',
                    tipoMovimiento: 'Salida',
                    fechaInicio: fechaInicio,
                    fechaFin: fechaFin
                }) // Pasamos las fechas también
            });

            const textResponseSalidas = await responseSalidas.text();
            console.log('Respuesta recibida para salidas:', textResponseSalidas);

            const parsedSalidas = JSON.parse(textResponseSalidas); // Convertir a JSON
            console.log('Datos de salidas parseados:', parsedSalidas);

            // Cambia la validación para verificar "data" dentro de la respuesta
            if (Array.isArray(parsedSalidas.data) && parsedSalidas.data.length > 0) {
                console.log('Salidas encontradas, inicializando DataTable...');
                $('#tabla-salidas').DataTable().clear().destroy(); // Limpiar DataTable antes de recargar
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
                    // Aquí aplicamos la traducción utilizando tu archivo JSON
                    language: {
                        url: '/haras/data/es_es.json' // Ruta correcta al archivo JSON de traducción
                    }
                });
                mostrarMensajeDinamico('Historial de salidas cargado exitosamente.', 'SUCCESS');
            } else {
                console.log('No se encontraron salidas en el rango de fechas seleccionado.');
                mostrarMensajeDinamico('No se encontraron movimientos de salidas en el rango seleccionado.', 'WARNING');
            }

        } catch (error) {
            console.error('Error al cargar historial de movimientos:', error);
            mostrarMensajeDinamico('Error al cargar historial de movimientos.', 'ERROR');
        }
    };



    // Vincular la función al botón de búsqueda
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
    const cargarLotes = async () => {
        const entradaLoteSelect = document.querySelector("#entradaLote");  // Select para entrada
        const salidaLoteSelect = document.getElementById('salidaLote');    // Select para salida

        try {
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: 'POST',
                body: new URLSearchParams({ operation: 'listarLotes' })  // Petición al controlador
            });

            // Asegurarse de obtener el resultado como JSON
            const result = await response.json();

            if (result.status === "success") {
                // Limpiar los selects de entrada y salida
                entradaLoteSelect.innerHTML = '<option value="">Seleccione un lote</option>';
                salidaLoteSelect.innerHTML = '<option value="">Seleccione un lote</option>';
                
                // Insertar cada lote en ambos selects
                result.data.forEach(lote => {
                    const optionEntrada = document.createElement("option");
                    optionEntrada.value = lote.lote;
                    optionEntrada.textContent = `${lote.lote} - ${lote.nombreAlimento}`;
                    entradaLoteSelect.appendChild(optionEntrada);

                    const optionSalida = document.createElement("option");
                    optionSalida.value = lote.lote;
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
      const stockActualInput = document.querySelector("#stockActual-entrada");
      const mensajeStock = document.querySelector("#mensaje-stock");

      console.log("Iniciando la función registrarEntrada"); // Log inicial

      // Validar el stock antes de proceder
      const validarStock = () => {
        const stockActual = parseFloat(stockActualInput.value) || 0;

        console.log("Stock actual:", stockActual); // Verificar el valor del stock actual
        console.log("Stock mínimo:", stockMinimo); // Verificar el valor del stock mínimo

        if (stockMinimo > stockActual) {
          mensajeStock.textContent = "El stock mínimo no puede ser mayor que el stock actual.";
          console.log("Stock inválido: El stock mínimo es mayor que el stock actual.");
          return false;
        } else {
          mensajeStock.textContent = ""; // Limpiar mensaje si está todo correcto
          console.log("Stock válido.");
          return true;
        }
      };

      // Verificar si el stock es válido antes de continuar
      if (!validarStock()) {
        showToast("El stock mínimo no puede ser mayor que el stock actual.", 'ERROR');
        console.log("Validación de stock fallida. Deteniendo la operación.");
        return;
      }

      if (!alimentoSelectEntrada.value) {
        showToast("Por favor, seleccione un alimento.", 'ERROR');
        console.log("No se seleccionó ningún alimento.");
        return;
      }

      // Confirmación del usuario usando SweetAlert (ask)
      if (await ask("¿Confirmar entrada de alimento?")) {
        console.log("Usuario confirmó la entrada de alimento."); // Confirmación de usuario

        const formData = new FormData(formEntradaAlimento);
        const data = new URLSearchParams(formData);
        data.append('operation', 'entrada');

        console.log("Datos enviados:", Array.from(formData.entries())); // Verificar los datos enviados

        try {
          const response = await fetch('../../controllers/alimento.controller.php', {
            method: "POST",
            body: data
          });

          const textResponse = await response.text();
          console.log("Respuesta cruda del servidor (texto):", textResponse); // Mostrar la respuesta cruda del servidor

          const result = JSON.parse(textResponse);
          console.log("Respuesta procesada (JSON):", result); // Verificar el JSON resultante

          // Aquí actualizamos el acceso al JSON con base en lo que devuelve realmente
          if (result.status === "success") {
            showToast(result.message, 'SUCCESS');
            formEntradaAlimento.reset();
            console.log("Formulario reseteado. Recargando alimentos y movimientos.");
            loadAlimentos();
            loadHistorialMovimientos();
          } else {
            showToast(result.message || "Error en la operación.", 'ERROR');
            console.log("Error en la operación:", result.message || "Error desconocido");
          }
        } catch (error) {
          showToast("Error en la solicitud: " + error.message, 'ERROR');
          console.error("Error en la solicitud:", error.message);
        }
      } else {
        console.log("El usuario canceló la operación.");
      }
    };



    // Función para manejar la salida de alimentos
    const registrarSalida = async () => {
        // Selecciona el campo de cantidad usando el ID correcto
        const cantidadField = document.getElementById('cantidad-salida');
        const cantidad = cantidadField.value;

        // Selecciona el campo de merma (si está presente)
        const mermaField = document.getElementById('merma-salida');
        let merma = mermaField && mermaField.value ? mermaField.value : 0;  // Si no hay valor, asigna 0 por defecto

        // Selecciona el campo de lote (si está presente)
        const loteField = document.getElementById('salidaLote');
        const lote = loteField && loteField.value ? loteField.value : null;  // Si no hay valor, asigna null

        // Verificar los valores antes de enviar
        console.log("Valor de la cantidad:", cantidad);
        console.log("Valor de la merma:", merma);
        console.log("Valor del lote:", lote);

        // Validaciones básicas
        if (!alimentoSelectSalida.value) {
            showToast("Por favor, seleccione un alimento.", 'ERROR');
            return;
        }

        if (!tipoEquinoMovimiento.value) {
            showToast("Seleccione un tipo de equino para la salida.", 'ERROR');
            return;
        }

        if (!unidadMedidaSalida.value) {
            showToast("Seleccione una unidad de medida.", 'ERROR');
            return;
        }

        // Validar la cantidad
        if (!cantidad || isNaN(cantidad) || cantidad <= 0) {
            showToast("Por favor, ingrese una cantidad válida.", 'ERROR');
            return;
        }

        // Confirmación del usuario usando SweetAlert (ask)
        if (await ask("¿Confirmar salida de alimento?")) {
            console.log("Usuario confirmó la salida de alimento.");

            // Crear un objeto params con los datos
            const params = {
                operation: 'salida', // Asegurarse de enviar el nombre de la operación correctamente
                nombreAlimento: alimentoSelectSalida.value,
                idTipoEquino: tipoEquinoMovimiento.value,
                unidadMedida: unidadMedidaSalida.value,
                lote: lote,  // Lote opcional, se envía como null si no se especifica
                cantidad: cantidad,
                merma: merma  // Asegurarse de que merma siempre se envíe (0 si está vacío)
            };

            console.log("Parámetros enviados:", params);

            // Convertimos el objeto en formato JSON para enviar por POST
            const data = JSON.stringify(params);

            try {
                // Realizar la solicitud al servidor
                const response = await fetch('../../controllers/alimento.controller.php', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: data
                });

                const textResponse = await response.text();
                console.log("Respuesta cruda del servidor (texto):", textResponse); // Mostrar la respuesta cruda del servidor

                // Ahora intentamos parsear el texto a JSON
                const result = JSON.parse(textResponse);
                console.log("Respuesta procesada (JSON):", result);

                // Verificar el estado del resultado y proceder
                if (result.status === "success") {
                    showToast(result.message || "Salida registrada exitosamente.", 'SUCCESS');
                    formSalidaAlimento.reset(); // Reiniciar el formulario
                    $('#modalSalidaAlimento').modal('hide'); // Cerrar el modal
                    loadAlimentos(); // Recargar la lista de alimentos
                    loadHistorialMovimientos(); // Recargar el historial de movimientos
                } else {
                    showToast(result.message || "Error al registrar la salida.", 'ERROR');
                }
            } catch (error) {
                console.error("Error en la solicitud:", error.message);
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
