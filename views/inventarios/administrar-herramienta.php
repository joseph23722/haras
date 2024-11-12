<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Registro de Historial de Herrero</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #ffcc80, #ffb74d); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Historial de Herrero</h5>
        </div>

        <!-- Formulario -->
        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-historial-herrero" autocomplete="off">
                <div class="row g-3">
                    
                    <!-- Selector para el Tipo de Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="tipoEquinoSelect" class="form-select" name="tipoEquino" required>
                                <option value="">Seleccione Tipo de Equino</option>
                                <!-- Opciones se llenarán dinámicamente -->
                            </select>
                            <label for="tipoEquinoSelect"><i class="fas fa-horse" style="color: #00b4d8;"></i> Tipo de Equino</label>
                        </div>
                    </div>

                    <!-- Selector para el nombre del Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="equinoSelect" class="form-select" name="idEquino" required>
                                <option value="">Seleccione Equino</option>
                                <!-- Opciones se llenarán dinámicamente -->
                            </select>
                            <label for="equinoSelect"><i class="fas fa-horse" style="color: #00b4d8;"></i> Equino</label>
                        </div>
                    </div>

                    <!-- Selector para Trabajo Realizado -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="trabajoRealizado" class="form-select" name="trabajoRealizado" required>
                                <option value="">Seleccione Trabajo Realizado</option>
                                <option value="recorte">Recorte de casco</option>
                                <option value="colocacion">Colocación de herraduras nuevas</option>
                                <option value="cambio">Cambio de herraduras</option>
                                <option value="ajuste">Ajuste o corrección de herraduras</option>
                                <option value="reparacion">Reparación de cascos dañados</option>
                            </select>
                            <label for="trabajoRealizado"><i class="fas fa-tools" style="color: #ff8c00;"></i> Trabajo Realizado</label>
                        </div>
                    </div>

                    <!-- Herramienta Usada como un selector simple -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="herramientaUsada" class="form-select" name="herramientaUsada" required>
                                <option value="">Seleccione Herramienta Usada</option>
                                <option value="herradura_acero">Herradura de acero</option>
                                <option value="herradura_aluminio">Herradura de aluminio</option>
                                <option value="lima_casco">Lima de cascos</option>
                                <option value="cuchillo_casco">Cuchillo para casco</option>
                                <option value="martillo_herrado">Martillo de herrado</option>
                            </select>
                            <label for="herramientaUsada"><i class="fas fa-wrench" style="color: #00b4d8;"></i> Herramienta Usada</label>
                        </div>
                    </div>

                    <!-- Campo de fecha -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" id="fecha" name="fecha" class="form-control" required>
                            <label for="fecha"><i class="fas fa-calendar-alt" style="color: #007bff;"></i> Fecha</label>
                        </div>
                    </div>


                    <!-- Observaciones -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <textarea name="observaciones" id="observaciones" class="form-control" style="height: 100px;"></textarea>
                            <label for="observaciones"><i class="fas fa-notes-medical" style="color: #007bff;"></i> Observaciones</label>
                        </div>
                    </div>

                    <div id="mensaje" style="margin-top: 10px; color: green; font-weight: bold;"></div>

                    <!-- Botones -->
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="registrar-historial-herrero" style="background-color: #0077b6; border: none;">
                            <i class="fas fa-save"></i> Registrar Historial
                        </button>
                        <button type="reset" class="btn btn-secondary btn-lg shadow-sm" style="background-color: #adb5bd; border: none;">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla para DataTable -->
    <div class="card mt-4">
        <div class="card-header" style="background: linear-gradient(to right, #ffcc80, #ffb74d); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Historiales de Herrero</h5>
        </div>
        <div class="card-body">
            <table id="historialHerreroTable" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Trabajo Realizado</th>
                        <th>Herramienta Usada</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Se llenará dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Incluye SweetAlert -->
<script src="../../swalcustom.js"></script>
<script>
    // Función para establecer la fecha mínima en el campo de fecha
    document.addEventListener('DOMContentLoaded', function() {
        const fechaInput = document.getElementById('fecha');
        
        // Obtener la fecha actual en formato YYYY-MM-DD
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0'); // Mes con dos dígitos
        const day = String(today.getDate()).padStart(2, '0'); // Día con dos dígitos
        const minDate = `${year}-${month}-${day}`;
        
        // Establecer la fecha mínima
        fechaInput.setAttribute('min', minDate);
    });
    
    // Función para cargar tipos de equinos en el selector
    function cargarTiposEquinos() {
        console.log("Iniciando carga de tipos de equinos...");
        fetch('/haras/controllers/herrero.controller.php?operation=getTipoEquinos')
            .then(response => response.json())
            .then(data => {
                console.log("Datos de tipos de equinos recibidos:", data);
                if (data.status === 'success') {
                    const tipoEquinoSelect = document.getElementById('tipoEquinoSelect');
                    tipoEquinoSelect.innerHTML = '<option value="">Seleccione Tipo de Equino</option>'; // Limpia las opciones anteriores
                    
                    data.data.forEach(tipo => {
                        console.log("Tipo de Equino:", tipo); // Log de cada tipo de equino
                        const option = document.createElement('option');
                        option.value = tipo.idTipoEquino;
                        option.textContent = tipo.tipoEquino;
                        tipoEquinoSelect.appendChild(option);
                    });
                } else {
                    console.warn("No se recibieron tipos de equinos o hubo un error.");
                }
            })
            .catch(error => console.error('Error al cargar tipos de equinos:', error));
    }

    // Función para cargar equinos según el tipo de equino seleccionado
    function cargarEquinosPorTipo(tipoEquinoId) {
        console.log(`Cargando equinos para el tipo con ID ${tipoEquinoId}`);
        fetch(`/haras/controllers/herrero.controller.php?operation=getEquinosByTipo&idTipoEquino=${tipoEquinoId}`)
            .then(response => response.json())
            .then(data => {
                console.log("Datos de equinos recibidos:", data);
                const equinoSelect = document.getElementById('equinoSelect');
                equinoSelect.innerHTML = '<option value="">Seleccione Equino</option>'; // Limpia las opciones anteriores
                
                if (data.status === 'success' && data.data.length > 0) {
                    data.data.forEach(equino => {
                        console.log("Equino recibido:", equino); // Log de cada equino recibido
                        const option = document.createElement('option');
                        option.value = equino.idEquino;
                        option.textContent = equino.nombreEquino;
                        equinoSelect.appendChild(option);
                    });
                } else {
                    console.warn("No se encontraron equinos para este tipo.");
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No hay equinos disponibles para este tipo';
                    equinoSelect.appendChild(option);
                }
            })
            .catch(error => console.error('Error al cargar equinos:', error));
    }
    // Función para registrar un nuevo historial de herrero

        function registrarHistorialHerrero() {
        const formData = new FormData(document.getElementById('form-historial-herrero'));
        formData.append('operation', 'insertarHistorialHerrero');

        const datos = {};
        formData.forEach((value, key) => {
            if (key === 'herramientaUsada') key = 'herramientasUsadas';  // Asegura la clave correcta
            datos[key] = value;
            console.log(`Campo ${key}:`, value);  // Log de cada campo y valor en el frontend
        });

        datos['idUsuario'] = 'superE';  // Asegúrate de que idUsuario se pase correctamente

        console.log("Datos a enviar para registrar historial:", datos);

        fetch('/haras/controllers/herrero.controller.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        })
        .then(response => {
            console.log("Respuesta de la API recibida:", response);
            return response.json();
        })
        .then(data => {
            console.log("Datos procesados de la respuesta:", data);
            if (data.status === 'success') {
                Swal.fire('Registrado', data.message, 'success');
                document.getElementById('form-historial-herrero').reset();
                cargarHistorialHerrero();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => console.error('Error al registrar historial:', error));
    }




    // Función para cargar el historial del herrero en la tabla
    function cargarHistorialHerrero() {
        console.log("Cargando historial del herrero...");
        fetch('/haras/controllers/herrero.controller.php?operation=consultarHistorialEquino&idEquino=1') // Cambia "1" por el ID dinámico del equino si es necesario
            .then(response => {
                console.log("Respuesta de la API para historial recibida:", response); // Log de respuesta sin procesar
                return response.json();
            })
            .then(data => {
                console.log("Datos del historial recibidos:", data);
                const historialTable = document.querySelector('#historialHerreroTable tbody');
                historialTable.innerHTML = '';

                if (data.status === 'success') {
                    data.data.forEach(historial => {
                        console.log("Registro de historial:", historial); // Log de cada registro de historial
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${historial.fecha}</td>
                            <td>${historial.trabajoRealizado}</td>
                            <td>${historial.herramientasUsadas}</td>
                            <td>${historial.observaciones}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="actualizarEstadoFinal(${historial.idHistorialHerrero})">Actualizar Estado</button>
                            </td>
                        `;
                        historialTable.appendChild(row);
                    });
                } else {
                    console.warn("No se encontró historial o hubo un error.");
                }
            })
            .catch(error => console.error('Error al cargar historial de herrero:', error));
    }

    // Evento DOMContentLoaded para cargar datos iniciales
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Evento DOMContentLoaded disparado");
        cargarTiposEquinos();
        cargarHistorialHerrero();

        // Evento para el formulario de registro
        document.getElementById('form-historial-herrero').addEventListener('submit', function(event) {
            event.preventDefault();
            console.log("Formulario de historial de herrero enviado");
            registrarHistorialHerrero();
        });

        // Evento para cargar equinos según el tipo seleccionado
        document.getElementById('tipoEquinoSelect').addEventListener('change', function() {
            const tipoEquinoId = this.value;
            console.log("Tipo de equino seleccionado:", tipoEquinoId);
            cargarEquinosPorTipo(tipoEquinoId);
        });
    });
</script>
