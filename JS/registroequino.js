document.addEventListener("DOMContentLoaded", () => {
    const idPropietarioSelect = document.querySelector("#idPropietario");
    const fechaNacimientoInput = document.querySelector("#fechaNacimiento");
    const tipoEquinoSelect = document.querySelector("#TipoEquino");
    const formEquino = document.querySelector("#form-registro-equino");
    const sexoSelect = document.querySelector("#sexo");
    const pesoKgInput = document.querySelector("#pesokg");
    const nacionalidadInput = document.querySelector("#nacionalidad");
    const sugerenciasNacionalidad = document.querySelector("#sugerenciasNacionalidad");
    const idNacionalidadInput = document.querySelector("#idNacionalidad");

    // Buscar nacionalidades cuando el usuario escribe en el campo
    nacionalidadInput.addEventListener("input", async () => {
        const query = nacionalidadInput.value;

        if (query.length > 3) {
            try {
                const response = await fetch('../../controllers/registrarequino.controller.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        operation: 'buscarNacionalidad',
                        nacionalidad: query
                    })
                });

                if (!response.ok) throw new Error('Error en la solicitud: ' + response.status);

                const data = await response.json();
                sugerenciasNacionalidad.innerHTML = ''; // Limpiar opciones anteriores

                if (Array.isArray(data) && data.length > 0) {
                    sugerenciasNacionalidad.style.display = 'block';
                    data.forEach(({
                        idNacionalidad,
                        nacionalidad
                    }) => {
                        const option = document.createElement('option');
                        option.value = nacionalidad;
                        option.dataset.id = idNacionalidad;
                        sugerenciasNacionalidad.appendChild(option);
                    });
                } else {
                    sugerenciasNacionalidad.style.display = 'none';
                    console.log('No se encontraron nacionalidades.');
                }
            } catch (error) {
                console.error('Error al buscar la nacionalidad:', error);
            }
        } else {
            sugerenciasNacionalidad.innerHTML = '';
            sugerenciasNacionalidad.style.display = 'none';
        }
    });

    // Captura el idNacionalidad al seleccionar una nacionalidad de las sugerencias (lista)
    nacionalidadInput.addEventListener("change", () => {
        const selectedOption = Array.from(sugerenciasNacionalidad.options)
            .find(option => option.value === nacionalidadInput.value);

        if (selectedOption) {
            idNacionalidadInput.value = selectedOption.dataset.id;
        } else {
            idNacionalidadInput.value = "";
        }
    });

    async function loadPropietarios() {
        try {
            const response = await fetch('../../controllers/registrarequino.controller.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    operation: 'listarPropietarios'
                })
            });

            const data = await response.json();
            idPropietarioSelect.innerHTML = '<option value="">Haras Rancho Sur</option>';

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(({
                    idPropietario,
                    nombreHaras
                }) => {
                    const option = document.createElement('option');
                    option.value = idPropietario;
                    option.textContent = nombreHaras;
                    idPropietarioSelect.appendChild(option);
                });
            } else {
                console.log('No se encontraron propietarios disponibles.');
            }
        } catch (error) {
            console.error('Error al cargar los propietarios:', error);
        }
    }

    // Función para cargar tipos de equinos
    async function loadTipoEquinos() {
        try {
            const response = await fetch('../../controllers/registrarequino.controller.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    operation: 'listarTipoEquinos'
                })
            });

            const data = await response.json();
            tipoEquinoSelect.innerHTML = '<option value="">Seleccione Tipo Equino</option>';

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(({
                    idTipoEquino,
                    tipoEquino
                }) => {
                    const option = document.createElement('option');
                    option.value = idTipoEquino;
                    option.textContent = tipoEquino;
                    tipoEquinoSelect.appendChild(option);
                });
            } else {
                console.log('No se encontraron tipos de equinos disponibles.');
            }
        } catch (error) {
            console.error('Error al cargar los tipos de equinos:', error);
        }
    }

    // Función para calcular la diferencia en días entre dos fechas
    function getDifferenceInDays(dateFrom, dateTo) {
        const timeDifference = dateTo - dateFrom;
        return Math.floor(timeDifference / (1000 * 3600 * 24));
    }

    // Función para aplicar la lógica de validación de tipo de equino según la edad en días
    function applyTipoEquinoLogic() {
        const fechaNacimiento = new Date(fechaNacimientoInput.value);
        const today = new Date();
        const diasDiferencia = getDifferenceInDays(fechaNacimiento, today);
        const sexo = sexoSelect.value;

        tipoEquinoSelect.innerHTML = '';

        // Lógica de validación basada en días
        if (diasDiferencia <= 180) {  // Recién nacido menor o igual a 6 meses
            tipoEquinoSelect.innerHTML = '<option value="5">Recién nacido</option>';

        } else if (diasDiferencia > 180 && diasDiferencia <= 730) {  // Potrillo/Potranca menor a 2 años
            if (sexo === 'Macho') {
                tipoEquinoSelect.innerHTML = '<option value="4">Potrillo</option>';
            } else if (sexo === 'Hembra') {
                tipoEquinoSelect.innerHTML = '<option value="3">Potranca</option>';
            }

        } else if (diasDiferencia > 730) {  // Equinos mayores de 730 días es mayor a 2 años o 24 meses
            if (sexo === 'Macho') {
                tipoEquinoSelect.innerHTML = `
            <option value="2">Padrillo</option>
            <option value="4">Potrillo</option>`;
            } else if (sexo === 'Hembra') {
                tipoEquinoSelect.innerHTML = `
            <option value="1">Yegua</option>
            <option value="3">Potranca</option>`;
            }
        }
    }

    // Función para manejar el cambio en propietario
    idPropietarioSelect.addEventListener("change", () => {
        if (idPropietarioSelect.value) {
            tipoEquinoSelect.innerHTML = '';
            sexoSelect.addEventListener("change", () => {
                const sexo = sexoSelect.value;
                if (sexo === "Macho") {
                    tipoEquinoSelect.innerHTML = '<option value="2">Padrillo</option>';
                } else if (sexo === "Hembra") {
                    tipoEquinoSelect.innerHTML = '<option value="1">Yegua</option>';
                }
            });

            nacionalidadInput.value = '';
            fechaNacimientoInput.value = '';
            pesoKgInput.disabled = true;
            fechaNacimientoInput.disabled = true;

            tipoEquinoSelect.innerHTML = '';
            fechaNacimientoInput.removeEventListener("change", applyTipoEquinoLogic);
            sexoSelect.removeEventListener("change", applyTipoEquinoLogic);

        } else {
            fechaNacimientoInput.addEventListener("change", applyTipoEquinoLogic);
            sexoSelect.addEventListener("change", applyTipoEquinoLogic);
            applyTipoEquinoLogic();

            pesoKgInput.disabled = false;
            fechaNacimientoInput.disabled = false;
        }
    });

    fechaNacimientoInput.addEventListener("change", applyTipoEquinoLogic);
    sexoSelect.addEventListener("change", applyTipoEquinoLogic);

    // Función para validar si un número es positivo y mayor que 0
    function isValidPositiveNumber(value) {
        return !isNaN(value) && parseFloat(value) > 0;
    }

    // SE CONECTA CON EL CLOUDINARY Y LA FUNCION ESPERA PARA PODER ENVIAR EL public_id
    const myWidget = cloudinary.createUploadWidget({
        // Credenciales propias
        cloudName: "dtbhq7drd",
        uploadPreset: "upload-image-test",
    }, async (error, result) => {
        if (!error && result && result.event === "success") {
            // Mostrar el public_id en la consola para verificar que se ha capturado correctamente
            const public_id = result.info.public_id;
            console.log("Public ID de la imagen:", public_id);

            // Guardar el public_id en el campo hidden
            $('#fotografia').val(public_id);
        }
    });

    document.getElementById("upload_button").addEventListener(
        "click",
        function () {
            myWidget.open();
        },
        false
    );

    formEquino.addEventListener("submit", async (event) => {
        event.preventDefault();

        // Obtener los valores de los campos
        const nombreEquino = document.querySelector("#nombreEquino").value;
        const sexo = sexoSelect.value;
        const idTipoEquino = tipoEquinoSelect.value;
        const idNacionalidad = nacionalidadInput.value;

        // Validación de campos obligatorios
        if (!nombreEquino || !sexo || !idTipoEquino || (!idPropietarioSelect.value && (!idNacionalidad || !fechaNacimientoInput.value))) {
            showToast('Los campos nombre, sexo, tipo de equino, y nacionalidad y fecha de nacimiento (si no hay propietario) son obligatorios.', 'ERROR');
            console.log('Registro fallido: faltan campos obligatorios.');
            return;
        }

        // Si no hay propietario seleccionado, la fotografía es obligatoria
        if (!idPropietarioSelect.value && !document.querySelector("#fotografia").value) {
            showToast('Debe cargar una fotografía del equino', 'ERROR');
            return;
        }

        // Confirmar si el usuario quiere registrar el equino
        const confirm = await ask("¿Está seguro de que desea registrar el equino?", "Registro de Equinos");
        if (!confirm) return;

        // Obtener el public_id de la fotografía
        const fotografiaPublicId = document.querySelector("#fotografia").value;

        // Crear un objeto FormData para enviar con el formulario
        const formData = new FormData(formEquino);
        const data = {
            operation: 'registrarEquino',
            idNacionalidad: idNacionalidad,
            fotografia: fotografiaPublicId // Incluir el public_id en los datos
        };

        // Recorrer el FormData y añadir los valores a 'data'
        formData.forEach((value, key) => {
            data[key] = value === "" && key === 'idPropietario' ? null : value;
        });

        try {
            const response = await fetch('../../controllers/registrarequino.controller.php', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.status === "success") {
                showToast('Equino registrado exitosamente.', 'SUCCESS');
                formEquino.reset();
            } else {
                const cleanMessage = result.message.replace(/SQLSTATE\[\d{5}\]: <<Unknown error>>: \d+ /, '');
                showToast(cleanMessage, 'ERROR');
            }
        } catch (error) {
            console.error('Error en el envío del formulario:', error);
            showToast('Error en el registro del equino. Inténtalo de nuevo más tarde.', 'ERROR');
        }
    });

    // Modal para editar Equinos, que tiene que realizar una busqueda, y todos los datos lo muestre
    document.querySelector("#buscar-equino").addEventListener("click", function () {
        const nombreEquino = document.getElementById("buscarEquino").value;

        fetch('../../controllers/registrarequino.controller.php', {
            method: 'POST',
            body: JSON.stringify({
                operation: 'buscarEquinoPorNombre',
                nombreEquino: nombreEquino
            }),
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);

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
                    document.getElementById("estado").value = '';
                } else {
                    const equino = data[0];
                    document.getElementById("fechanacimiento").value = equino.fechaNacimiento || '';
                    document.getElementById("nacionalidades").value = equino.nacionalidad || '';
                    document.getElementById("propietario").value = equino.idPropietario || 'Haras Rancho Sur';
                    document.getElementById("genero").value = equino.sexo || '';
                    document.getElementById("tipoEquino").value = equino.tipoEquino || '';
                    document.getElementById("idEstadoMonta").value = equino.estadoMonta || '';
                    document.getElementById("peso").value = equino.pesokg || 'Por pesar';
                    document.getElementById("fotografia").value = equino.fotografia || '';
                    document.getElementById("estado").value = equino.estado || 'Desconocido';
                    document.getElementById("idEquino").value = equino.idEquino;
                }
            })
            .catch(error => console.error("Error al buscar el equino:", error));
    });

    loadPropietarios();
    loadTipoEquinos();
});