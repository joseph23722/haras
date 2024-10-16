document.addEventListener("DOMContentLoaded", () => {
    const formMixto = document.querySelector("#form-registro-servicio-mixto");
    const idEquinoMachoSelect = document.querySelector("#idEquinoMacho");
    const idEquinoHembraSelect = document.querySelector("#idEquinoHembra");
    const idPropietarioSelect = document.querySelector("#idPropietario");
    const idEquinoExternoSelect = document.querySelector("#idEquinoExterno");
    const idDetalleMedSelect = document.querySelector("#idDetalleMed");
    const costoServicioInput = document.querySelector("#costoServicio");
    const mensajeDiv = document.querySelector("#mensaje");

    const loadOptions = async (url, selectElement) => {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Error al cargar opciones');
            }
            const data = await response.json();
            const items = Array.isArray(data) ? data : Object.values(data);
            selectElement.innerHTML = '<option value="">Seleccione</option>';
            items.forEach(item => {
                const option = document.createElement("option");
                option.value = item.idEquino || item.idPropietario;
                option.textContent = item.nombreEquino || item.nombreHaras;
                selectElement.appendChild(option);
            });
        } catch (error) {
            console.error(`Error al cargar opciones: ${error}`);
            showToast(`Error al cargar opciones: ${error.message}`, 'ERROR');
        }
    };

    const loadMedicamentos = async () => {
        try {
            const response = await fetch('../../controllers/mixto.controller.php?listarMedicamentos=1');
            if (!response.ok) {
                throw new Error('Error al cargar medicamentos');
            }
            const data = await response.json();
            idDetalleMedSelect.innerHTML = '<option value="">Seleccione Medicamento (Opcional)</option>';
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.idMedicamento;
                option.textContent = item.nombreMedicamento;
                idDetalleMedSelect.appendChild(option);
            });
        } catch (error) {
            console.error(`Error al cargar medicamentos: ${error}`);
            showToast(`Error al cargar medicamentos: ${error.message}`, 'ERROR');
        }
    };

    const soloNumerosPositivos = (inputElement) => {
        inputElement.addEventListener('input', () => {
            if (!/^\d*\.?\d*$/.test(inputElement.value)) {
                inputElement.value = inputElement.value.replace(/[^0-9.]/g, '');
            }
        });
    };

    soloNumerosPositivos(costoServicioInput);

    // Carga padrillos (tipo = 2) y yeguas (tipo = 1)
    loadOptions('../../controllers/mixto.controller.php?tipoEquino=2', idEquinoMachoSelect); // Padrillos
    loadOptions('../../controllers/mixto.controller.php?tipoEquino=1', idEquinoHembraSelect); // Yeguas
    loadOptions('../../controllers/mixto.controller.php?listarPropietarios=true', idPropietarioSelect);
    loadMedicamentos();

    const updateExternoSelect = async (genero) => {
        const idPropietario = idPropietarioSelect.value;
        if (idPropietario) {
            loadOptions(`../../controllers/mixto.controller.php?idPropietario=${idPropietario}&genero=${genero}`, idEquinoExternoSelect);
        }
    };

    idEquinoMachoSelect.addEventListener('change', () => {
        idEquinoHembraSelect.disabled = !!idEquinoMachoSelect.value;
        idEquinoExternoSelect.innerHTML = '<option value="">Seleccione Equino Externo</option>';
        if (idEquinoMachoSelect.value) {
            updateExternoSelect(2); // Cargar hembras si se seleccionó un macho
        }
    });

    idEquinoHembraSelect.addEventListener('change', () => {
        idEquinoMachoSelect.disabled = !!idEquinoHembraSelect.value;
        idEquinoExternoSelect.innerHTML = '<option value="">Seleccione Equino Externo</option>';
        if (idEquinoHembraSelect.value) {
            updateExternoSelect(1); // Cargar machos si se seleccionó una hembra
        }
    });

    idPropietarioSelect.addEventListener('change', () => {
        idEquinoExternoSelect.innerHTML = '<option value="">Seleccione Equino Externo</option>';
        if (idEquinoMachoSelect.value) {
            updateExternoSelect(2); // Cargar hembras si hay un macho seleccionado
        } else if (idEquinoHembraSelect.value) {
            updateExternoSelect(1); // Cargar machos si hay una hembra seleccionada
        }
    });

    formMixto.addEventListener("submit", async (event) => {
        event.preventDefault();
        
        // Preguntar si se quiere registrar
        const confirmacion = await ask("¿Desea registrar este servicio mixto?", "Registro de Servicio Mixto");
        if (!confirmacion) {
            return; // Salir si el usuario cancela
        }

        const formData = new FormData(formMixto);
        const data = Object.fromEntries(formData.entries());

        if (idEquinoMachoSelect.disabled) {
            data.idEquinoMacho = null;
        }

        if (idEquinoHembraSelect.disabled) {
            data.idEquinoHembra = null;
        }

        try {
            const response = await fetch('../../controllers/mixto.controller.php', {
                method: 'POST',
                body: JSON.stringify(data),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            if (!response.ok) {
                throw new Error('Error al procesar la solicitud');
            }

            const result = await response.json();
            if (result.status === "error") {
                const errorMessage = result.message;
                const cleanMessage = errorMessage.replace(/SQLSTATE\[\d{5}\]: <<Unknown error>>: \d+ /, '');
                showToast(cleanMessage, 'ERROR');
            } else {
                showToast(result.message, 'SUCCESS');
                formMixto.reset();
            }
        } catch (error) {
            showToast(`Error: ${error.message}`, 'ERROR');
            console.error(`Error al registrar servicio mixto: ${error.message}`);
        }
    });
});