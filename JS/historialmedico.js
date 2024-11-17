document.addEventListener("DOMContentLoaded", () => {
    let historialTable;

    let selectedEquinoId = null;
    const tipoEquinoSelect = document.getElementById("tipoEquino");
    const equinoSelect = document.getElementById("equino");
    const medicamentoSelect = document.querySelector("#selectMedicamento");
    const fechaFinInput = document.querySelector("#fechaFin");
    const mensajeDiv = document.querySelector("#mensaje");
    const selectViaAdministracion = document.getElementById("viaAdministracion");


    // Verificar campos para Vías de Administración
    const verificarCamposVia = () => {
        const nombreVia = document.getElementById("inputNombreVia")?.value?.trim();
        const descripcionVia = document.getElementById("inputDescripcionVia")?.value?.trim(); // Este campo es opcional
        const mensajeModalVia = document.getElementById("mensajeModalVia");

        mensajeModalVia.innerHTML = ""; // Limpiar mensajes previos

        if (!nombreVia) {
            console.log("Campo 'Nombre de la Vía' vacío");
            mensajeModalVia.innerHTML = '<p class="text-danger">Por favor, complete el campo "Nombre de la Vía".</p>';
            return false;
        }

        return { nombreVia, descripcionVia }; // Devolver ambos campos; 'descripcionVia' será null si está vacío
    };


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

    // Guardar nueva vía de administración
    // Guardar nueva vía de administración
    const guardarViaAdministracion = async () => {
        // Obtener valores de los campos
        const inputNombreVia = document.getElementById("inputNombreVia");
        const inputDescripcionVia = document.getElementById("inputDescripcionVia");
        const mensajeModalVia = document.getElementById("mensajeModalVia");

        const nombreVia = inputNombreVia.value.trim();
        const descripcion = inputDescripcionVia.value.trim();

        // Verificar que el campo obligatorio esté lleno
        if (!nombreVia) {
            mensajeModalVia.innerHTML = '<p class="text-danger">El nombre de la vía es obligatorio.</p>';
            console.log("El campo 'Nombre de la Vía' está vacío.");
            return;
        }

        try {
            console.log("Preparando datos para enviar al backend...");
            const datos = {
                operation: "agregarVia",
                nombreVia: nombreVia,
                descripcion: descripcion || null // La descripción es opcional
            };
            console.log("Datos enviados al backend:", datos);

            const response = await fetch("../../controllers/historialme.controller.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });

            console.log("Respuesta del servidor recibida:", response);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log("Datos procesados como JSON:", result);

            mensajeModalVia.innerHTML = result.status === "success"
                ? '<p class="text-success">¡Vía de administración agregada correctamente!</p>'
                : `<p class="text-danger">${result.message}</p>`;

            if (result.status === "success") {
                console.log("Vía agregada exitosamente, actualizando la lista de vías...");

                setTimeout(() => {
                    // Resetear el formulario
                    document.getElementById("formNuevaViaAdministracion").reset();
                    mensajeModalVia.innerHTML = "";

                    // Cerrar el modal
                    bootstrap.Modal.getInstance(document.getElementById("modalAgregarViaAdministracion")).hide();

                    // Actualizar la lista de vías en el select
                    cargarViasAdministracion();
                }, 1500);
            }
        } catch (error) {
            console.error("Error al enviar los datos al servidor:", error);
            mensajeModalVia.innerHTML = '<p class="text-danger">Error al enviar los datos al servidor.</p>';
        }
    };

    // Asignar evento al botón de guardar
    const btnGuardarVia = document.getElementById("btnGuardarViaAdministracion");
    if (btnGuardarVia) {
        console.log("Botón 'Guardar' para vías de administración encontrado");
        btnGuardarVia.addEventListener("click", guardarViaAdministracion);
    } else {
        console.error("El botón #btnGuardarViaAdministracion no se encontró.");
    }



        

    // Función para cargar las vías de administración
    async function cargarViasAdministracion() {
        try {
            console.log("Iniciando solicitud para listar vías de administración...");

            // Solicitud al backend con método GET
            const response = await fetch("../../controllers/historialme.controller.php?operation=listarVias", {
                method: "GET", // Aseguramos el uso de GET
                headers: {
                    "Content-Type": "application/json", // Indicamos que esperamos JSON
                },
            });

            console.log("Respuesta del servidor recibida:", response);

            // Verificamos si la respuesta fue exitosa
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.statusText}`);
            }

            // Procesar la respuesta como JSON
            const data = await response.json();

            console.log("Datos procesados como JSON:", data);

            if (data.status === "success") {
                console.log("Vías de administración recibidas:", data.data);

                // Limpiar el select (por si acaso ya tiene datos)
                selectViaAdministracion.innerHTML = '<option value="">Seleccione Vía de Administración</option>';

                // Agregar cada vía al select
                data.data.forEach((via) => {
                    const option = document.createElement("option");
                    option.value = via.idViaAdministracion; // El valor será el ID
                    option.textContent = `${via.nombreVia} - ${via.descripcion || "Sin descripción"}`;
                    selectViaAdministracion.appendChild(option);
                });

                console.log("Select actualizado correctamente.");
            } else {
                console.error("Error al listar las vías de administración:", data.message || "Respuesta no exitosa.");
            }
        } catch (error) {
            console.error("Error en la solicitud al backend:", error.message);
        }
    }

        // Llamar a la función para cargar las vías
        cargarViasAdministracion();



    

    // Función para mostrar un mensaje de equinos notificaciones
    const notificarTratamientosVeterinarios = async () => {
        try {
            // Realizar la solicitud al backend usando GET
            const response = await fetch('../../controllers/historialme.controller.php?operation=notificarTratamientosVeterinarios', {
                method: "GET",
            });
    
            const textResponse = await response.text();
            const result = JSON.parse(textResponse);
    
            // Verifica si 'data' es un array y contiene las notificaciones
            if (Array.isArray(result.data) && result.data.length > 0) {
                result.data.forEach(notificacion => {
    
                    // Crear el mensaje dinámico con la información del tratamiento
                    const mensajeDinamico = `
                        <span class="text-primary">Equino:</span> <strong>${notificacion.nombreEquino}     ,
                        <span class="text-success">Medicamento:</span> <strong>${notificacion.nombreMedicamento}  ,
                        <span class="text-warning">Fecha Fin:</span> ${notificacion.fechaFin} ,
                        <span class="text-danger">Estado:</span> ${notificacion.TipoNotificacion} 
                    `.replace(/\s+/g, '  ').trim(); // Elimina espacios extra
    
                    mostrarMensajeDinamico(mensajeDinamico, notificacion.TipoNotificacion === 'PRONTO' ? 'WARNING' : 'INFO');
                });
            } else {
                mostrarMensajeDinamico('No hay notificaciones de tratamientos veterinarios.', 'INFO');
            }
        } catch (error) {
            mostrarMensajeDinamico('Error al obtener notificaciones de tratamientos.', 'ERROR');
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
    notificarTratamientosVeterinarios();

});