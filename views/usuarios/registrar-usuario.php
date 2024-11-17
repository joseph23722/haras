<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 36px; color: #005b99;">
        <i class="fas fa-users" style="color: #a0ffb8;"></i> Listado de Usuarios
    </h1>

    <!-- Sección de Listado de Usuarios -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow border-0 mt-4">
                <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #9be8e4); color: #003366;">
                    <h5 class="text-center m-0" style="font-weight: bold;">
                        <i class="fas fa-address-book" style="color: #3498db;"></i> Datos de los Usuarios
                    </h5>
                </div>
                <div class="card-body p-4" style="background-color: #f9f9f9;">
                    <table id="tabla-personal" class="table table-striped table-hover table-bordered">
                        <thead style="background-color: #caf0f8; color: #003366;">
                            <tr>
                                <th><i class="fas fa-hashtag"></i> #</th>
                                <th><i class="fas fa-id-card"></i> DNI</th>
                                <th><i class="fas fa-user"></i> Apellidos</th>
                                <th><i class="fas fa-user-tag"></i> Nombres</th>
                                <th><i class="fas fa-map-marker-alt"></i> Dirección</th>
                                <th><i class="fas fa-user-circle"></i> Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- .col-md-12 -->
    </div> <!-- .row -->

</div> <!-- .container-fluid -->

<!-- Modal para registrar usuario -->
<div class="modal fade" id="modalRegistrarUsuario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-light shadow-sm">
            <div class="modal-header" style="background: linear-gradient(to right, #005b99, #0077b6); color: white;">
                <h5 class="modal-title" id="exampleModalLabel">
                    <i class="fas fa-user-plus"></i> Registrar Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background-color: #f9f9f9;">
                <form id="formulario-usuario">
                    <input type="hidden" id="idPersonal">
                    <div class="mb-3">
                        <label for="correo" class="form-label"><i class="fas fa-envelope"></i> Nombre de Usuario</label>
                        <input type="text" class="form-control form-control-lg border-1 shadow-sm" id="correo" required>
                    </div>
                    <div class="mb-3">
                        <label for="clave" class="form-label"><i class="fas fa-key"></i> Clave</label>
                        <input type="password" class="form-control form-control-lg border-1 shadow-sm" id="clave" required>
                    </div>
                    <div class="mb-3">
                        <label for="idRol" class="form-label"><i class="fas fa-user-tag"></i> Rol</label>
                        <select class="form-select form-select-lg border-1 shadow-sm" id="idRol" required>
                            <option value="">Seleccione un rol</option>
                            <option value="1">Administrador</option>
                            <option value="2">Colaborador</option>
                            <option value="3">Supervisor</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm" style="background-color: #0056b3;">
                        <i class="fas fa-save"></i> Registrar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>


<!-- Incluye DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../swalcustom.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", () => {

    // Inicializa DataTables
    function inicializarDataTable() {
      $('#tabla-personal').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 10
      });
    }

    // Obtener lista de personal
    async function obtenerPersonal() {
      try {
        const response = await fetch(`../../controllers/Persona.controller.php?operation=getAll`, {
          method: 'GET'
        });
        const data = await response.json();
        
        console.log("Personal recibido:", data);

        let numeroFila = 1;
        data.forEach(personal => {
          const tieneUsuario = personal.tieneUsuario == 1 ? '✔️' : `<button data-id="${personal.idPersonal}" class="btn btn-sm btn-outline-primary register-user" data-bs-toggle="modal" data-bs-target="#modalRegistrarUsuario">Registrar Usuario</button>`;
          
          const nuevaFila = `
            <tr>
              <td>${numeroFila}</td>
              <td>${personal.nrodocumento}</td>
              <td>${personal.apellidos}</td>
              <td>${personal.nombres}</td>
              <td>${personal.direccion}</td>
              <td class="text-center">${tieneUsuario}</td>
            </tr>
          `;

          $("#tabla-personal tbody").append(nuevaFila);
          numeroFila++;
        });

        inicializarDataTable();

        // Asigna evento click a cada botón "Registrar Usuario"
        document.querySelectorAll(".register-user").forEach(button => {
          button.addEventListener("click", (event) => {
            const idPersonal = event.currentTarget.getAttribute("data-id");  // Capturamos el valor de data-id con currentTarget para asegurar que el evento funcione bien
            console.log("ID del personal capturado en el click:", idPersonal);  // Log para verificar el ID capturado

            if (idPersonal) {
              $("#idPersonal").val(idPersonal);  // Asigna el valor al input oculto
              console.log("ID del personal asignado al input oculto:", $("#idPersonal").val());  // Log para verificar asignación
            } else {
              console.error("No se pudo capturar el ID del personal.");
            }
          });
        });

      } catch (error) {
        console.error("Error al obtener personal:", error);
      }
    }

    // Función para registrar un usuario
    async function registrarUsuario() {
        const idPersonalVal = $("#idPersonal").val();

        if (idPersonalVal === "" || idPersonalVal == null) {
            showToast("Por favor, selecciona un personal válido antes de registrar un usuario.", "WARNING");
            return;
        }

        const parametros = new FormData();
        parametros.append("operation", "add");
        parametros.append("idPersonal", idPersonalVal);
        parametros.append("correo", $("#correo").val());
        parametros.append("clave", $("#clave").val());
        parametros.append("idRol", $("#idRol").val());

        try {
            const response = await fetch(`../../controllers/usuario.controller.php`, {
                method: 'POST',
                body: parametros
            });

            const data = await response.json();

            if (data.status === 'success') {
                showToast("Usuario registrado correctamente", "SUCCESS");
                $('#modalRegistrarUsuario').modal('hide');
            } else if (data.status === 'error') {
                showToast(data.message, "ERROR");
            }
        } catch (error) {
            showToast("Error al registrar usuario: " + error.message, "ERROR");
            console.error("Error al registrar usuario:", error);
        }
    }


    // Enviar el formulario de registro
    $("#formulario-usuario").on("submit", async (event) => {
      event.preventDefault();
      await registrarUsuario();
    });

    // Cargar la lista de personal al inicio
    obtenerPersonal();
  });

</script>
