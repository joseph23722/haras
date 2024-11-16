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

                    <!-- Selector para el nombre del Tipo de Equino -->
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
    <!-- Tabla para DataTable -->
    <div class="card mt-4">
        <div class="card-header" style="background: linear-gradient(to right, #ffcc80, #ffb74d); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Historiales de Herrero</h5>
        </div>
        <div class="card-body">
            <table id="historialHerreroTable" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre del Equino</th>
                        <th>Tipo de Equino</th>
                        <th>Fecha</th>
                        <th>Trabajo Realizado</th>
                        <th>Herramienta Usada</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>

<?php require_once '../footer.php'; ?>

<script src="/haras/vendor/herrero/herrero.js" defer></script>


<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>




<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Incluye SweetAlert -->
<script src="../../swalcustom.js"></script>
<script>
    // Función para establecer la fecha mínima en el campo de fecha
    document.addEventListener('DOMContentLoaded', function() {
        const fechaInput = document.getElementById('fecha');
        const tipoEquinoSelect = document.getElementById("tipoEquino");
        const equinoSelect = document.getElementById("equino");
        
        // Obtener la fecha actual en formato YYYY-MM-DD
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0'); // Mes con dos dígitos
        const day = String(today.getDate()).padStart(2, '0'); // Día con dos dígitos
        const minDate = `${year}-${month}-${day}`;
        
        // Establecer la fecha mínima
        fechaInput.setAttribute('min', minDate);
    });
    
    // Función para cargar los equinos según el tipo seleccionado
    async function loadEquinosPorTipo(tipoEquino) {
        const equinoSelect = document.getElementById("equinoSelect");

        try {
            const response = await fetch(`../../controllers/historialme.controller.php?operation=listarEquinosPorTipo`, {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            });

            const data = await response.json();

            // Limpiar el selector de equinos
            equinoSelect.innerHTML = '<option value="">Seleccione Equino</option>';

            // Verificar si se obtuvieron datos
            if (data.data && Array.isArray(data.data)) {
                data.data.forEach(equino => {
                    if (equino.idTipoEquino == tipoEquino) {
                        const option = document.createElement('option');
                        option.value = equino.idEquino;
                        option.textContent = equino.nombreEquino;
                        option.setAttribute('data-peso', equino.pesokg);
                        equinoSelect.appendChild(option);
                    }
                });
            } else {
                mostrarMensajeDinamico("No se encontraron equinos para el tipo seleccionado.", "WARNING");
            }
        } catch (error) {
            mostrarMensajeDinamico("Error al cargar los equinos", "ERROR");
            console.error("Error:", error);
        }
    }

    // Evento para actualizar el select de equinos cuando cambia el tipo de equino
    document.getElementById("tipoEquinoSelect").addEventListener("change", function () {
        const tipoEquino = this.value;
        if (tipoEquino) {
            loadEquinosPorTipo(tipoEquino);
        } else {
            // Limpiar si no hay tipo seleccionado
            document.getElementById("equinoSelect").innerHTML = '<option value="">Seleccione Equino</option>';
        }
    });

    
    // Función para registrar un nuevo historial de herrero

    document.getElementById('form-historial-herrero').addEventListener('submit', function (event) {
    // Prevenir el comportamiento predeterminado de recargar la página
        event.preventDefault();

        // Llama a la función de registro de historial
        registrarHistorialHerrero();
    });

    function registrarHistorialHerrero() {
        const formData = new FormData(document.getElementById('form-historial-herrero'));
        formData.append('operation', 'insertarHistorialHerrero');

        const datos = {};
        formData.forEach((value, key) => {
            if (key === 'herramientaUsada') key = 'herramientasUsadas';  // Asegura la clave correcta
            datos[key] = value;
            console.log(`Campo ${key}:`, value);  // Log de cada campo y valor en el frontend
        });

        console.log("Datos a enviar para registrar historial:", datos);

        fetch('/haras/controllers/herrero.controller.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datos)
        })
        .then(response => response.text()) // Captura como texto para depurar
        .then(text => {
            console.log("Respuesta cruda:", text);
            try {
                const data = JSON.parse(text); // Intenta parsear a JSON
                if (data.status === 'success') {
                    Swal.fire('Registrado', data.message, 'success');
                    document.getElementById('form-historial-herrero').reset();
                    loadHistorialHerreroTable();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (e) {
                console.error("Error al parsear JSON:", e);
                console.log("Contenido recibido:", text);
            }
        })
        .catch(error => console.error('Error al registrar historial:', error));


    }



    // Función para cargar el DataTable de historial de herrero
    const loadHistorialHerreroTable = (idEquino) => {
        if (!$.fn.DataTable.isDataTable('#historialHerreroTable')) {
            $('#historialHerreroTable').DataTable(configurarDataTableHerrero(idEquino));
        } else {
            $('#historialHerreroTable').DataTable().ajax.reload();
        }
    };

    // Inicializar la tabla al cargar la página
    $(document).ready(function () {
        const idEquino = 1; // Cambia esto a un ID real o dinámico
        loadHistorialHerreroTable(idEquino);
    });

    


    
</script>
