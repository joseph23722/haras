document.addEventListener('DOMContentLoaded', function () {
    // Inicializar la fecha mínima para el input de fecha
    const fechaInput = document.getElementById('fecha');
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0'); // Mes con dos dígitos
    const day = String(today.getDate()).padStart(2, '0'); // Día con dos dígitos
    const minDate = `${year}-${month}-${day}`;
    fechaInput.setAttribute('min', minDate);

    // Cargar los tipos de trabajo y herramientas al cargar la página
    cargarTiposTrabajos();
    cargarHerramientas();
});

// Función para mostrar notificaciones dinámicas en el div `mensaje`
const mostrarMensajeDinamico = (mensaje, tipo = 'INFO') => {
    const mensajeDiv = document.getElementById('mensaje');

    if (mensajeDiv) {
        const estilos = {
            'INFO': { color: '#3178c6', bgColor: '#e7f3ff', icon: 'ℹ️' },
            'SUCCESS': { color: '#3c763d', bgColor: '#dff0d8', icon: '✅' },
            'ERROR': { color: '#a94442', bgColor: '#f2dede', icon: '❌' },
            'WARNING': { color: '#8a6d3b', bgColor: '#fcf8e3', icon: '⚠️' }
        };

        const estilo = estilos[tipo] || estilos['INFO'];

        mensajeDiv.style.color = estilo.color;
        mensajeDiv.style.backgroundColor = estilo.bgColor;
        mensajeDiv.style.fontWeight = 'bold';
        mensajeDiv.style.padding = '15px';
        mensajeDiv.style.marginBottom = '15px';
        mensajeDiv.style.border = `1px solid ${estilo.color}`;
        mensajeDiv.style.borderRadius = '8px';
        mensajeDiv.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
        mensajeDiv.style.display = 'flex';
        mensajeDiv.style.alignItems = 'center';

        mensajeDiv.innerHTML = `<span style="margin-right: 10px; font-size: 1.2em;">${estilo.icon}</span>${mensaje}`;

        setTimeout(() => {
            mensajeDiv.innerHTML = '';
            mensajeDiv.style.border = 'none';
            mensajeDiv.style.boxShadow = 'none';
            mensajeDiv.style.backgroundColor = 'transparent';
        }, 5000);
    } else {
        console.warn('El contenedor de mensajes no está presente en el DOM.');
    }
};

// Verificar campos para Trabajo o Herramienta
const verificarCamposTrabajoHerramienta = () => {
    const nombre = document.getElementById("inputNombre")?.value?.trim();
    const tipo = document.getElementById("selectTipo")?.value;
    const trabajoSeleccionado = document.getElementById("selectTrabajo")?.value;
    const herramientaSeleccionada = document.getElementById("selectHerramienta")?.value;
    const mensajeModal = document.getElementById("mensajeModal");

    mensajeModal.innerHTML = ""; // Limpiar mensajes previos

    if (!nombre) {
        mensajeModal.innerHTML = '<p class="text-danger">Por favor, complete el campo "Nombre".</p>';
        return false;
    }

    if (!tipo) {
        mensajeModal.innerHTML = '<p class="text-danger">Por favor, seleccione un tipo.</p>';
        return false;
    }

    return { nombre, tipo, trabajoSeleccionado, herramientaSeleccionada }; // Retorna los campos
};




// Cargar tipos de trabajo dinámicamente
async function cargarTiposTrabajos() {
    try {
        const response = await fetch('../../controllers/herrero.controller.php?operation=listarTiposTrabajos');
        
        // Manejo de respuesta como texto para depuración
        const textResponse = await response.text();
        
        // Intentar convertir a JSON
        let data;
        try {
            data = JSON.parse(textResponse);
        } catch (error) {
            return; // Salir si la respuesta no es JSON válido
        }

        if (data.status === 'success') {
            const trabajoSelect = document.getElementById('trabajoRealizado');
            trabajoSelect.innerHTML = '<option value="">Seleccione un trabajo</option>'; // Limpiar opciones previas
            data.data.forEach(trabajo => {
                const option = document.createElement('option');
                option.value = trabajo.idTipoTrabajo;
                option.textContent = trabajo.nombreTrabajo;
                trabajoSelect.appendChild(option);
            });
        } else {
            console.error('Error al cargar tipos de trabajo:', data.message);
        }
    } catch (error) {
        console.error('Error en la solicitud para tipos de trabajo:', error);
    }
}

// Cargar herramientas dinámicamente
async function cargarHerramientas() {
    try {
        const response = await fetch('../../controllers/herrero.controller.php?operation=listarHerramientas');
        
        // Manejo de respuesta como texto para depuración
        const textResponse = await response.text();
        
        // Intentar convertir a JSON
        let data;
        try {
            data = JSON.parse(textResponse);
        } catch (error) {
            return; // Salir si la respuesta no es JSON válido
        }

        if (data.status === 'success') {
            const herramientaSelect = document.getElementById('herramientaUsada');
            herramientaSelect.innerHTML = '<option value="">Seleccione una herramienta</option>'; // Limpiar opciones previas
            data.data.forEach(herramienta => {
                const option = document.createElement('option');
                option.value = herramienta.idHerramienta;
                option.textContent = herramienta.nombreHerramienta;
                herramientaSelect.appendChild(option);
            });
        } else {
            console.error('Error al cargar herramientas:', data.message);
        }
    } catch (error) {
        console.error('Error en la solicitud para herramientas:', error);
    }
}


// Función para cargar los equinos según el tipo seleccionado
async function loadEquinosPorTipo(tipoEquino) {
    const equinoSelect = document.getElementById("equinoSelect");

    try {
        const response = await fetch(`../../controllers/historialme.controller.php?operation=listarEquinosPorTipo`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        // Limpiar el selector de equinos
        equinoSelect.innerHTML = '<option value="">Seleccione Equino</option>';

        // Verificar si se obtuvieron datos
        if (data.data && Array.isArray(data.data)) {
            data.data.forEach(equino => {
                if (equino.idTipoEquino == tipoEquino) {
                    const option = document.createElement('option');
                    option.value = equino.idEquino;
                    option.textContent = equino.nombreEquino;
                    option.setAttribute('data-peso', equino.pesokg);
                    equinoSelect.appendChild(option);
                }
            });
        } else {
            mostrarMensajeDinamico("No se encontraron equinos para el tipo seleccionado.", "WARNING");
        }
    } catch (error) {
        mostrarMensajeDinamico("Error al cargar los equinos", "ERROR");
    }
}

// Evento para actualizar el select de equinos cuando cambia el tipo de equino
document.getElementById("tipoEquinoSelect").addEventListener("change", function () {
    const tipoEquino = this.value;
    if (tipoEquino) {
        loadEquinosPorTipo(tipoEquino);
    } else {
        // Limpiar si no hay tipo seleccionado
        document.getElementById("equinoSelect").innerHTML = '<option value="">Seleccione Equino</option>';
    }
});

// Función para registrar un nuevo historial de herrero
document.getElementById('form-historial-herrero').addEventListener('submit', function (event) {
    // Prevenir el comportamiento predeterminado de recargar la página
    event.preventDefault();

    // Llama a la función de registro de historial
    registrarHistorialHerrero();
});

function registrarHistorialHerrero() {
    const formData = new FormData(document.getElementById('form-historial-herrero'));
    formData.append('operation', 'insertarHistorialHerrero');

    const datos = {};
    formData.forEach((value, key) => {
        if (key === 'herramientaUsada') key = 'herramientasUsadas'; // Asegura la clave correcta
        datos[key] = value;
    });


    fetch('/haras/controllers/herrero.controller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datos)
    })
        .then(response => response.text()) // Captura como texto para depurar
        .then(text => {
            try {
                const data = JSON.parse(text); // Intenta parsear a JSON
                if (data.status === 'success') {
                    Swal.fire('Registrado', data.message, 'success');
                    document.getElementById('form-historial-herrero').reset();
                    loadHistorialHerreroTable();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            } catch (e) {
                console.error("Error al parsear JSON:", e);
                console.log("Contenido recibido:", text);
            }
        })
        .catch(error => console.error('Error al registrar historial:', error));
}



// Guardar nuevo Trabajo o Herramienta
const guardarTrabajoHerramienta = async () => {
    const inputNombre = document.getElementById("inputNombre");
    const selectTipo = document.getElementById("selectTipo");
    const selectTrabajo = document.getElementById("selectTrabajo");
    const selectHerramienta = document.getElementById("selectHerramienta");
    const mensajeModal = document.getElementById("mensajeModal");

    const nombre = inputNombre.value.trim();
    const tipo = selectTipo.value;
    const trabajoSeleccionado = selectTrabajo.value;
    const herramientaSeleccionada = selectHerramienta.value;

    // Verificar que el campo obligatorio esté lleno
    if (!nombre || !tipo) {
        mensajeModal.innerHTML = '<p class="text-danger">Por favor, complete los campos obligatorios.</p>';
        return;
    }

    const datos = {
        operation: tipo === 'trabajo' ? "agregarTipoTrabajo" : "agregarHerramienta", // Cambia dependiendo del tipo
        nombre: nombre,
        tipo: tipo,
        trabajo: trabajoSeleccionado || null, // Si es tipo trabajo
        herramienta: herramientaSeleccionada || null // Si es tipo herramienta
    };

    try {
        const response = await fetch("../../controllers/herrero.controller.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(datos)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        mensajeModal.innerHTML = result.status === "success"
            ? '<p class="text-success">¡Trabajo/Herramienta agregado correctamente!</p>'
            : `<p class="text-danger">${result.message}</p>`;

        if (result.status === "success") {
            setTimeout(() => {
                // Resetear el formulario
                document.getElementById("formNuevoTrabajoHerramienta").reset();
                mensajeModal.innerHTML = "";

                // Cerrar el modal
                bootstrap.Modal.getInstance(document.getElementById("modalAgregarTrabajoHerramienta")).hide();

                // Actualizar la lista de trabajos y herramientas
                cargarTiposTrabajos();
                cargarHerramientas();
            }, 1500);
        }
    } catch (error) {
        mensajeModal.innerHTML = '<p class="text-danger">Error al enviar los datos al servidor.</p>';
    }
};

// Asignar evento al botón de guardar
const btnGuardarTrabajoHerramienta = document.getElementById("btnGuardarTrabajoHerramienta");
if (btnGuardarTrabajoHerramienta) {
    btnGuardarTrabajoHerramienta.addEventListener("click", guardarTrabajoHerramienta);
} else {
    console.error("El botón #btnGuardarTrabajoHerramienta no se encontró.");
}

