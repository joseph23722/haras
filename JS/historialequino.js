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

// Evento de clic para buscar equino
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
        const equinoImage = document.querySelector(".equino-image");
        if (equinoImage) {
            equinoImage.src = 'https://via.placeholder.com/400x365?text=Imagen+No+Disponible';
        }
    } else {
        const equino = data[0];
        document.getElementById("fechaNacimiento").value = equino.fechaNacimiento || '';
        document.getElementById("nacionalidad").value = equino.nacionalidad || '';
        document.getElementById("idPropietario").value = equino.idPropietario || 'Haras Rancho Sur';
        document.getElementById("sexo").value = equino.sexo || '';
        document.getElementById("tipoEquino").value = equino.tipoEquino || '';
        document.getElementById("idEstadoMonta").value = equino.estadoMonta || '';
        document.getElementById("pesokg").value = equino.pesokg || 'Por pesar';

        const fotografiaField = document.getElementById("fotografia-buscada");
        if (fotografiaField) {
            fotografiaField.value = equino.fotografia || '';
        }

        const equinoImage = document.querySelector(".equino-image");
        if (equinoImage) {
            equinoImage.src = equino.fotografia
                ? `https://res.cloudinary.com/dtbhq7drd/image/upload/${equino.fotografia}`
                : 'https://via.placeholder.com/400x365?text=Imagen+No+Disponible';
        }
    }
});

// Manejo de la carga de fotografía y no se envíe el formulario para que no muestre alerta
document.getElementById('upload_button').addEventListener('click', function () {
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