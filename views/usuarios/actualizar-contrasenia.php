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
            <div class="input-group">
              <input type="password" class="form-control" id="nuevaContrasenia" placeholder="Ingresa nueva contraseña" required>
              <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i>
            </div>
            <!-- Barra de progreso de fuerza de contraseña -->
            <div id="password-strength-status" class="mt-2"></div>
            <div class="progress mt-2" style="height: 5px;">
              <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
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
  const togglePassword = document.getElementById('togglePassword');
  const strengthBar = document.getElementById('password-strength-bar');
  const strengthStatus = document.getElementById('password-strength-status');

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

  // Funcionalidad para mostrar/ocultar la contraseña
  togglePassword.addEventListener('click', function() {
    const passwordField = document.getElementById('nuevaContrasenia');
    const confirmPasswordField = document.getElementById('confirmarContrasenia');
    
    if (passwordField.type === "password") {
      passwordField.type = "text";
      confirmPasswordField.type = "text";
      togglePassword.classList.remove("fa-eye-slash");
      togglePassword.classList.add("fa-eye");
    } else {
      passwordField.type = "password";
      confirmPasswordField.type = "password";
      togglePassword.classList.remove("fa-eye");
      togglePassword.classList.add("fa-eye-slash");
    }
  });

  // Función para evaluar la fuerza de la contraseña
  const evaluatePasswordStrength = (password) => {
    let strength = 0;

    // RegExp para comprobar los diferentes criterios de seguridad
    if (password.length >= 8) strength += 25;  // Longitud mínima de 8 caracteres
    if (/[A-Z]/.test(password)) strength += 25; // Contiene al menos una letra mayúscula
    if (/[a-z]/.test(password)) strength += 25; // Contiene al menos una letra minúscula
    if (/[0-9]/.test(password)) strength += 25; // Contiene al menos un número

    // Cambiar el color de la barra según la fuerza de la contraseña
    if (strength <= 25) {
      strengthBar.style.width = '25%';
      strengthBar.classList.add('bg-danger');
      strengthStatus.textContent = 'Contraseña débil';
      strengthStatus.style.color = 'red';
    } else if (strength <= 50) {
      strengthBar.style.width = '50%';
      strengthBar.classList.remove('bg-danger');
      strengthBar.classList.add('bg-warning');
      strengthStatus.textContent = 'Contraseña moderada';
      strengthStatus.style.color = 'orange';
    } else if (strength <= 75) {
      strengthBar.style.width = '75%';
      strengthBar.classList.remove('bg-warning');
      strengthBar.classList.add('bg-info');
      strengthStatus.textContent = 'Contraseña buena';
      strengthStatus.style.color = 'blue';
    } else {
      strengthBar.style.width = '100%';
      strengthBar.classList.remove('bg-info');
      strengthBar.classList.add('bg-success');
      strengthStatus.textContent = 'Contraseña fuerte';
      strengthStatus.style.color = 'green';
    }
  };

  // Llamar a la función de evaluación cada vez que el usuario escriba en el campo de la contraseña
  nuevaContraseniaInput.addEventListener('input', function() {
    evaluatePasswordStrength(nuevaContraseniaInput.value);
  });
</script>

<!-- Estilos CSS para la animación y el diseño atractivo -->
<style>


  @keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  /* Animación para los campos de entrada */
  .form-control, #btnActualizarContrasenia {
    animation: fadeIn 0.5s ease-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Estilo para el botón */
  #btnActualizarContrasenia {
    transition: transform 0.3s ease, background-color 0.3s ease;
  }

  #btnActualizarContrasenia:hover {
    transform: scale(1.05);
    background-color: #004a72;
  }
</style>
