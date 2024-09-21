document.addEventListener("DOMContentLoaded", () => {
    const formPropio = document.querySelector("#form-registro-servicio-propio");
    const idPadrilloSelect = document.querySelector("#idPadrillo");
    const idYeguaSelect = document.querySelector("#idYegua");
    const idDetalleMedSelect = document.querySelector("#idDetalleMed");
    const mensajeDiv = document.querySelector("#mensaje");

    const loadOptions = async (url, selectElement, tipoEquino) => {
        try {
            const response = await fetch(`${url}?tipoEquino=${tipoEquino === 'padrillo' ? 2 : 1}`);
            if (!response.ok) {
                throw new Error('Error al cargar opciones');
            }
            const data = await response.json();

            // Convierte el objeto a un arreglo
            const items = Object.values(data);

            selectElement.innerHTML = '<option value="">Seleccione</option>';
            items.forEach(item => {
                const option = document.createElement("option");
                option.value = item.idEquino;
                option.textContent = item.nombreEquino;
                selectElement.appendChild(option);
            });
        } catch (error) {
            console.error(`Error al cargar opciones: ${error}`);
        }
    };

    const loadMedicamentos = async () => {
        try {
            const response = await fetch('../../controllers/Propio.controller.php?listarMedicamentos=1');
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
        }
    };

    // Carga padrillos (tipo = 2) y yeguas (tipo = 1)
    loadOptions('../../controllers/Propio.controller.php', idPadrilloSelect, 'padrillo');
    loadOptions('../../controllers/Propio.controller.php', idYeguaSelect, 'yegua');
    loadMedicamentos();  

    formPropio.addEventListener("submit", async (event) => {
        event.preventDefault();

        const formData = new FormData(formPropio);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('../../controllers/Propio.controller.php', {
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
            mensajeDiv.innerText = result.message;
            if (result.status === "success") {
                formPropio.reset();
            }
        } catch (error) {
            console.error(`Error al registrar servicio propio: ${error}`);
            mensajeDiv.innerText = "Error al registrar el servicio.";
        }
    });
});
