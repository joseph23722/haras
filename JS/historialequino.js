// Inicialización de Quill
var quill = new Quill('#descripcion', {
    theme: 'snow',
    placeholder: 'Escribe una descripción...',
    modules: {
        toolbar: [
            [{ 'header': '1' }, { 'header': '2' }, { 'font': [] }],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
            ['bold', 'italic', 'underline'],
            [{ 'align': [] }],
            ['link'],
            [{ 'color': [] }, { 'background': [] }],
            ['blockquote', 'code-block']
        ]
    }
});

// Al enviar el formulario, obtener el contenido del editor Quill
document.getElementById('form-historial-equino').onsubmit = function () {
    var descripcion = quill.root.innerHTML;
    document.getElementById('descripcion').value = descripcion;
};

// Buscar el equino por nombre
document.querySelector("#buscar-equino").addEventListener("click", async function () {
    const nombreEquino = document.getElementById("buscarEquino").value;
    const response = await fetch('../../controllers/registrarequino.controller.php', {
        method: 'POST',
        body: JSON.stringify({
            operation: 'buscarEquinoPorNombre',
            nombreEquino: nombreEquino
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    });

    const data = await response.json();

    if (data.length === 0) {
        showToast("No se encontró ningún equino con ese nombre.", 'WARNING');
        document.getElementById("fechaNacimiento").value = '';
        document.getElementById("nacionalidad").value = '';
        document.getElementById("idPropietario").value = '';
        document.getElementById("sexo").value = '';
        document.getElementById("tipoEquino").value = '';
        document.getElementById("idEstadoMonta").value = '';
        document.getElementById("pesokg").value = '';
        document.getElementById("fotografia").value = '';
    } else {
        const equino = data[0];
        document.getElementById("fechaNacimiento").value = equino.fechaNacimiento || '';
        document.getElementById("nacionalidad").value = equino.nacionalidad || '';
        document.getElementById("idPropietario").value = equino.idPropietario || 'Haras Rancho Sur';
        document.getElementById("sexo").value = equino.sexo || '';
        document.getElementById("tipoEquino").value = equino.tipoEquino || '';
        document.getElementById("idEstadoMonta").value = equino.estadoMonta || '';
        document.getElementById("pesokg").value = equino.pesokg || 'Por pesar';
        document.getElementById("fotografia").value = equino.fotografia || '';
        document.getElementById("idEquino").value = equino.idEquino;
    }
});

// Al enviar el formulario de historial
document.querySelector("#form-historial-equino").addEventListener("submit", async function (event) {
    event.preventDefault();

    const idEquino = document.getElementById('idEquino').value;
    const descripcion = quill.root.innerHTML;

    // Verificar si el campo Quill está vacío o solo tiene espacios
    const descripcionSinEspacios = descripcion.replace(/<[^>]+>/g, '').trim();

    if (!descripcionSinEspacios) {
        showToast("La descripción es obligatoria y no puede estar vacía.", 'ERROR');
        return;
    }

    // Confirmación de registro
    const confirmar = await ask("¿Está seguro de que desea registrar el historial?");
    if (confirmar) {
        const data = {
            operation: 'registrarHistorialEquino',
            idEquino: idEquino,
            descripcion: descripcion
        };

        const response = await fetch('../../controllers/registrarequino.controller.php', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (result.status === 'success') {
            showToast("Historial registrado correctamente.", 'SUCCESS');
        } else {
            showToast("Error al registrar el historial.", 'ERROR');
        }
    }
});