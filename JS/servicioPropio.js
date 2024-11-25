document.addEventListener("DOMContentLoaded", () => {
    const formPropio = document.querySelector("#form-registro-servicio-propio");
    const idPadrilloSelect = document.querySelector("#idPadrillo");
    const idYeguaSelect = document.querySelector("#idYegua");
    const idDetalleMedSelect = document.querySelector("#idDetalleMed");
    const medicamentoCampos = document.querySelector("#medicamentoCampos"); // Sección de campos relacionados con el medicamento
    const unidadSelect = document.querySelector("#unidad");
    const cantidadAplicadaInput = document.querySelector("#cantidadAplicada");

    // Función para cargar opciones de equinos
    const loadOptions = async (url, selectElement, tipoEquino) => {
        try {
            const response = await fetch(`${url}?action=listarEquinosPropios&tipoEquino=${tipoEquino === 'padrillo' ? 2 : 1}`);
            const textResponse = await response.text();
            console.log(`Respuesta como texto (${tipoEquino}):`, textResponse);

            const data = JSON.parse(textResponse);
            console.log(`Datos convertidos a JSON (${tipoEquino}):`, data);

            selectElement.innerHTML = '<option value="">Seleccione</option>';
            Object.values(data).forEach(item => {
                const option = document.createElement("option");
                option.value = item.idEquino;
                option.textContent = item.nombreEquino;
                selectElement.appendChild(option);
            });
        } catch (error) {
            console.error(`Error al cargar ${tipoEquino}:`, error);
            showToast(`Error al cargar opciones de ${tipoEquino}: ${error.message}`, "ERROR");
        }
    };

    // Cargar medicamentos
    const loadMedicamentos = async () => {
        try {
            const response = await fetch('../../controllers/Propio.controller.php?action=listarMedicamentos');
            const textResponse = await response.text();
            console.log("Respuesta como texto (medicamentos):", textResponse);

            const data = JSON.parse(textResponse);
            console.log("Datos convertidos a JSON (medicamentos):", data);

            idDetalleMedSelect.innerHTML = '<option value="">Seleccione Medicamento (Opcional)</option>';
            data.forEach(item => {
                const option = document.createElement("option");
                option.value = item.idMedicamento;
                option.textContent = item.nombreMedicamento;
                idDetalleMedSelect.appendChild(option);
            });
        } catch (error) {
            console.error("Error al cargar medicamentos:", error);
            showToast(`Error al cargar medicamentos: ${error.message}`, "ERROR");
        }
    };

    // Cargar unidades según el medicamento seleccionado
    const loadUnidadesPorMedicamento = async (idMedicamento) => {
        try {
            const response = await fetch(`../../controllers/Propio.controller.php?action=listarUnidadesPorMedicamento&idMedicamento=${idMedicamento}`);
            const textResponse = await response.text();
            console.log("Respuesta como texto (unidades):", textResponse);

            const data = JSON.parse(textResponse);
            console.log("Datos convertidos a JSON (unidades):", data);

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
            console.error("Error al cargar unidades:", error);
            showToast(`Error al cargar unidades: ${error.message}`, "ERROR");
        }
    };

    // Mostrar/ocultar campos relacionados con medicamentos
    idDetalleMedSelect.addEventListener("change", (event) => {
        const idMedicamento = event.target.value;
        if (idMedicamento) {
            medicamentoCampos.style.display = "block"; // Mostrar campos relacionados con el medicamento
            loadUnidadesPorMedicamento(idMedicamento);
        } else {
            medicamentoCampos.style.display = "none"; // Ocultar campos
            unidadSelect.innerHTML = '<option value="">Seleccione Unidad</option>';
            cantidadAplicadaInput.value = '';
            unidadSelect.disabled = true;
            cantidadAplicadaInput.disabled = true;
        }
    });

    // Cargar datos iniciales
    loadOptions('../../controllers/Propio.controller.php', idPadrilloSelect, 'padrillo');
    loadOptions('../../controllers/Propio.controller.php', idYeguaSelect, 'yegua');
    loadMedicamentos();

    // Manejar envío del formulario
    formPropio.addEventListener("submit", async (event) => {
        event.preventDefault();
    
        const confirmacion = await ask("¿Desea registrar este servicio propio?", "Registro de Servicio Propio");
        if (!confirmacion) return;
    
        const formData = new FormData(formPropio);
        const data = Object.fromEntries(formData.entries());
        data.action = "registrarServicioPropio";
    
        // Convertir el ID de la unidad en su texto correspondiente
        const unidadSeleccionada = unidadSelect.options[unidadSelect.selectedIndex];
        data.unidad = unidadSeleccionada ? unidadSeleccionada.textContent : "";
    
        console.log("Datos enviados (antes de validación):", data);
    
        // Validar campos relacionados con medicamentos
        if (data.idMedicamento) {
            if (!data.unidad || !data.cantidadAplicada) {
                showToast("Debe seleccionar una unidad y cantidad aplicada si selecciona un medicamento.", "ERROR");
                return;
            }
        }
    
        try {
            const response = await fetch('../../controllers/Propio.controller.php', {
                method: 'POST',
                body: JSON.stringify(data),
                headers: { 'Content-Type': 'application/json' }
            });
    
            const textResponse = await response.text();
            console.log("Respuesta como texto (registro):", textResponse);
    
            const result = JSON.parse(textResponse);
            console.log("Respuesta como JSON (registro):", result);
    
            if (result.status === "success") {
                showToast(result.message, "SUCCESS");
                formPropio.reset();
                unidadSelect.innerHTML = '<option value="">Seleccione Unidad</option>';
                cantidadAplicadaInput.disabled = true;
                unidadSelect.disabled = true;
                medicamentoCampos.style.display = "none"; // Ocultar los campos tras enviar
    
                if (data.idMedicamento) {
                    console.log("Buscando historial actualizado de dosis aplicadas...");
                    const historialResponse = await fetch("../../controllers/Propio.controller.php?action=obtenerHistorialDosisAplicadas");
                    const historialText = await historialResponse.text();
                    console.log("Respuesta como texto (historial):", historialText);
    
                    const historialData = JSON.parse(historialText);
                    console.log("Historial de dosis aplicada (actualizado):", historialData);
                }
            } else {
                showToast(result.message, "ERROR");
                console.error("Error al registrar servicio propio:", result.message);
            }
        } catch (error) {
            console.error("Error al registrar servicio propio:", error.message);
            showToast(`Error al registrar servicio propio: ${error.message}`, "ERROR");
        }
    });
});
