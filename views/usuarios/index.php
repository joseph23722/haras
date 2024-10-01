<?php require_once '../../header.php'; ?>

<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #005b99;">Registrar Personal y Listado de Usuarios</h1>

    <!-- Contenido -->
    <div class="row">
      <!-- Sección de Registro de Personal -->
      <div class="col-md-12 mb-4">
        <form action="" autocomplete="off" id="formulario-personal">
          <div class="card">
            <div class="card-header">Datos del Personal</div>
            <div class="card-body">
              <!-- Fila 1 -->
              <div class="row g-3 mb-3">
                <div class="col-md-2">
                  <div class="input-group">
                    <div class="form-floating">
                      <input
                        type="text"
                        class="form-control"
                        id="dni"
                        pattern="[0-9]+"
                        title="Solo se permiten números"
                        maxlength="8"
                        required
                        autofocus>
                      <label for="dni" class="form-label">Nro. Documento</label>
                    </div>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="apellidos" maxlength="100" required>
                    <label for="apellidos" class="form-label">Apellidos</label>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="nombres" maxlength="100" required>
                    <label for="nombres" class="form-label">Nombres</label>
                  </div>
                </div>
              </div>
              <!-- Fin Fila 1 -->

              <!-- Fila 2 -->
              <div class="row g-3 mb-3">
                <div class="col-md-2">
                  <div class="form-floating">
                    <select class="form-control" name="tipodoc" id="tipodoc" required>
                      <option value="DNI">DNI</option>
                      <option value="Pasaporte">Pasaporte</option>
                      <option value="Carnet">Carnet</option>
                    </select>
                    <label for="tipodoc">Tipo de documento</label>
                  </div>
                </div>

                <div class="col-md-2">
                  <div class="form-floating">
                    <input class="form-control" id="numeroHijos" type="text"
                        id="dni"
                        pattern="[0-9]+"
                        title="Solo se permiten números"
                        maxlength="2">
                    <label for="numeroHijos" class="form-label">Número de Hijos</label>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="direccion" maxlength="255">
                    <label for="direccion" class="form-label">Dirección</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-floating">
                    <input type="date" class="form-control" id="fechaIngreso">
                    <label for="fechaIngreso" class="form-label">Fecha de Ingreso</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-floating">
                    <select class="form-control" name="tipoContrato" id="tipoContrato" required>
                      <option value="Parcial">Parcial</option>
                      <option value="Completo">Completo</option>
                      <option value="PorPracticas">Por Prácticas</option>
                      <option value="Otros">Otros</option>
                    </select>
                    <label for="tipoContrato">Tipo de contrato</label>
                  </div>
                </div>
              </div>
              <!-- Fin Fila 2 -->
            </div>
            <div class="card-footer text-end">
              <button type="submit" class="btn btn-sm btn-primary">Registrar</button>
              <button type="reset" class="btn btn-sm btn-outline-secondary">Cancelar</button>
            </div>
          </div>
        </form>
      </div>

      <!-- Sección de Listado de Usuarios -->
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Datos de los Usuarios</div>
          <div class="card-body">
            <table id="tabla-personal" class="table table-striped table-sm">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Tipo Documento</th>
                  <th>Nro. Documento</th>
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
      </div>
    </div>
    <!-- Fin contenido -->

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
                <label for="correo" class="form-label">Correo</label>
                <input type="email" class="form-control form-control-lg border-1 shadow-sm" id="correo" required>
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
<script src="../../JS/personal.js"></script>