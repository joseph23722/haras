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
            <input type="text" class="form-control" id="correo" placeholder="Ingresa tu correo o nombre de usuario" required>
          </div>
          <div class="mb-3">
            <label for="clave" class="form-label">Nueva Contraseña</label>
            <input type="password" class="form-control" id="clave" placeholder="Ingresa nueva contraseña" required minlength="8">
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

<script>
  const formActualizarContrasenia = document.getElementById('formActualizarContrasenia');
  const correoInput = document.getElementById('correo');
  const nuevaContraseniaInput = document.getElementById('clave');
  const confirmarContraseniaInput = document.getElementById('confirmarContrasenia');
  const btnGuardar = document.getElementById('btnActualizarContrasenia');

  const mensajeValidacion = document.createElement('small');
  mensajeValidacion.style.display = 'block';
  mensajeValidacion.style.marginTop = '5px';

  // Insertar el mensaje debajo del campo confirmar contraseña
  confirmarContraseniaInput.parentElement.appendChild(mensajeValidacion);

  // Función para mostrar mensaje con SweetAlert
  const mostrarAlerta = (mensaje, tipo) => {
    Swal.fire({
      icon: tipo,
      title: tipo === 'success' ? '¡Éxito!' : '¡Error!',
      text: mensaje
    });
  };

  // Función para verificar si las contraseñas coinciden
  const validarContrasenias = () => {
    const clave = nuevaContraseniaInput.value;
    const confirmarClave = confirmarContraseniaInput.value;

    if (clave === confirmarClave && clave.length > 0) {
      mensajeValidacion.textContent = 'Las contraseñas coinciden.';
      mensajeValidacion.style.color = 'green';
      btnGuardar.disabled = false;
    } else if (confirmarClave.length > 0) {
      mensajeValidacion.textContent = 'Las contraseñas no coinciden.';
      mensajeValidacion.style.color = 'red';
      btnGuardar.disabled = true;
    } else {
      mensajeValidacion.textContent = '';
      btnGuardar.disabled = true;
    }
  };

  // Escuchar cambios en los campos de contraseña
  nuevaContraseniaInput.addEventListener('input', validarContrasenias);
  confirmarContraseniaInput.addEventListener('input', validarContrasenias);

  // Manejar el envío del formulario
  formActualizarContrasenia.addEventListener('submit', function(e) {
    e.preventDefault();

    // Validar si las contraseñas coinciden antes de enviar
    if (nuevaContraseniaInput.value !== confirmarContraseniaInput.value) {
      mostrarAlerta('Las contraseñas no coinciden. Por favor, verifica.', 'error');
      return;
    }

    fetch('../../controllers/usuario.controller.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          operation: 'actualizarcontrasenia',
          correo: correoInput.value,
          clave: nuevaContraseniaInput.value,
        }),
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        if (data.status === 'success') {
          mostrarAlerta('Contraseña actualizada con éxito.', 'success');
          formActualizarContrasenia.reset();
          mensajeValidacion.textContent = '';
          btnGuardar.disabled = true;
        } else {
          mostrarAlerta(data.message || 'Error desconocido.', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('Error en la conexión. Intenta nuevamente.', 'error');
      });
  });
</script>