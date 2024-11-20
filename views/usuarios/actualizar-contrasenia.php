<?php require_once '../header.php'; ?>

<main>
  <div class="container-fluid px-4">
    <!-- Título con estilo elegante y detalles coloridos -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 36px; color: #005b99; letter-spacing: 2px;">
      <i class="fas fa-key" style="color: #ffb84d; font-size: 38px;"></i> Actualizar Contraseña
    </h1>

    <!-- Formulario para actualizar contraseña -->
    <div class="row justify-content-center">
      <div class="col-md-6">
        <form id="formActualizarContrasenia" class="shadow-lg p-5 rounded-4 bg-white border border-light">
          <div class="mb-4">
            <label for="correo" class="form-label fs-5 text-muted"><i class="fas fa-user-circle" style="color: #ff8c00;"></i> Correo o Nombre de Usuario</label>
            <input type="text" class="form-control form-control-lg border-0 rounded-3 shadow-sm" id="correo" value="<?= htmlspecialchars($_SESSION['login']['correo'], ENT_QUOTES, 'UTF-8') ?>" readonly>
          </div>
          
          <div class="mb-4">
            <label for="clave" class="form-label fs-5 text-muted"><i class="fas fa-lock" style="color: #00b894;"></i> Nueva Contraseña</label>
            <input type="password" class="form-control form-control-lg border-0 rounded-3 shadow-sm" id="clave" placeholder="Ingresa nueva contraseña" required minlength="8" maxlength="20">
          </div>
          
          <div class="mb-4">
            <label for="confirmarContrasenia" class="form-label fs-5 text-muted"><i class="fas fa-lock" style="color: #00b894;"></i> Confirmar Contraseña</label>
            <input type="password" class="form-control form-control-lg border-0 rounded-3 shadow-sm" id="confirmarContrasenia" placeholder="Confirma tu nueva contraseña" required>
          </div>

          <!-- Botones con bordes redondeados, colores vibrantes y transiciones -->
          <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-danger btn-lg px-4 rounded-pill shadow" data-bs-dismiss="modal">
              <i class="fas fa-times-circle"></i> Cancelar
            </button>
            <button type="submit" class="btn btn-success btn-lg px-4 rounded-pill shadow" id="btnActualizarContrasenia">
              <i class="fas fa-save"></i> Guardar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>

<?php require_once '../footer.php'; ?>


<!-- SweetAlert y Swalcustom -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../swalcustom.js"></script>

<script src="../../JS/actualizar-contrasenia.js"></script>