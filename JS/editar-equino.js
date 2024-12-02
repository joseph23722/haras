// Evento para buscar el equino por nombre
document.querySelector("#buscar-equino").addEventListener("click", function () {
    const nombreEquino = document.getElementById("buscarEquino").value.trim();

    if (!nombreEquino) {
        showToast("Ingrese un nombre válido para buscar.", "WARNING");
        return;
    }

    fetch('../../controllers/registrarequino.controller.php', {
        method: 'POST',
        body: JSON.stringify({
            operation: 'buscarEquinoPorNombre',
            nombreEquino: nombreEquino
        }),
        headers: { 'Content-Type': 'application/json' }
    })
        .then(response => {
            if (!response.ok) throw new Error("Error en la respuesta del servidor.");
            return response.json();
        })
        .then(data => {
            console.log("Datos recibidos del servidor:", data); // Para depuración

            if (data.error) {
                showToast(data.message || "Error desconocido.", "ERROR");
                clearModalFields(); // Limpia los campos del modal en caso de error
                return;
            }

            if (data.length === 0) {
                showToast("No se encontró ningún equino con ese nombre.", "WARNING");
                clearModalFields(); // Limpia los campos si no hay resultados
            } else {
                loadModalFields(data[0]); // Carga los datos del equino en el modal
                showToast("Datos cargados correctamente.", "SUCCESS");
            }
        })
        .catch(error => {
            console.error("Error al buscar el equino:", error);
            showToast("Error al buscar el equino.", "ERROR");
        });
});

// Función para limpiar los campos del modal
function clearModalFields() {
    const fields = [
        "fechanacimiento", "nacionalidades", "propietario",
        "genero", "tipoEquino", "idEstadoMonta",
        "peso", "estado", "idEquino"
    ];
    fields.forEach(field => document.getElementById(field).value = '');
}

// Función para cargar los datos en el modal
function loadModalFields(equino) {
    console.log("Cargando datos del equino en el modal:", equino); // Para depuración

    document.getElementById("fechanacimiento").value = equino.fechaNacimiento || '';
    document.getElementById("nacionalidades").value = equino.nacionalidad || '';
    document.getElementById("propietario").value = equino.idPropietario || 'Haras Rancho Sur';
    document.getElementById("genero").value = equino.sexo || '';
    document.getElementById("tipoEquino").value = equino.tipoEquino || '';
    document.getElementById("idEstadoMonta").value = equino.estadoMonta || '';
    document.getElementById("peso").value = equino.pesokg || 'Por pesar';
    document.getElementById("estado").value = equino.estado || 'Desconocido';
    document.getElementById("idEquino").value = equino.idEquino || '';
}

// Evento para guardar los cambios al presionar el botón "Guardar cambios"
document.querySelector("#editarEquinosModal .btn-primary").addEventListener("click", function () {
    const idEquino = document.getElementById("idEquino").value.trim();
    let idPropietario = document.getElementById("propietario").value.trim();
    const pesokg = document.getElementById("peso").value.trim();
    let idEstadoMonta = document.getElementById("idEstadoMonta").value.trim();
    let estado = document.getElementById("estado").value.trim();

    // Validar que el ID del equino esté presente
    if (!idEquino) {
        showToast("El ID del equino es obligatorio.", "WARNING");
        return;
    }

    // Mapear valores de texto a los valores esperados por el backend solo si están presentes
    const estadoMap = {
        "Vivo": 1,
        "Muerto": 0
    };

    const estadoMontaMap = {
        "Activo": 1,
        "Inactivo": 2,
        "Preñada": 3,
        "Servida": 4,
        "S/S": 5,
        "Por Servir": 6,
        "Vacía": 7,
        "Con Cria": 8
    };

    // Convertir valores de texto a los valores del backend solo si están presentes
    estado = estado ? estadoMap[estado] : undefined; // Convertir "Vivo" o "Muerto" a 1 o 0, o dejarlo como undefined
    idEstadoMonta = idEstadoMonta ? estadoMontaMap[idEstadoMonta] : undefined; // Convertir "Inactivo" a su ID correspondiente

    // Si el propietario es "Haras Rancho Sur", enviar null para mantenerlo como propio
    if (idPropietario === "Haras Rancho Sur") {
        idPropietario = null;
    }

    // Construir el objeto para enviar solo con los campos que tienen cambios
    const datosEdicion = { operation: "editarEquino", idEquino };

    if (idPropietario !== null) datosEdicion.idPropietario = idPropietario;
    if (pesokg) datosEdicion.pesokg = pesokg;
    if (idEstadoMonta !== undefined) datosEdicion.idEstadoMonta = idEstadoMonta;
    if (estado !== undefined) datosEdicion.estado = estado;

    console.log("Datos enviados para edición:", datosEdicion); // Log de los datos enviados

    // Enviar los datos al backend
    fetch('../../controllers/editarequino.controller.php', {
        method: 'POST',
        body: JSON.stringify(datosEdicion),
        headers: { 'Content-Type': 'application/json' }
    })
        .then(response => {
            console.log("Estado HTTP:", response.status); // Log del estado HTTP
            return response.text(); // Capturar la respuesta como texto
        })
        .then(text => {
            console.log("Respuesta completa (text):", text); // Log de la respuesta en texto

            // Intentar convertir la respuesta en JSON
            try {
                const data = JSON.parse(text);
                console.log("Respuesta JSON:", data); // Log del JSON convertido

                // Manejo de respuesta
                if (data.status === "success") {
                    showToast(data.message || "Equino actualizado correctamente.", "SUCCESS");
                    // Cerrar el modal
                    const modal = bootstrap.Modal.getInstance(document.querySelector("#editarEquinosModal"));
                    modal.hide();
                } else {
                    showToast(data.message || "Error al actualizar el equino.", "ERROR");
                }
            } catch (error) {
                console.error("Error al convertir respuesta a JSON:", error); // Log del error de conversión
                showToast("Error inesperado en la respuesta del servidor.", "ERROR");
            }
        })
        .catch(error => {
            console.error("Error al guardar los cambios:", error); // Log del error de fetch
            showToast("Ocurrió un error al intentar guardar los cambios.", "ERROR");
        });
});