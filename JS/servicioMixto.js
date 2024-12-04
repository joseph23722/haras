document.addEventListener("DOMContentLoaded", () => {
    const formMixto = document.querySelector("#form-registro-servicio-mixto");
    const idEquinoMachoSelect = document.querySelector("#idEquinoMacho");
    const idEquinoHembraSelect = document.querySelector("#idEquinoHembra");
    const idPropietarioSelect = document.querySelector("#idPropietario");
    const idEquinoExternoSelect = document.querySelector("#idEquinoExterno");
    const idDetalleMedSelect = document.querySelector("#idDetalleMed");
    const medicamentoCampos = document.querySelector("#medicamentoCampos");
    const unidadSelect = document.querySelector("#unidad");
    const cantidadAplicadaInput = document.querySelector("#cantidadAplicada");
    const costoServicioInput = document.querySelector("#costoServicio");
    const fechaServicioInput = document.querySelector("#fechaServicio");

    // Función para cargar opciones en select
    const loadOptions = async (url, selectElement) => {
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error("Error al cargar opciones");
            }
            const data = await response.json();
            // Convertir a array si no lo es
            const items = Array.isArray(data) ? data : Object.values(data);

            selectElement.innerHTML = '<option value="">Seleccione</option>';
            items.forEach(item => {
                const option = document.createElement("option");
                option.value = item.idEquino || item.idPropietario || item.idMedicamento;
                option.textContent = item.nombreEquino || item.nombreHaras || item.nombreMedicamento;
                selectElement.appendChild(option);
            });
        } catch (error) {
            showToast(`Error al cargar opciones: ${error.message}`, "ERROR");
        }
    };

    const loadUnidadesPorMedicamento = async (idMedicamento) => {
        try {
            const response = await fetch(`../../controllers/mixto.controller.php?action=listarUnidadesPorMedicamento&idMedicamento=${idMedicamento}`);
            if (!response.ok) {
                throw new Error("Error al cargar unidades");
            }
            const data = await response.json();
            unidadSelect.innerHTML = '<option value="">Seleccione Unidad</option>';
            data.forEach(unidad => {
                const option = document.createElement("option");
                option.value = unidad.idUnidad;
                option.textContent = unidad.nombreUnidad;
                unidadSelect.appendChild(option);
            });

            unidadSelect.disabled = false;
            cantidadAplicadaInput.disabled = false;
        } catch (error) {
            showToast(`Error al cargar unidades: ${error.message}`, "ERROR");
        }
    };

    // Cargar medicamentos
    const loadMedicamentos = async () => {
        try {
            const response = await fetch('../../controllers/mixto.controller.php?action=listarMedicamentos');
            if (!response.ok) {
                throw new Error("Error al cargar medicamentos");
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
            showToast(`Error al cargar medicamentos: ${error.message}`, "ERROR");
        }
    };

    // Manejar el select de equinos externos según género y propietario
    updateExternoSelect = async (genero) => {
        const idPropietario = idPropietarioSelect.value;
        if (!idPropietario) return;

        const url = `../../controllers/mixto.controller.php?action=listarEquinosExternosPorPropietarioYGenero&idPropietario=${idPropietario}&genero=${genero}`;
        await loadOptions(url, idEquinoExternoSelect);
    };

    idEquinoMachoSelect.addEventListener("change", () => {
        idEquinoHembraSelect.disabled = !!idEquinoMachoSelect.value;
        idEquinoExternoSelect.innerHTML = '<option value="">Seleccione Equino Externo</option>';
        if (idEquinoMachoSelect.value) {
            updateExternoSelect(2); // Cargar hembras si se seleccionó un macho
        }
    });

    idEquinoHembraSelect.addEventListener("change", () => {
        idEquinoMachoSelect.disabled = !!idEquinoHembraSelect.value;
        idEquinoExternoSelect.innerHTML = '<option value="">Seleccione Equino Externo</option>';
        if (idEquinoHembraSelect.value) {
            updateExternoSelect(1); // Cargar machos si se seleccionó una hembra
        }
    });

    idPropietarioSelect.addEventListener("change", () => {
        idEquinoExternoSelect.innerHTML = '<option value="">Seleccione Equino Externo</option>';
        if (idEquinoMachoSelect.value) {
            updateExternoSelect(2); // Cargar hembras si hay un macho seleccionado
        } else if (idEquinoHembraSelect.value) {
            updateExternoSelect(1); // Cargar machos si hay una hembra seleccionada
        }
    });

    // Mostrar/ocultar campos relacionados con medicamentos
    idDetalleMedSelect.addEventListener("change", (event) => {
        const idMedicamento = event.target.value;
        if (idMedicamento) {
            medicamentoCampos.style.display = "block"; // Mostrar campos relacionados con el medicamento
            loadUnidadesPorMedicamento(idMedicamento); // Llamar a la función para cargar las unidades
        } else {
            medicamentoCampos.style.display = "none"; // Ocultar campos
            unidadSelect.innerHTML = '<option value="">Seleccione Unidad</option>';
            cantidadAplicadaInput.value = '';
            unidadSelect.disabled = true;
            cantidadAplicadaInput.disabled = true;
        }
    });

    // Cargar datos iniciales
    loadOptions('../../controllers/mixto.controller.php?action=listarPropietarios', idPropietarioSelect);
    loadOptions('../../controllers/mixto.controller.php?action=listarEquinosPropios&tipoEquino=2', idEquinoMachoSelect);
    loadOptions('../../controllers/mixto.controller.php?action=listarEquinosPropios&tipoEquino=1', idEquinoHembraSelect);
    loadMedicamentos();

    // Manejar envío del formulario
    formMixto.addEventListener("submit", async (event) => {
        event.preventDefault();

        const confirmacion = await ask("¿Desea registrar este servicio mixto?", "Registro de Servicio Mixto");
        if (!confirmacion) return;

        const formData = new FormData(formMixto);
        const data = Object.fromEntries(formData.entries());

        // Validar si se seleccionó un medicamento y su uso
        if (data.idMedicamento) {
            if (!data.usoMedicamento) {
                showToast("Debe seleccionar si el medicamento es para el Padrillo, Yegua o Equino Externo.", "ERROR");
                return;
            }

            if (!data.unidad || !data.cantidadAplicada) {
                showToast("Debe completar los campos de unidad y cantidad aplicada si selecciona un medicamento.", "ERROR");
                return;
            }
        }

        // Obtener el texto de la unidad seleccionada
        const unidadSeleccionada = unidadSelect.options[unidadSelect.selectedIndex]?.textContent;
        data.unidad = unidadSeleccionada || ""; // Usar texto en lugar de idUnidad

        // Agregar acción al objeto
        data.action = "registrarServicioMixto";

        // Obtener la fecha de servicio
        const fechaServicio = fechaServicioInput.value;
        if (!fechaServicio) {
            showToast("Debe seleccionar una fecha de servicio.", "ERROR");
            return;
        }
        data.fechaServicio = fechaServicio;
        data.fechaAplicacion = fechaServicio;

        try {
            // Enviar solicitud
            const response = await fetch('../../controllers/mixto.controller.php', {
                method: "POST",
                body: JSON.stringify(data),
                headers: { "Content-Type": "application/json" },
            });

            // Obtener respuesta como texto para depuración
            const textResponse = await response.text();
            // Intentar convertir respuesta a JSON
            let result;
            try {
                result = JSON.parse(textResponse);
            } catch (jsonError) {
                showToast(`Error en el formato de la respuesta: ${textResponse}`, "ERROR");
                return;
            }

            // Manejar respuesta JSON
            if (result.status === "success") {
                showToast(result.message, "SUCCESS");
                formMixto.reset();
            } else if (result.status === "error") {
                // Mostrar el mensaje de error del procedimiento almacenado o validación del backend
                showToast(result.message, "ERROR");
            } else {
                showToast("Ocurrió un error inesperado.", "ERROR");
            }
        } catch (error) {
            showToast(`Error al registrar servicio mixto: ${error.message}`, "ERROR");
        }
    });
});
