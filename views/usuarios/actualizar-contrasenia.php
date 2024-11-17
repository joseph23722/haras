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
            <input type="email" class="form-control" id="correo" placeholder="Ingresa tu correo o nombre de usuario" required>
          </div>
          <div class="mb-3">
            <label for="nuevaContrasenia" class="form-label">Nueva Contraseña</label>
            <input type="password" class="form-control" id="nuevaContrasenia" placeholder="Ingresa nueva contraseña" required>
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

<!-- JS para la funcionalidad -->
<script>
  // Capturar el formulario y los elementos
  const formActualizarContrasenia = document.getElementById('formActualizarContrasenia');
  const correoInput = document.getElementById('correo');
  const nuevaContraseniaInput = document.getElementById('nuevaContrasenia');
  const confirmarContraseniaInput = document.getElementById('confirmarContrasenia');
  const btnGuardar = document.getElementById('btnActualizarContrasenia');

  // Función para mostrar mensaje con SweetAlert
  const mostrarAlerta = (mensaje, tipo) => {
    Swal.fire({
      icon: tipo,
      title: tipo === 'success' ? '¡Éxito!' : '¡Error!',
      text: mensaje
    });
  };

  // Función para manejar el envío del formulario
  formActualizarContrasenia.addEventListener('submit', function(e) {
    e.preventDefault();

    // Validar si las contraseñas coinciden
    if (nuevaContraseniaInput.value !== confirmarContraseniaInput.value) {
      mostrarAlerta('Las contraseñas no coinciden. Por favor, verifica.', 'error');
      return;
    }

    // Datos del formulario
    const datos = {
      correo: correoInput.value,
      nuevaContrasenia: nuevaContraseniaInput.value
    };

    // Usar fetch para enviar los datos al servidor
    fetch('../../controllers/usuario.controller.php?operation=actualizarcontrasenia', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(datos)
      })
      .then(response => response.json())
      .then(data => {
        if (data === 1) {
          mostrarAlerta('Contraseña actualizada con éxito.', 'success');
        } else {
          mostrarAlerta('Hubo un problema al actualizar la contraseña. Intenta nuevamente.', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('Hubo un problema con la conexión. Intenta más tarde.', 'error');
      });
  });
</script>