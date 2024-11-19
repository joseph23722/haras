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
formActualizarContrasenia.addEventListener('submit', async function (e) {
    e.preventDefault();

    // Validar si las contraseñas coinciden antes de enviar
    if (nuevaContraseniaInput.value !== confirmarContraseniaInput.value) {
        showToast('Las contraseñas no coinciden. Por favor, verifica.', 'ERROR');
        return;
    }

    // Confirmar actualización de contraseña
    if (!(await ask('¿Estás seguro de actualizar tu contraseña?'))) {
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
                showToast('Contraseña actualizada con éxito.', 'SUCCESS');
                formActualizarContrasenia.reset();
                mensajeValidacion.textContent = '';
                btnGuardar.disabled = true;
            } else {
                showToast(data.message || 'Error desconocido.', 'ERROR');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error en la conexión. Intenta nuevamente.', 'ERROR');
        });
});