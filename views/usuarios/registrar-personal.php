<?php require_once '../header.php'; ?>

<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 36px; color: #005b99;">
      <i class="fas fa-users" style="color: #a0ffb8;"></i> Registrar Personal y Listado de Usuarios
    </h1>

    <!-- Sección de Registro de Personal -->
    <div class="row">
      <div class="col-md-12 mb-4">
        <form action="" autocomplete="off" id="formulario-personal">
          <div class="card shadow border-0">
            <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #9be8e4); color: #003366;">
              <h5 class="text-center m-0" style="font-weight: bold;">
                <i class="fas fa-id-card-alt" style="color: #3498db;"></i> Datos del Personal
              </h5>
            </div>
            <div class="card-body" style="background-color: #f9f9f9;">
              <!-- Fila 1 -->
              <div class="row g-3 mb-3">
                <div class="col-md-2">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="dni" pattern="[0-9]+" title="Solo se permiten números" maxlength="8" required autofocus>
                    <label for="dni"><i class="fas fa-id-badge" style="color: #3498db;"></i> Nro. Documento</label>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="apellidos" maxlength="100" required>
                    <label for="apellidos"><i class="fas fa-user" style="color: #3498db;"></i> Apellidos Completos</label>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="nombres" maxlength="100" required>
                    <label for="nombres"><i class="fas fa-user-tag" style="color: #3498db;"></i> Nombres Completos</label>
                  </div>
                </div>
              </div>
              <!-- Fin Fila 1 -->

              <!-- Fila 2 -->
              <div class="row g-3 mb-3">
                <div class="col-md-3">
                  <div class="form-floating">
                    <select class="form-control" id="tipodoc" required>
                      <option value="">Tipo de documento</option>
                      <option value="DNI">DNI</option>
                      <option value="Pasaporte">Pasaporte</option>
                      <option value="Carnet">Carnet</option>
                    </select>
                    <label for="tipodoc"><i class="fas fa-passport" style="color: #3498db;"></i> Tipo de documento</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="direccion" maxlength="255">
                    <label for="direccion"><i class="fas fa-map-marker-alt" style="color: #3498db;"></i> Dirección</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-floating">
                    <input type="date" class="form-control" id="fechaIngreso">
                    <label for="fechaIngreso"><i class="fas fa-calendar-check" style="color: #3498db;"></i> Fecha de Ingreso</label>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-floating">
                    <select class="form-control" id="tipoContrato" required>
                      <option value="">Tipo de Contrato</option>
                      <option value="Parcial">Parcial</option>
                      <option value="Completo">Completo</option>
                      <option value="PorPracticas">Por Prácticas</option>
                      <option value="Otros">Otros</option>
                    </select>
                    <label for="tipoContrato"><i class="fas fa-briefcase" style="color: #3498db;"></i> Tipo de contrato</label>
                  </div>
                </div>
              </div>
              <!-- Fin Fila 2 -->
            </div>
            <div class="card-footer text-end">
              <button type="submit" class="btn btn-primary btn-lg shadow-sm me-2" style="background-color: #0056b3;">
                <i class="fas fa-save"></i> Registrar
              </button>
              <button type="reset" class="btn btn-secondary btn-lg shadow-sm" style="background-color: #adb5bd;">
                <i class="fas fa-times"></i> Cancelar
              </button>
            </div>
          </div>
        </form>
      </div>

      <!-- Sección de Listado de Usuarios -->
      <div class="col-md-12">
        <div class="card shadow border-0">
          <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #9be8e4); color: #003366;">
            <h5 class="text-center m-0" style="font-weight: bold;">
              <i class="fas fa-users" style="color: #3498db;"></i> Datos de los Usuarios
            </h5>
          </div>
          <div class="card-body p-4" style="background-color: #f9f9f9;">
            <table id="tabla-personal" class="table table-striped table-hover table-bordered">
              <thead style="background-color: #caf0f8; color: #003366;">
                <tr>
                  <th><i class="fas fa-hashtag"></i></th>
                  <th><i class="fas fa-id-card"></i> Tipo Documento</th>
                  <th><i class="fas fa-address-card"></i> Nro. Documento</th>
                  <th><i class="fas fa-user"></i> Apellidos</th>
                  <th><i class="fas fa-user"></i> Nombres</th>
                  <th><i class="fas fa-map-marker-alt"></i> Dirección</th>
                  <th><i class="fas fa-user-circle"></i> Cuenta Usuario</th>
                  <th><i class="fas fa-envelope"></i> Usuario</th> <!-- Columna de correo -->
                  <th><i class="fas fa-sync-alt"></i> Estado</th>
                  <th style="display:none;">ID Usuario</th> <!-- Columna oculta -->
                </tr>
              </thead>
              <tbody>
                <!-- Los datos se cargarán aquí -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para registrar usuario -->
    <div class="modal fade" id="modalRegistrarUsuario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content border-light shadow-sm">
          <div class="modal-header" style="background: linear-gradient(to right, #005b99, #0077b6); color: white;">
            <h5 class="modal-title" id="exampleModalLabel">Registrar Usuario</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
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
                  <option value="1">Gerente</option>
                  <option value="2">Administrador</option>
                  <option value="3">Supervisor Equino</option>
                  <option value="4">Supervisor Campo</option>
                  <option value="5">Médico</option>
                  <option value="6">Herrero</option>
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
</main>

<?php require_once '../footer.php'; ?>


<!-- SweetAlert y Swalcustom -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../swalcustom.js"></script>
<!-- Incluye DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="../../JS/registrarPersonal.js"></script>