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
                                <option value="1">Yegua</option>
                                <option value="2">Padrillo</option>
                                <option value="3">Potranca</option>
                                <option value="4">Potrillo</option>
                            </select>
                            <label for="tipoEquinoSelect"><i class="fas fa-horse" style="color: #00b4d8;"></i> Tipo de Equino</label>
                        </div>
                    </div>

                    <!-- Selector para el nombre del Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="equinoSelect" class="form-select" name="idEquino" required>
                                <option value="">Seleccione Equino</option>
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

                    <!-- Selector para Estado Inicial de los Cascos -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="estadoCascosInicio" class="form-select" name="estadoCascosInicio" required>
                                <option value="">Seleccione Estado Inicial de los Cascos</option>
                                <option value="1">Buen estado</option>
                                <option value="2">Desgastado</option>
                                <option value="3">Daño visible</option>
                                <option value="4">Infección o enfermedad</option>
                            </select>
                            <label for="estadoCascosInicio"><i class="fas fa-info-circle" style="color: #6a5acd;"></i> Estado Inicial de Cascos</label>
                        </div>
                    </div>

                    <!-- Herramientas Usadas como un selector simple -->
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

                    <!-- Estado Inicial de las Herramientas -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="estadoInicio" id="estadoInicio" class="form-select" required>
                                <option value="">Seleccione Estado Inicial de Herramientas</option>
                                <option value="1">En buen estado</option>
                                <option value="2">Desgastada</option>
                                <option value="3">Necesita reparación</option>
                            </select>
                            <label for="estadoInicio"><i class="fas fa-info-circle" style="color: #6a5acd;"></i> Estado Inicial de Herramientas</label>
                        </div>
                    </div>

                    <!-- Estado Final de Herramientas -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="estadoFin" id="estadoFin" class="form-select">
                                <option value="">Seleccione Estado Final de Herramientas</option>
                                <option value="1">En buen estado</option>
                                <option value="2">Desgastada</option>
                                <option value="3">Necesita reparación</option>
                            </select>
                            <label for="estadoFin"><i class="fas fa-info-circle" style="color: #ff6347;"></i> Estado Final de Herramientas</label>
                        </div>
                    </div>

                    <!-- Estado Final de los Cascos -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="estadoCascosFin" class="form-select" name="estadoCascosFin">
                                <option value="">Seleccione Estado Final de los Cascos</option>
                                <option value="1">Buen estado</option>
                                <option value="2">Mejorado</option>
                                <option value="3">Sin cambios</option>
                                <option value="4">Requiere atención adicional</option>
                            </select>
                            <label for="estadoCascosFin"><i class="fas fa-info-circle" style="color: #6a5acd;"></i> Estado Final de los Cascos</label>
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
                        <th>Equino</th>
                        <th>Trabajo Realizado</th>
                        <th>Herramienta Usada</th>
                        <th>Estado Inicial Cascos</th>
                        <th>Estado Final Cascos</th>
                        <th>Estado Inicio Herramienta</th>
                        <th>Estado Fin Herramienta</th>
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
                        const option = document.createElement('option');
                        option.value = tipo.idTipoEquino;
                        option.textContent = tipo.tipoEquino;
                        tipoEquinoSelect.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Error al cargar tipos de equinos:', error));
    }

    // Función para cargar equinos según el tipo de equino seleccionado
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
                        const option = document.createElement('option');
                        option.value = equino.idEquino;  // Asegúrate de que 'idEquino' es correcto
                        option.textContent = equino.nombreEquino;  // Usa la clave correcta en lugar de 'nombre'
                        equinoSelect.appendChild(option);
                        console.log("Equino agregado:", equino.nombreEquino); // Confirma el nombre del equino
                    });

                } else {
                    console.warn('No se encontraron equinos para este tipo.');
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'No hay equinos disponibles para este tipo';
                    equinoSelect.appendChild(option);
                }
            })
            .catch(error => console.error('Error al cargar equinos:', error));
    }


    // Función para cargar herramientas disponibles
    function cargarHerramientas() {
        fetch('/haras/controllers/herrero.controller.php?operation=getHerramientas')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const herramientasSelect = document.getElementById('herramientasUsadas');
                    herramientasSelect.innerHTML = ''; // Limpia las opciones anteriores
                    data.data.forEach(herramienta => {
                        const option = document.createElement('option');
                        option.value = herramienta.idHerramienta;
                        option.textContent = herramienta.nombre;
                        herramientasSelect.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Error al cargar herramientas:', error));
    }

    // Función para registrar un nuevo historial de herrero
    function registrarHistorialHerrero() {
        const formData = new FormData(document.getElementById('form-historial-herrero'));
        formData.append('operation', 'insertarHistorialHerrero');
        
        const datos = {};
        formData.forEach((value, key) => datos[key] = value);

        fetch('/haras/controllers/herrero.controller.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire('Registrado', data.message, 'success');
                document.getElementById('form-historial-herrero').reset();
                cargarHistorialHerrero(); // Recargar el historial
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => console.error('Error al registrar historial:', error));
    }

    // Función para cargar el historial del herrero en la tabla
    function cargarHistorialHerrero() {
        fetch('/haras/controllers/herrero.controller.php?operation=consultarHistorialEquino&idEquino=1') // Cambia "1" por el ID dinámico del equino si es necesario
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const historialTable = document.querySelector('#historialHerreroTable tbody');
                    historialTable.innerHTML = '';

                    data.data.forEach(historial => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${historial.nombreEquino}</td>
                            <td>${historial.trabajoRealizado}</td>
                            <td>${historial.herramientasUsadas}</td>
                            <td>${historial.estadoInicio}</td>
                            <td>${historial.estadoFin}</td>
                            <td>${historial.observaciones}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="actualizarEstadoFinal(${historial.idHistorialHerrero})">Actualizar Estado</button>
                            </td>
                        `;
                        historialTable.appendChild(row);
                    });
                }
            })
            .catch(error => console.error('Error al cargar historial de herrero:', error));
    }

    // Función para actualizar el estado final de una herramienta
    function actualizarEstadoFinal(idHistorialHerrero) {
        Swal.fire({
            title: 'Actualizar Estado Final',
            input: 'select',
            inputOptions: {
                1: 'En buen estado',
                2: 'Desgastada',
                3: 'Necesita reparación'
            },
            inputPlaceholder: 'Seleccione el estado final',
            showCancelButton: true,
            confirmButtonText: 'Actualizar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/haras/controllers/herrero.controller.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        operation: 'actualizarEstadoFinalHerramientaUsada',
                        idHerramientasUsadas: idHistorialHerrero,
                        estadoFin: result.value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Actualizado', data.message, 'success');
                        cargarHistorialHerrero();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => console.error('Error al actualizar estado final:', error));
            }
        });
    }

    // Evento DOMContentLoaded para cargar datos iniciales
    document.addEventListener('DOMContentLoaded', function() {
        cargarTiposEquinos();
        cargarHerramientas();
        cargarHistorialHerrero();

        // Evento para el formulario de registro
        document.getElementById('form-historial-herrero').addEventListener('submit', function(event) {
            event.preventDefault();
            registrarHistorialHerrero();
        });

        // Evento para cargar equinos según el tipo seleccionado
        document.getElementById('tipoEquinoSelect').addEventListener('change', function() {
            const tipoEquinoId = this.value;
            cargarEquinosPorTipo(tipoEquinoId);
        });
    });
</script>
