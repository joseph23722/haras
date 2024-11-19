<?php require_once '../header.php'; ?>

<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 36px; color: #005b99;">
      <i class="fas fa-key" style="color: #a0ffb8;"></i> Actualizar Contraseña
    </h1>

    <!-- Formulario para actualizar contraseña -->
    <div class="row justify-content-center">
      <div class="col-md-6">
        <form id="formActualizarContrasenia">
          <div class="mb-3">
            <label for="correo" class="form-label">Correo o Nombre de Usuario</label>
            <input type="text" class="form-control" id="correo" value="<?= htmlspecialchars($_SESSION['login']['correo'], ENT_QUOTES, 'UTF-8') ?>" readonly>
          </div>
          <div class="mb-3">
            <label for="clave" class="form-label">Nueva Contraseña</label>
            <input type="password" class="form-control" id="clave" placeholder="Ingresa nueva contraseña" required minlength="8" maxlength="20">
          </div>
          <div class="mb-3">
            <label for="confirmarContrasenia" class="form-label">Confirmar Contraseña</label>
            <input type="password" class="form-control" id="confirmarContrasenia" placeholder="Confirma tu nueva contraseña" required>
          </div>
          <div class="mb-3 d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary" id="btnActualizarContrasenia">Guardar</button>
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