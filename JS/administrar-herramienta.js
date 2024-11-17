document.addEventListener('DOMContentLoaded', function () {
    const fechaInput = document.getElementById('fecha');
    const tipoEquinoSelect = document.getElementById("tipoEquino");
    const equinoSelect = document.getElementById("equino");

    // Obtener la fecha actual en formato YYYY-MM-DD
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0'); // Mes con dos dígitos
    const day = String(today.getDate()).padStart(2, '0'); // Día con dos dígitos
    const minDate = `${year}-${month}-${day}`;

    // Establecer la fecha mínima
    fechaInput.setAttribute('min', minDate);
});


// Cargar tipos de trabajo dinámicamente
async function cargarTiposTrabajos() {
    try {
        const response = await fetch('../../controllers/herrero.controller.php?operation=listarTiposTrabajos');
        
        // Manejo de respuesta como texto para depuración
        const textResponse = await response.text();
        console.log('Respuesta en texto (tipos de trabajo):', textResponse);
        
        // Intentar convertir a JSON
        let data;
        try {
            data = JSON.parse(textResponse);
            console.log('Respuesta parseada como JSON (tipos de trabajo):', data);
        } catch (error) {
            console.error('Error al parsear JSON para tipos de trabajo:', error);
            return; // Salir si la respuesta no es JSON válido
        }

        if (data.status === 'success') {
            const trabajoSelect = document.getElementById('trabajoRealizado');
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
        console.error("Error:", error);
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
        console.log(`Campo ${key}:`, value); // Log de cada campo y valor en el frontend
    });

    console.log("Datos a enviar para registrar historial:", datos);

    fetch('/haras/controllers/herrero.controller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datos)
    })
        .then(response => response.text()) // Captura como texto para depurar
        .then(text => {
            console.log("Respuesta cruda:", text);
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

// Función para cargar el DataTable de historial de herrero
const loadHistorialHerreroTable = (idEquino) => {
    if (!$.fn.DataTable.isDataTable('#historialHerreroTable')) {
        $('#historialHerreroTable').DataTable(configurarDataTableHerrero(idEquino));
    } else {
        $('#historialHerreroTable').DataTable().ajax.reload();
    }
};

// Inicializar la tabla al cargar la página
$(document).ready(function () {
    const idEquino = 1; // Cambia esto a un ID real o dinámico
    loadHistorialHerreroTable(idEquino);
    cargarTiposTrabajos();
    cargarHerramientas();
});