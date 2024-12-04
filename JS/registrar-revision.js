document.addEventListener("DOMContentLoaded", () => {
    const idPropietarioSelect = document.querySelector("#idPropietario");
    const idEquinoSelect = document.querySelector("#idEquino");
    const costoRevisionSelec = document.querySelector("#costorevision");

    // Función para mostrar u ocultar el campo de Costo de la Revisión
    function toggleCostoRevision() {
        if (idPropietarioSelect.value) {
            costoRevisionSelec.style.display = 'block'; // Muestra el campo
        } else {
            costoRevisionSelec.style.display = 'none'; // Oculta el campo
        }
    }

    // Escuchar el cambio en la selección del propietario
    idPropietarioSelect.addEventListener('change', (event) => {
        toggleCostoRevision();
        const idPropietario = event.target.value;
        if (idPropietario) {
            loadEquinos(idPropietario);
        } else {
            idEquinoSelect.innerHTML = '<option value="">Seleccione un Equino</option>';
            loadEquinos();
        }
    });

    // Llamar a la función al cargar la página para verificar si el propietario está seleccionado
    toggleCostoRevision();

    // Cargar propietarios
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

    // Cargar los equinos del propietario seleccionado
    async function loadEquinos(idPropietario = null) {
        try {
            const response = await fetch('../../controllers/revisionbasica.controller.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    operation: 'listarYeguasPorPropietario',
                    idPropietario: idPropietario
                })
            });

            const data = await response.json();
            idEquinoSelect.innerHTML = '<option value="">Seleccione un Equino</option>';

            if (Array.isArray(data) && data.length > 0) {
                data.forEach(({
                    idEquino,
                    nombreEquino
                }) => {
                    const option = document.createElement('option');
                    option.value = idEquino;
                    option.textContent = nombreEquino;
                    idEquinoSelect.appendChild(option);
                });
            } else {
                console.log('No se encontraron yeguas disponibles.');
            }
        } catch (error) {
            console.error('Error al cargar los equinos:', error);
        }
    }

    // Escuchar el cambio en la selección del propietario
    idPropietarioSelect.addEventListener('change', (event) => {
        const idPropietario = event.target.value;
        if (idPropietario) {
            loadEquinos(idPropietario);
        } else {
            idEquinoSelect.innerHTML = '<option value="">Seleccione un Equino</option>';
            loadEquinos();
        }
    });

    // Enviar los datos del formulario para registrar la revisión del equino
    document.querySelector('#formRevisionBasica').addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = {
            operation: 'registrarRevisionEquino',
            idEquino: document.querySelector("#idEquino").value,
            idPropietario: document.querySelector("#idPropietario").value || null,
            tiporevision: document.querySelector("#tiporevision").value,
            fecharevision: document.querySelector("#fecharevision").value,
            observaciones: document.querySelector("#observaciones").value,
            costorevision: document.querySelector("#costorevision").value || null
        };

        // Validación de campos obligatorios
        if (!formData.idEquino || !formData.tiporevision || !formData.fecharevision || !formData.observaciones) {
            showToast('Por favor, complete todos los campos obligatorios.', 'WARNING', 4500);
            return;
        }

        const confirmation = await ask("¿Estás seguro de que deseas registrar la revisión del equino?", "Haras Rancho Sur");

        if (!confirmation) {
            showToast('La acción fue cancelada.', 'INFO', 4500);
            return;
        }

        try {
            const response = await fetch('../../controllers/revisionbasica.controller.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.status === 'success') {
                showToast(result.message || 'Revisión registrada correctamente.', 'SUCCESS', 4500);
                document.querySelector('#formRevisionBasica').reset();
            } else {
                showToast(result.message || 'Hubo un error al registrar la revisión.', 'ERROR', 4500);
            }
        } catch (error) {
            showToast('Hubo un error al registrar la revisión.', 'ERROR', 4500);
        }
    });

    loadPropietarios();
    loadEquinos();
});