document.addEventListener("DOMContentLoaded", () => {
    let historialTable;

    let selectedEquinoId = null;
    const tipoEquinoSelect = document.getElementById("tipoEquino");
    const equinoSelect = document.getElementById("equino");
    const medicamentoSelect = document.querySelector("#selectMedicamento");
    const fechaFinInput = document.querySelector("#fechaFin");
    const mensajeDiv = document.querySelector("#mensaje");

    // Función para mostrar notificaciones en el div `mensaje`
    const mostrarMensajeDinamico = (mensaje, tipo = 'INFO') => {
        const mensajeDiv = document.getElementById('mensaje');

        if (mensajeDiv) {
            const estilos = {
                'INFO': { color: '#3178c6', bgColor: '#e7f3ff', icon: 'ℹ️' },
                'SUCCESS': { color: '#3c763d', bgColor: '#dff0d8', icon: '✅' },
                'ERROR': { color: '#a94442', bgColor: '#f2dede', icon: '❌' },
                'WARNING': { color: '#8a6d3b', bgColor: '#fcf8e3', icon: '⚠️' }
            };

            const estilo = estilos[tipo] || estilos['INFO'];

            mensajeDiv.style.color = estilo.color;
            mensajeDiv.style.backgroundColor = estilo.bgColor;
            mensajeDiv.style.fontWeight = 'bold';
            mensajeDiv.style.padding = '15px';
            mensajeDiv.style.marginBottom = '15px';
            mensajeDiv.style.border = `1px solid ${estilo.color}`;
            mensajeDiv.style.borderRadius = '8px';
            mensajeDiv.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';
            mensajeDiv.style.display = 'flex';
            mensajeDiv.style.alignItems = 'center';

            mensajeDiv.innerHTML = `<span style="margin-right: 10px; font-size: 1.2em;">${estilo.icon}</span>${mensaje}`;

            setTimeout(() => {
                mensajeDiv.innerHTML = '';
                mensajeDiv.style.border = 'none';
                mensajeDiv.style.boxShadow = 'none';
                mensajeDiv.style.backgroundColor = 'transparent';
            }, 5000);
        } else {
            console.warn('El contenedor de mensajes no está presente en el DOM.');
        }
    };

    // Restringir la fecha de fin a hoy y futuras
    fechaFinInput.min = new Date().toISOString().split("T")[0];

    // Función para cargar la tabla de historial médico
    // Función para cargar la tabla de historial médico
    const loadHistorialTable = async () => {
        if (!$.fn.DataTable.isDataTable('#historialTable')) {
            // Si el DataTable no está inicializado, crea uno con la configuración
            $('#historialTable').DataTable(configurarDataTableHistorial());
        } else {
            // Si ya está inicializado, simplemente recarga los datos
            $('#historialTable').DataTable().ajax.reload();
        }
    };
    $(document).ready(function () {
        loadHistorialTable();
    });
    

    

    // Función genérica para enviar la solicitud al servidor
    const sendRequest = async (idRegistro, accion) => {
        const data = {
            operation: 'gestionarTratamiento',
            idRegistro: idRegistro,
            accion: accion
        };

        console.log("Enviando datos al servidor:", JSON.stringify(data));

        try {
            const response = await fetch('../../controllers/historialme.controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            console.log(`Respuesta del servidor (${accion}):`, result);

            if (result.status === "success") {
                mostrarMensajeDinamico(`Registro ${accion} exitosamente`);
                $('#historialTable').DataTable().ajax.reload();
            } else {
                mostrarMensajeDinamico(`Error al ${accion} el registro: ` + (result.message || "Error desconocido"));
            }
        } catch (error) {
            console.error(`Error al ${accion} el registro:`, error);
        }
    };

    // Llamadas para cada acción
    const pausarRegistro = (idRegistro) => sendRequest(idRegistro, 'pausar');
    const continuarRegistro = (idRegistro) => sendRequest(idRegistro, 'continuar');
    const eliminarRegistro = (idRegistro) => sendRequest(idRegistro, 'eliminar');

    // Adjuntar las funciones a botones
    window.pausarRegistro = pausarRegistro;
    window.continuarRegistro = continuarRegistro;
    window.eliminarRegistro = eliminarRegistro;

    // Función para cargar los equinos según el tipo seleccionado
    async function loadEquinosPorTipo(tipoEquino) {
        const equinoSelect = document.getElementById("equinoSelect");

        try {
            const response = await fetch(`../../controllers/historialme.controller.php?operation=listarEquinosPorTipo`, {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            });

            const data = await response.json();

            // Limpiar el selector de equinos
            equinoSelect.innerHTML = '<option value="">Seleccione Equino</option>';

            // Verificar si se obtuvieron datos
            if (data.data && Array.isArray(data.data)) {
                data.data.forEach(equino => {
                    if (equino.idTipoEquino == tipoEquino) {
                        const option = document.createElement('option');
                        option.value = equino.idEquino;
                        option.textContent = equino.nombreEquino;
                        option.setAttribute('data-peso', equino.pesokg);
                        equinoSelect.appendChild(option);
                    }
                });
            } else {
                mostrarMensajeDinamico("No se encontraron equinos para el tipo seleccionado.", "WARNING");
            }
        } catch (error) {
            mostrarMensajeDinamico("Error al cargar los equinos", "ERROR");
            console.error("Error:", error);
        }
    }

    // Evento para actualizar el select de equinos cuando cambia el tipo de equino
    document.getElementById("tipoEquinoSelect").addEventListener("change", function () {
        const tipoEquino = this.value;
        if (tipoEquino) {
            loadEquinosPorTipo(tipoEquino);
        } else {
            // Limpiar si no hay tipo seleccionado
            document.getElementById("equinoSelect").innerHTML = '<option value="">Seleccione Equino</option>';
        }
    });

    // Función para cargar la lista de medicamentos
    async function loadMedicamentos() {
        try {
            const response = await fetch('../../controllers/historialme.controller.php?operation=getAllMedicamentos', {
                method: 'GET',
                headers: { 'Content-Type': 'application/json' }
            });

            const data = await response.json();
            console.log("Respuesta del servidor:", data);

            if (data.data && Array.isArray(data.data)) {
                medicamentoSelect.innerHTML = '<option value="">Seleccione Medicamento</option>';
                data.data.forEach(medicamento => {
                    const option = document.createElement('option');
                    option.value = medicamento.idMedicamento;
                    option.textContent = medicamento.nombreMedicamento;
                    medicamentoSelect.appendChild(option);
                });
            } else {
                mostrarMensajeDinamico("No se encontraron medicamentos disponibles.", "WARNING");
            }
        } catch (error) {
            mostrarMensajeDinamico("Error al cargar los medicamentos", "ERROR");
            console.error("Error:", error);
        }
    }

    // Función para mostrar el peso del equino seleccionado
    const mostrarPesoEquinoSeleccionado = () => {
        const equinoSelect = document.getElementById("equinoSelect");
        const selectedOption = equinoSelect.options[equinoSelect.selectedIndex];
        const pesoDiv = document.getElementById("pesokg");

        if (selectedOption) {
            console.log("Opción seleccionada:", selectedOption);

            const peso = selectedOption.getAttribute('data-peso');

            console.log("Peso obtenido:", peso);

            pesoDiv.value = peso ? `${peso} kg` : '';
        } else {
            pesoDiv.value = '';
            console.log("No hay opción seleccionada.");
        }
    };

    document.getElementById("equinoSelect").addEventListener("change", mostrarPesoEquinoSeleccionado);

    document.querySelector("#form-historial-medico").addEventListener("submit", async (event) => {
        event.preventDefault();

        if (await ask("¿Está seguro de que desea registrar este historial médico?")) {
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData.entries());
            data.operation = "registrarHistorialMedico";

            console.log("Datos enviados al servidor:", JSON.stringify(data, null, 2));

            try {
                const response = await fetch('../../controllers/historialme.controller.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                console.log("Respuesta JSON parseada:", result);

                if (result.status === "success") {
                    showToast(result.message || "Historial médico registrado correctamente", "SUCCESS");
                    mostrarMensajeDinamico(result.message || "Historial médico registrado correctamente", "SUCCESS");
                    event.target.reset();
                    $('#historialTable').DataTable().ajax.reload();
                } else {
                    mostrarMensajeDinamico("Error al registrar el historial: " + (result.message || "Desconocido"), "ERROR");
                }
            } catch (error) {
                console.error("Error al registrar el historial médico:", error);
                mostrarMensajeDinamico("Error al registrar el historial médico: Error de conexión o error inesperado", "ERROR");
            }
        }
    });

    function clearSelect(selectElement) {
        selectElement.innerHTML = `<option value="">Seleccione ${selectElement.id.replace('select', '')}</option>`;
    }

    loadMedicamentos();

});