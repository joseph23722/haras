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
            operation: 'buscarEquinosGeneral',
            nombreEquino: nombreEquino
        }),
        headers: { 'Content-Type': 'application/json' }
    })
        .then(response => {
            if (!response.ok) throw new Error("Error en la respuesta del servidor.");
            return response.json();
        })
        .then(data => {
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
            showToast("Error al buscar el equino.", "ERROR");
        });
});

// Función para limpiar los campos del modal
function clearModalFields() {
    const fields = [
        "fechanacimiento", "nacionalidades", "propietario",
        "genero", "tipoEquino", "idEstadoMonta",
        "peso", "estado", "ingreso", "salida", "idEquino"
    ];
    fields.forEach(field => document.getElementById(field).value = '');
}

// Función para cargar los datos en el modal
function loadModalFields(equino) {
    document.getElementById("fechanacimiento").value = equino.fechaNacimiento || '--';
    document.getElementById("nacionalidades").value = equino.nacionalidad || '--';
    document.getElementById("propietario").value = equino.nombreHaras || 'Haras Rancho Sur';
    document.getElementById("genero").value = equino.sexo || '--';
    document.getElementById("tipoEquino").value = equino.tipoEquino || '--';
    document.getElementById("idEstadoMonta").value = equino.estadoMonta || '--';
    document.getElementById("peso").value = equino.pesokg || '--';
    document.getElementById("estado").value = equino.estado || 'Desconocido';
    document.getElementById("ingreso").value = equino.fechaentrada || '';
    document.getElementById("salida").value = equino.fechasalida || '';  // Si la fecha de salida es nula o vacía, dejarla vacía

    document.getElementById("idEquino").value = equino.idEquino || '';

    // Verificar si el propietario es null y ocultar las fechas de entrada/salida si es necesario
    if (equino.nombreHaras === null || equino.nombreHaras === 'Haras Rancho Sur') {
        document.getElementById("ingreso").closest(".col-md-6").style.display = "none";
        document.getElementById("salida").closest(".col-md-6").style.display = "none";
    } else {
        document.getElementById("ingreso").closest(".col-md-6").style.display = "block";
        document.getElementById("salida").closest(".col-md-6").style.display = "block";
    }
}

// Guardar los cambios realizados en el formulario
document.querySelector("#editarEquinosModal .btn-primary").addEventListener("click", async function () {
    const idEquino = document.getElementById("idEquino").value.trim();
    let idPropietario = document.getElementById("propietario").value.trim();
    const pesokg = document.getElementById("peso").value.trim();
    let idEstadoMonta = document.getElementById("idEstadoMonta").value.trim();
    let estado = document.getElementById("estado").value.trim();
    const fechaEntrada = document.getElementById("ingreso").value.trim();
    const fechaSalida = document.getElementById("salida").value.trim();  // Aquí capturamos la fecha de salida

    // Validar que el ID del equino esté presente
    if (!idEquino) {
        showToast("El ID del equino es obligatorio.", "WARNING");
        return;
    }

    const confirmacion = await ask("¿Está seguro de guardar los cambios?", "Haras Rancho Sur");

    if (confirmacion) {
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
        if (fechaEntrada) datosEdicion.fechaentrada = fechaEntrada;

        // Aquí comprobamos si la fecha de salida está vacía o no, si está vacía enviamos "" (vacío)
        if (fechaSalida !== "") {
            datosEdicion.fechasalida = fechaSalida;  // Si el campo no está vacío, enviar el valor.
        } else {
            datosEdicion.fechasalida = "";  // Si el campo está vacío, enviar cadena vacía.
        }

        // Enviar los datos al backend
        fetch('../../controllers/editarequino.controller.php', {
            method: 'POST',
            body: JSON.stringify(datosEdicion),
            headers: { 'Content-Type': 'application/json' }
        })
            .then(response => response.text()) // Capturar la respuesta como texto
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.status === "success") {
                        showToast(data.message || "Equino actualizado correctamente.", "SUCCESS");
                        // Cerrar el modal de edición
                        const modal = bootstrap.Modal.getInstance(document.querySelector("#editarEquinosModal"));
                        modal.hide();
                    } else {
                        showToast(data.message || "Error al actualizar el equino.", "ERROR");
                    }
                } catch (error) {
                    showToast("Error inesperado en la respuesta del servidor.", "ERROR");
                }
            })
            .catch(error => {
                showToast("Ocurrió un error al intentar guardar los cambios.", "ERROR");
            });
    } else {
        showToast("Los cambios no han sido guardados.", "INFO");
    }
});
