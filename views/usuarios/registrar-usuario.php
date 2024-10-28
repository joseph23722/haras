<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #005b99;">LISTADO DE USUARIOS</h1>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Datos de los sUsuarios</div>
                <div class="card-body">
                    <table id="tabla-personal" class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>DNI</th>
                                <th>Apellidos</th>
                                <th>Nombres</th>
                                <th>Dirección</th>
                                <th>Usuario</th>
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
    <!-- Fin contenido -->
</div>

  <!-- Modal para registrar usuario -->
  <div class="modal fade" id="modalRegistrarUsuario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content border-light shadow-sm">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="exampleModalLabel">Registrar Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="formulario-usuario">
            <input type="hidden" id="idPersonal">
            <div class="mb-3">
              <label for="correo" class="form-label">Nombre de Usuario</label>
              <input type="text" class="form-control form-control-lg border-1 shadow-sm" id="correo" required>
            </div>
            <div class="mb-3">
              <label for="clave" class="form-label">Clave</label>
              <input type="password" class="form-control form-control-lg border-1 shadow-sm" id="clave" required>
            </div>
            <div class="mb-3">
              <label for="idRol" class="form-label">Rol</label>
              <select class="form-select form-select-lg border-1 shadow-sm" id="idRol" required>
                <option value="1">Administrador</option>
                <option value="2">Colaborador</option>
                <option value="3">Supervisor</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm">Registrar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

</main>

<?php require_once '../../footer.php'; ?>

<!-- Incluye DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

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
      console.log("Valor de idPersonal antes de enviar al servidor:", idPersonalVal);  // Log para depuración
      
      if (idPersonalVal === "" || idPersonalVal == null) {
        alert("Por favor, selecciona un personal válido antes de registrar un usuario.");
        return;
      }

      const parametros = new FormData();
      parametros.append("operation", "add");
      parametros.append("idPersonal", idPersonalVal);
      parametros.append("correo", $("#correo").val());
      parametros.append("clave", $("#clave").val());
      parametros.append("idRol", $("#idRol").val());

      // Log para verificar los datos que se envían al servidor
      console.log("Datos enviados al servidor:", {
        idPersonal: idPersonalVal,
        correo: $("#correo").val(),
        clave: $("#clave").val(),
        idRol: $("#idRol").val()
      });

      try {
        const response = await fetch(`../../controllers/usuario.controller.php`, {
          method: 'POST',
          body: parametros
        });

        const data = await response.json();

        console.log("Respuesta del servidor:", data);

        if (data.status === 'success') {
          alert('Usuario registrado correctamente');
          $('#modalRegistrarUsuario').modal('hide');
        } else if (data.status === 'error') {
          alert(data.message);
        }
      } catch (error) {
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
