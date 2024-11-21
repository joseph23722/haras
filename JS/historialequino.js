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

        /* Para capturar el idEquino */
        document.getElementById('idEquino').value = equino.idEquino || '';

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

// SE CONECTA CON EL CLOUDINARY Y LA FUNCION ESPERA PARA PODER ENVIAR EL public_id
const myWidget = cloudinary.createUploadWidget({
    // Credenciales propias
    cloudName: "dtbhq7drd",
    uploadPreset: "upload-image-test",
}, async (error, result) => {
    if (!error && result && result.event === "success") {
        const public_id = result.info.public_id;
        console.log("Public ID de la imagen:", public_id); /* Muestra en consola el public_id almacenado */

        // Guardar el public_id en el campo hidden
        $('#foto-nueva').val(public_id);
    }
});

document.getElementById("upload_button").addEventListener(
    "click",
    function () {
        myWidget.open();
    },
    false
);

// Manejo de la carga de fotografía y no se envíe el formulario para que no muestre alerta
document.getElementById('upload_button').addEventListener('click', function () {
});

// Función para registrar la nueva foto
async function registrarNuevaFoto(public_id, idEquino) {
    try {
        const response = await fetch('../../controllers/nuevafotoequino.controller.php', {
            method: 'POST',
            body: JSON.stringify({
                operation: 'registrarNuevasFotos',
                public_id: public_id,
                idEquino: idEquino
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();
        if (result.status === 'success') {
            showToast("Foto registrada correctamente.", 'SUCCESS');
        } else {
            showToast("Error al registrar la foto.", 'ERROR');
        }
    } catch (error) {
        console.error("Error en la función registrarNuevaFoto:", error);
        showToast("Hubo un problema al registrar la foto.", 'ERROR');
    }
}


/* Formulario de Historial */
document.querySelector("#form-historial-equino").addEventListener("submit", async function (event) {
    event.preventDefault();

    const idEquino = document.getElementById('idEquino').value;
    const descripcion = quill.root.innerHTML;
    const fotoNueva = document.getElementById('foto-nueva').value;

    // Verificar si la descripción está vacía o solo tiene espacios
    const descripcionSinEspacios = descripcion.replace(/<[^>]+>/g, '').trim();

    if (!descripcionSinEspacios && !fotoNueva) {
        showToast("Porfavor inserte datos", 'ERROR');
        return;
    }

    // Si la descripción está vacía y hay foto, solo enviar la foto
    if (!descripcionSinEspacios && fotoNueva) {
        const confirmarFoto = await ask('¿Está seguro de que desea registrar la foto?');

        if (confirmarFoto) {
            await registrarNuevaFoto(idEquino, fotoNueva);
            showToast("Fotografía registrada correctamente.", 'SUCCESS');
        }
        return;
    }

    // Si la foto está vacía y hay descripción, solo registrar el historial
    if (descripcionSinEspacios && !fotoNueva) {
        const confirmarHistorial = await ask('¿Está seguro de que desea registrar el historial?');

        if (confirmarHistorial) {
            await registrarHistorialEquino(idEquino, descripcion);
            showToast("Historial registrado correctamente.", 'SUCCESS');
        }
        return;
    }

    // Si ambos tienen datos, registrar ambos con confirmación
    if (descripcionSinEspacios && fotoNueva) {
        const confirmarAmbos = await ask('¿Está seguro de que desea registrar el historial y la foto?');

        if (confirmarAmbos) {
            await registrarHistorialEquino(idEquino, descripcion);
            await registrarNuevaFoto(idEquino, fotoNueva);
            showToast("Historial y fotografía registrados correctamente.", 'SUCCESS');
        }
    }
});

// Función para registrar el historial
async function registrarHistorialEquino(idEquino, descripcion) {
    const data = {
        operation: 'registrarHistorialEquino',
        idEquino: idEquino,
        descripcion: descripcion
    };

    const response = await fetch('../../controllers/historialequino.controller.php', {
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

// Función para registrar la foto
async function registrarNuevaFoto(idEquino, public_id) {
    const data = {
        operation: 'registrarNuevasFotos',
        idEquino: idEquino,
        public_id: public_id
    };

    try {
        const response = await fetch('../../controllers/nuevafotoequino.controller.php', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();
        if (result.status === 'success') {
            showToast("Fotografía registrada correctamente.", 'SUCCESS');
        } else {
            showToast("Error al registrar la fotografía.", 'ERROR');
        }
    } catch (error) {
        console.error("Error en la función registrarNuevaFoto:", error);
        showToast("Error al registrar la fotografía.", 'ERROR');
    }
}