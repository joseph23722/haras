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

    const fechaEntradaInput = document.querySelector("#fechaEntrada");
    const fechaSalidaInput = document.querySelector("#fechaSalida");

    /* CHECKBOX DE ESTADIA */
    /* Contenedor Checkbox */
    const estadiaWrapper = document.querySelector("#checkboxEstadiaWrapper");
    /* Contenedor fechas */
    const fechaEstadia = document.querySelector("#fechasEstadia");

    if (estadiaWrapper) {
        estadiaWrapper.style.display = 'none';
    }
    if (fechaEstadia) {
        fechaEstadia.style.display = 'none';
    }

    idPropietarioSelect.addEventListener("change", () => {
        if (idPropietarioSelect.value) {
            if (estadiaWrapper) {
                estadiaWrapper.style.display = 'block';
            }
        } else {
            if (estadiaWrapper) {
                estadiaWrapper.style.display = 'none';
            }
            if (fechaEstadia) {
                fechaEstadia.style.display = 'none';
            }
        }
    });

    const requiereEstadiaCheckbox = document.querySelector("#requiereEstadia");
    if (requiereEstadiaCheckbox) {
        requiereEstadiaCheckbox.addEventListener("change", () => {
            if (requiereEstadiaCheckbox.checked) {
                if (fechaEstadia) {
                    fechaEstadia.style.display = 'block';
                }
            } else {
                if (fechaEstadia) {
                    fechaEstadia.style.display = 'none';
                }
            }
        });
    }

    // Buscar nacionalidades cuando el usuario escribe en el campo
    nacionalidadInput.addEventListener("input", async () => {
        const query = nacionalidadInput.value;

        if (query.length > 1) {
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
        const idNacionalidad = idNacionalidadInput.value;

        // Validación de campos obligatorios
        if (!nombreEquino || !sexo || !idTipoEquino || (!idPropietarioSelect.value && (!idNacionalidad || !fechaNacimientoInput.value))) {
            showToast('Los campos nombre, sexo, tipo de equino, y nacionalidad y fecha de nacimiento (si no hay propietario) son obligatorios.', 'ERROR');
            return;
        }
        // Si no hay propietario seleccionado, la fotografía es obligatoria
        if (!idPropietarioSelect.value && !document.querySelector("#fotografia").value) {
            showToast('Debe cargar una fotografía del equino', 'ERROR');
            return;
        }

        // Obtener los valores de las fechas, y si están vacíos, asignarles null
        const fechaEntrada = fechaEntradaInput.value || null;
        const fechaSalida = fechaSalidaInput.value || null;

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
                /* Vuelve a ocultar los campos del checkbox: fechaentrada y fechasalida */
                document.getElementById('checkboxEstadiaWrapper').style.display = 'none';
                document.getElementById('fechasEstadia').style.display = 'none';
                showToast('Equino registrado exitosamente.', 'SUCCESS');
                formEquino.reset();
            } else {
                const cleanMessage = result.message.replace(/SQLSTATE\[\d{5}\]: <<Unknown error>>: \d+ /, '');
                showToast(cleanMessage, 'ERROR');
            }
        } catch (error) {
            showToast('Error en el registro del equino. Inténtalo de nuevo más tarde.', 'ERROR');
        }
    });

    // Registrar nuevo propietario para equinos
    const formRegistrarPropietario = document.querySelector("#formRegistrarPropietario");
    const nombreHarasInput = document.querySelector("#nombreHaras");
    const closeModalButton = document.querySelector("#closeModalButton");
    const registrarPropietarioModal = new bootstrap.Modal(document.getElementById('registrarPropietario'), {
        backdrop: 'static',
        keyboard: false
    });

    formRegistrarPropietario.addEventListener("submit", async (event) => {
        event.preventDefault();
        registrarPropietarioModal.show();

        const nombreHaras = nombreHarasInput.value.trim();

        if (!nombreHaras) {
            showToast("El nombre del Haras es obligatorio.", "ERROR");
            return;
        }

        const confirmacion = await ask("¿Está seguro de que desea registrar el propietario?", "Confirmación de Registro");

        if (!confirmacion) {
            return;
        }

        try {
            const response = await fetch('../../controllers/propietario.controller.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    operation: 'registrarPropietario',
                    nombreHaras: nombreHaras
                })
            });

            if (!response.ok) {
                throw new Error('Error en la solicitud: ' + response.status);
            }

            const result = await response.json();

            if (result.status === "success") {
                showToast("Propietario registrado exitosamente", "SUCCESS");
                registrarPropietarioModal.hide();
                formRegistrarPropietario.reset();
                loadPropietarios();
            } else {
                showToast(result.message, "ERROR");
            }
        } catch (error) {
            console.error("Error al registrar propietario:", error);
            showToast("Hubo un error al registrar el propietario.", "ERROR");
        }
    });

    closeModalButton.addEventListener('click', () => {
        registrarPropietarioModal.hide();
    });

    loadPropietarios();
    loadTipoEquinos();
});