document.querySelector("#buscar-equino").addEventListener("click", function () {
    const nombreEquino = document.getElementById("buscarEquino").value.trim();

    if (!nombreEquino) {
        showToast("Ingrese un nombre válido para buscar.", 'WARNING');
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
        if (data.error) {
            showToast(data.message || "Error desconocido.", 'ERROR');
            return;
        }

        if (data.length === 0) {
            showToast("No se encontró ningún equino con ese nombre.", 'WARNING');
            clearModalFields();
        } else {
            loadModalFields(data[0]);
        }
    })
    .catch(error => console.error("Error al buscar el equino:", error));
});

// Función para limpiar campos del modal
function clearModalFields() {
    const fields = [
        "fechanacimiento", "nacionalidades", "propietario", 
        "genero", "tipoEquino", "idEstadoMonta", 
        "peso", "estado", "idEquino"
    ];
    fields.forEach(field => document.getElementById(field).value = '');
}

// Función para cargar campos del modal con los datos del equino
function loadModalFields(equino) {
    document.getElementById("fechanacimiento").value = equino.fechaNacimiento || '';
    document.getElementById("nacionalidades").value = equino.nacionalidad || '';
    document.getElementById("propietario").value = equino.idPropietario || 'Haras Rancho Sur';
    document.getElementById("genero").value = equino.sexo || '';
    document.getElementById("tipoEquino").value = equino.tipoEquino || '';
    document.getElementById("idEstadoMonta").value = equino.estadoMonta || '';
    document.getElementById("peso").value = equino.pesokg || 'Por pesar';
    document.getElementById("estado").value = equino.estado || 'Desconocido';
    document.getElementById("idEquino").value = equino.idEquino;
}
