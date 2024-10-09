<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
  <!-- Título principal -->
  <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Gestionar
    Alimentos</h1>

  <!-- Botón para abrir el modal de registrar alimentos -->
  <div class="d-flex justify-content-end">
    <button class="btn btn-primary btn-lg mb-3" style="background-color: #007bff; border-color: #007bff;" data-bs-toggle="modal" data-bs-target="#modalRegistrarAlimento">
      <i class="fas fa-plus-circle"></i> Registrar Nuevo Alimento
    </button>
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
            <th>Stock Final</th>
            <th>Costo</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="alimentos-table"></tbody>
      </table>
    </div>
  </div>

  <!-- Botón para abrir el modal de movimientos de entrada -->
  <div class="d-flex justify-content-end mb-2">
    <button class="btn btn-success btn-lg me-2" style="background-color: #28a745; border-color: #28a745;" data-bs-toggle="modal" data-bs-target="#modalEntradaAlimento">
      <i class="fas fa-plus"></i> Registrar Entrada
    </button>
    <button class="btn btn-danger btn-lg" style="background-color: #dc3545; border-color: #dc3545;" data-bs-toggle="modal" data-bs-target="#modalSalidaAlimento">
      <i class="fas fa-minus"></i> Registrar Salida
    </button>
  </div>

  <!-- Modal para Registrar Nuevo Alimento -->
  <div class="modal fade" id="modalRegistrarAlimento" tabindex="-1" aria-labelledby="modalRegistrarAlimentoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header" style="background: #00b4d8; color: white;">
          <h5 class="modal-title" id="modalRegistrarAlimentoLabel">Registrar Nuevo Alimento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="" id="form-registrar-alimento" autocomplete="off">
            <div class="row g-3">
              <!-- Campos del formulario para registrar alimento -->
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text" name="nombreAlimento" id="nombreAlimento" class="form-control" required>
                  <label for="nombreAlimento">Nombre del Alimento</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text" id="tipoAlimento" name="tipoAlimento" class="form-control" required>
                  <label for="tipoAlimento">Tipo de Alimento</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="number" name="cantidad" id="cantidad" class="form-control" required min="0">
                  <label for="cantidad">Cantidad</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <select id="unidadMedida" name="unidadMedida" class="form-select" required>
                    <option value="">Seleccione la Unidad de Medida</option>
                  </select>
                  <label for="unidadMedida">Unidad de Medida</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="number" step="0.01" name="costo" id="costo" class="form-control" required>
                  <label for="costo">Costo</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text" name="lote" id="lote" class="form-control" required>
                  <label for="lote">Lote</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="date" name="fechaCaducidad" id="fechaCaducidad" class="form-control" required>
                  <label for="fechaCaducidad">Fecha de Caducidad</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="datetime-local" name="fechaIngreso" id="fechaIngreso" class="form-control" required>
                  <label for="fechaIngreso">Fecha de Ingreso</label>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary" form="form-registrar-alimento">Registrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para Movimientos de Entrada -->
  <div class="modal fade" id="modalEntradaAlimento" tabindex="-1" aria-labelledby="modalEntradaAlimentoLabel"
    aria-hidden="true">
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
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="number" name="cantidad" id="cantidad-entrada" class="form-control" required min="0">
                  <label for="cantidad-entrada">Cantidad</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text" name="lote" id="lote-entrada" class="form-control" required>
                  <label for="lote-entrada">Lote</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="date" name="fechaCaducidad" id="fechaCaducidad-entrada" class="form-control" required>
                  <label for="fechaCaducidad-entrada">Fecha de Caducidad</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <input type="text" name="tipoAlimento" id="tipoAlimentoEntrada" class="form-control" required>
                  <label for="tipoAlimento">Tipo de Alimento</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-floating">
                  <select id="unidadMedidaEntrada" name="unidadMedida" class="form-select" required>
                    <option value="">Seleccione la Unidad de Medida</option>
                  </select>
                  <label for="unidadMedida">Unidad de Medida</label>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-floating">
                  <input type="number" name="nuevoPrecio" id="nuevo-precio-entrada" class="form-control" step="0.01">
                  <label for="nuevo-precio-entrada">Nuevo Precio (opcional)</label>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-success" id="guardarEntrada">Guardar Entrada</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para Movimientos de Salida -->
  <div class="modal fade" id="modalSalidaAlimento" tabindex="-1" aria-labelledby="modalSalidaAlimentoLabel"
    aria-hidden="true">
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

  <!-- Historial de Movimientos -->
  <div class="card mb-4">
    <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
      <h5 class="text-center"><i class="fas fa-history"></i> Historial de Movimientos</h5>
    </div>
    <div class="card-body" style="background-color: #f9f9f9;">
      <table class="table table-striped table-hover table-bordered">
        <thead style="background-color: #caf0f8; color: #003366;">
          <tr>
            <th>ID</th>
            <th>Alimento</th>
            <th>Tipo Movimiento</th>
            <th>Cantidad</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody id="historial-movimientos-table"></tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once '../../footer.php'; ?>


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

    // Elementos del tipo de alimento y unidad de medida para ambos modales
    const tipoAlimentoElementRegistrar = document.getElementById('tipoAlimento');
    const unidadMedidaElementRegistrar = document.getElementById('unidadMedida');
    const tipoAlimentoElementEntrada = document.getElementById('tipoAlimentoEntrada');
    const unidadMedidaElementEntrada = document.getElementById('unidadMedidaEntrada');

    // Elementos de fecha de caducidad e ingreso
    const fechaCaducidadElement = document.getElementById('fechaCaducidad');
    const fechaIngresoElement = document.getElementById('fechaIngreso');

    // Validar cantidad positiva en movimientos de entrada y salida
    document.querySelector("#cantidad-entrada").addEventListener("input", (e) => {
      if (e.target.value < 0) e.target.value = 0;
    });
    document.querySelector("#cantidad-salida").addEventListener("input", (e) => {
      if (e.target.value < 0) e.target.value = 0;
    });

    // **Fecha de Caducidad**: No permitir fechas pasadas
    fechaCaducidadElement.setAttribute('min', new Date().toISOString().split('T')[0]);

    // Función para validar la fecha de caducidad
    const validarFechaCaducidad = () => {
      const fechaCaducidad = new Date(fechaCaducidadElement.value);
      const hoy = new Date();
      hoy.setHours(0, 0, 0, 0);

      if (fechaCaducidad < hoy) {
        alert("La fecha de caducidad no puede ser en el pasado.");
        return false;
      }
      return true;
    };

     // **Función para obtener la fecha y hora actuales en el formato correcto (local)**
     const obtenerFechaHoraActual = () => {
      const ahora = new Date();
      const anio = ahora.getFullYear();
      const mes = String(ahora.getMonth() + 1).padStart(2, '0');
      const dia = String(ahora.getDate()).padStart(2, '0');
      const hora = String(ahora.getHours()).padStart(2, '0');
      const minutos = String(ahora.getMinutes()).padStart(2, '0');

      return `${anio}-${mes}-${dia}T${hora}:${minutos}`;
    };

    // **Fecha de Ingreso**: Solo permitir la fecha y hora actuales
    const actualizarFechaIngreso = () => {
      const fechaActual = obtenerFechaHoraActual();
      fechaIngresoElement.value = fechaActual;
      fechaIngresoElement.setAttribute('readonly', true);
    };

    // Validar la fecha de ingreso
    const validarFechaIngreso = () => {
      const fechaIngreso = new Date(fechaIngresoElement.value);
      const ahora = new Date();

      if (fechaIngreso.toISOString().slice(0, 16) !== ahora.toISOString().slice(0, 16)) {
        alert("La fecha de ingreso debe ser la fecha y hora actuales.");
        return false;
      }
      return true;
    };

    // Actualizar la fecha de ingreso al abrir el modal
    document.querySelector("#modalRegistrarAlimento").addEventListener("shown.bs.modal", actualizarFechaIngreso);


    // Función para cargar los alimentos registrados
    const loadAlimentos = async () => {
        try {
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                body: new URLSearchParams({ operation: 'getAllAlimentos' })
            });

            const textResponse = await response.text();

            // Intentar convertir el texto en JSON
            const alimentos = JSON.parse(textResponse);

            if (Array.isArray(alimentos)) {
                // Limpiar las tablas antes de añadir contenido nuevo
                alimentosTable.innerHTML = alimentos.map(alim => `
                    <tr>
                        <td>${alim.idAlimento}</td>
                        <td>${alim.nombreAlimento}</td>
                        <td>${alim.stockFinal}</td>
                        <td>${alim.costo}</td>
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
                alimentosTable.innerHTML = '<tr><td colspan="5">No se encontraron alimentos.</td></tr>';
            }
        } catch (error) {
            console.error('Error al cargar alimentos:', error);
        }
    };

    // Función para cargar los tipos de equinos
    const loadTipoEquinos = async () => {
      try {
        const response = await fetch('../../controllers/alimento.controller.php', {
          method: "POST",
          body: new URLSearchParams({ operation: 'getTipoEquinos' })
        });

        const textResponse = await response.text();

        // Intentar convertir el texto en JSON
        const tipos = JSON.parse(textResponse);

        tipoEquinoMovimiento.innerHTML = '<option value="">Seleccione Tipo de Equino (Solo para salida)</option>';
        tipos.forEach(tipo => {
          const option = document.createElement('option');
          option.value = tipo.idTipoEquino;
          option.textContent = tipo.tipoEquino;
          tipoEquinoMovimiento.appendChild(option);
        });
      } catch (error) {
        console.error('Error al cargar tipos de equinos:', error);
      }
    };

    // Función para registrar un nuevo alimento
    formRegistrarAlimento.addEventListener("submit", async (event) => {
      event.preventDefault(); // Previene la recarga de la página

      if (!validarFechaCaducidad() || !validarFechaIngreso()) {
        return;
      }

      const formData = new FormData(formRegistrarAlimento);
      const data = new URLSearchParams(formData);
      data.append('operation', 'registrar');

      try {
        const response = await fetch('../../controllers/alimento.controller.php', {
          method: "POST",
          body: data
        });

        const result = await response.json();
        console.log(result); // Muestra el resultado de la respuesta para debug

        // Aquí verifica la estructura correcta
        if (result.status === "success" && result.data.status === "success") {
          alert(result.data.message);  // Usa el mensaje correcto
          formRegistrarAlimento.reset();
          actualizarFechaIngreso(); // Restablecer la fecha de ingreso
          loadAlimentos();
        } else {
          alert(result.data.message || "Error en la operación.");
        }
      } catch (error) {
        console.error('Error:', error);
        alert("Error en la solicitud: " + error.message);
      }
    });

    // Función para cargar el historial de movimientos
    const loadHistorialMovimientos = async () => {
      try {
        const response = await fetch('../../controllers/alimento.controller.php', {
          method: "POST",
          body: new URLSearchParams({ operation: 'historial' })
        });

        const textResponse = await response.text();

        // Intentar convertir el texto en JSON
        const movimientos = JSON.parse(textResponse);

        if (Array.isArray(movimientos)) {
          historialMovimientosTable.innerHTML = movimientos.map(mov => `
                    <tr>
                        <td>${mov.idAlimento}</td>
                        <td>${mov.nombreAlimento}</td>
                        <td>${mov.tipoMovimiento}</td>
                        <td>${mov.cantidad}</td>
                        <td>${mov.fechaMovimiento}</td>
                    </tr>
                `).join('');
        } else {
          historialMovimientosTable.innerHTML = '<tr><td colspan="5">No se encontraron movimientos.</td></tr>';
        }
      } catch (error) {
        console.error('Error al cargar historial de movimientos:', error);
      }
    };

    // Función dinámica para cambiar las unidades de medida según el tipo de alimento
    const actualizarOpcionesUnidadMedida = (tipoAlimento, unidadMedidaSelect) => {
      // Limpiar las opciones anteriores
      unidadMedidaSelect.innerHTML = '';

      // Agregar opciones válidas según el tipo de alimento
      if (tipoAlimento === 'Grano') {
        unidadMedidaSelect.innerHTML += '<option value="Kilos">Kilos</option>';
        unidadMedidaSelect.innerHTML += '<option value="Gramos">Gramos</option>';
      } else if (tipoAlimento === 'Heno') {
        unidadMedidaSelect.innerHTML += '<option value="Kilos">Kilos</option>';
        unidadMedidaSelect.innerHTML += '<option value="Fardos">Fardos</option>';
      } else if (tipoAlimento === 'Suplemento') {
        unidadMedidaSelect.innerHTML += '<option value="Gramos">Gramos</option>';
        unidadMedidaSelect.innerHTML += '<option value="Kilos">Kilos</option>';
      } else if (tipoAlimento === 'Líquido') {
        unidadMedidaSelect.innerHTML += '<option value="Litros">Litros</option>';
        unidadMedidaSelect.innerHTML += '<option value="Mililitros">Mililitros</option>';
      }
    };

    // Aplicar la funcionalidad dinámica al cambiar el tipo de alimento
    tipoAlimentoElementRegistrar.addEventListener('input', () => {
      actualizarOpcionesUnidadMedida(tipoAlimentoElementRegistrar.value, unidadMedidaElementRegistrar);
    });

    tipoAlimentoElementEntrada.addEventListener('input', () => {
      actualizarOpcionesUnidadMedida(tipoAlimentoElementEntrada.value, unidadMedidaElementEntrada);
    });

    // Función para manejar entradas de alimentos
    const registrarEntrada = async () => {
      if (!alimentoSelectEntrada.value) {
        alert("Por favor, seleccione un alimento.");
        return;
      }

      const formData = new FormData(formEntradaAlimento);
      const data = new URLSearchParams(formData);
      data.append('operation', 'entrada');  // Cambiar a 'entrada'

      try {
        const response = await fetch('../../controllers/alimento.controller.php', {
          method: "POST",
          body: data
        });

        const textResponse = await response.text();
        console.log("Respuesta como texto (registrar entrada):", textResponse);

        const result = JSON.parse(textResponse);
        console.log("Resultado (registrar entrada):", result);

        // Aquí verifica la estructura correcta
        if (result.status === "success" && result.data.status === "success") {
          alert(result.data.message);  // Usa el mensaje correcto
          formEntradaAlimento.reset();
          loadAlimentos();
          loadHistorialMovimientos();
        } else {
          alert(result.data.message || "Error en la operación.");
        }
      } catch (error) {
        alert("Error en la solicitud: " + error.message);
        console.error('Error:', error);
      }
    };

    // Función para manejar salidas de alimentos
    const registrarSalida = async () => {
      if (!alimentoSelectSalida.value) {
        alert("Por favor, seleccione un alimento.");
        return;
      }

      if (!tipoEquinoMovimiento.value) {
        alert("Seleccione un tipo de equino para la salida.");
        return;
      }

      const formData = new FormData(formSalidaAlimento);
      const data = new URLSearchParams(formData);
      data.append('operation', 'salida');  // Cambiar a 'salida'

      try {
        const response = await fetch('../../controllers/alimento.controller.php', {
          method: "POST",
          body: data
        });

        const textResponse = await response.text();

        // Intentar parsear el JSON
        const result = JSON.parse(textResponse);

        if (result.status === "success") {
          const message = result.data.message || result.message || "Salida registrada exitosamente.";
          alert(message);

          // Restablecer el formulario
          formSalidaAlimento.reset();
          // Cerrar el modal de salida
          $('#modalSalidaAlimento').modal('hide');
          // Recargar los alimentos y el historial de movimientos
          loadAlimentos();
          loadHistorialMovimientos();
        } else {
          alert(result.message || "Error al registrar la salida.");
        }
      } catch (error) {
        alert("Error en la solicitud: " + error.message);
        console.error('Error:', error);
      }
    };
    // Función para eliminar un alimento
    window.eliminarAlimento = async (idAlimento) => {
        if (!confirm('¿Estás seguro de que deseas eliminar este alimento?')) return;

        const data = new URLSearchParams();
        data.append('operation', 'eliminar');
        data.append('idAlimento', idAlimento);

        try {
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                body: data
            });

            const resultText = await response.text(); // Obtener la respuesta como texto
            console.log(resultText); // Mostrar la respuesta en la consola para depurar

            // Intenta convertir la respuesta a JSON
            let result;
            try {
                result = JSON.parse(resultText); // Convierte la respuesta a JSON
            } catch (e) {
                console.error('Error al convertir la respuesta a JSON', e);
                alert('Ocurrió un error inesperado. Revisa la consola para más detalles.');
                return;
            }

            // Verifica la estructura correcta del JSON
            if (result.status === "success" && result.data && result.data.status === "success") {
                alert(result.data.message); // Usa el mensaje dentro de 'data'
                loadAlimentos(); // Recargar la lista de alimentos
            } else {
                alert(result.data?.message || result.message || "Error en la operación.");
            }
        } catch (error) {
            alert("Error en la solicitud: " + error.message);
            console.error('Error:', error);
        }
    };

    // Cargar todos los datos al inicio
    loadAlimentos();
    loadTipoEquinos();
    loadHistorialMovimientos();

    // Eventos para botones de guardar
    document.querySelector("#guardarEntrada").addEventListener("click", registrarEntrada);
    document.querySelector("#guardarSalida").addEventListener("click", registrarSalida);
  });
</script>


