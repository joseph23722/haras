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
        }
    };

    // Guardar nueva vía de administración
    const guardarViaAdministracion = async () => {
        const inputNombreVia = document.getElementById("inputNombreVia");
        const inputDescripcionVia = document.getElementById("inputDescripcionVia");
        const mensajeModalVia = document.getElementById("mensajeModalVia");
        const nombreVia = inputNombreVia.value.trim();
        const descripcion = inputDescripcionVia.value.trim();

        // Verificar que el campo obligatorio esté lleno
        if (!nombreVia) {
            mensajeModalVia.innerHTML = '<p class="text-danger">El nombre de la vía es obligatorio.</p>';
            return;
        }

        try {
            const datos = {
                operation: "agregarVia",
                nombreVia: nombreVia,
                descripcion: descripcion || null
            };

            const response = await fetch("../../controllers/historialme.controller.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            mensajeModalVia.innerHTML = result.status === "success"
                ? '<p class="text-success">¡Vía de administración agregada correctamente!</p>'
                : `<p class="text-danger">${result.message}</p>`;

            if (result.status === "success") {

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
            mensajeModalVia.innerHTML = '<p class="text-danger">Error al enviar los datos al servidor.</p>';
        }
    };

    // Asignar evento al botón de guardar
    const btnGuardarVia = document.getElementById("btnGuardarViaAdministracion");
    if (btnGuardarVia) {
        btnGuardarVia.addEventListener("click", guardarViaAdministracion);
    } else {
    }

    // Función para cargar las vías de administración
    async function cargarViasAdministracion() {
        try {

            // Solicitud al backend con método GET
            const response = await fetch("../../controllers/historialme.controller.php?operation=listarVias", {
                method: "GET", // Aseguramos el uso de GET
                headers: {
                    "Content-Type": "application/json", // Indicamos que esperamos JSON
                },
            });


            // Verificamos si la respuesta fue exitosa
            if (!response.ok) {
                throw new Error(`Error en la solicitud: ${response.statusText}`);
            }

            // Procesar la respuesta como JSON
            const data = await response.json();


            if (data.status === "success") {

                // Limpiar el select (por si acaso ya tiene datos)
                selectViaAdministracion.innerHTML = '<option value="">Seleccione Vía de Administración</option>';

                // Agregar cada vía al select
                data.data.forEach((via) => {
                    const option = document.createElement("option");
                    option.value = via.idViaAdministracion; // El valor será el ID
                    option.textContent = `${via.nombreVia} - ${via.descripcion || "Sin descripción"}`;
                    selectViaAdministracion.appendChild(option);
                });

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
        }
    }

    // Función para mostrar el peso del equino seleccionado
    const mostrarPesoEquinoSeleccionado = () => {
        const equinoSelect = document.getElementById("equinoSelect");
        const selectedOption = equinoSelect.options[equinoSelect.selectedIndex];
        const pesoDiv = document.getElementById("pesokg");

        if (selectedOption) {

            const peso = selectedOption.getAttribute('data-peso');
            pesoDiv.value = peso ? `${peso} kg` : '';
        } else {
            pesoDiv.value = '';
        }
    };

    document.getElementById("equinoSelect").addEventListener("change", mostrarPesoEquinoSeleccionado);

    //registrar historial medico
    document.querySelector("#form-historial-medico").addEventListener("submit", async (event) => {
        event.preventDefault();

        if (await ask("¿Está seguro de que desea registrar este historial médico?")) {
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData.entries());
            data.operation = "registrarHistorialMedico";

            // Log detallado para verificar los datos que se envían
            try {
                const response = await fetch('../../controllers/historialme.controller.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                // Revisar si el servidor responde correctamente

                const result = await response.json();
                if (result.status === "success") {
                    showToast(result.message || "Historial médico registrado correctamente", "SUCCESS");
                    mostrarMensajeDinamico(result.message || "Historial médico registrado correctamente", "SUCCESS");
                    event.target.reset();
                } else {
                    // Si el servidor devuelve error, mostramos los detalles
                    mostrarMensajeDinamico("Error al registrar el historial: " + (result.message || "Desconocido"), "ERROR");
                }
            } catch (error) {
                // Manejo de errores del lado del cliente

                mostrarMensajeDinamico("Error al registrar el historial médico: Error de conexión o error inesperado", "ERROR");
            }
        }
    });

    function clearSelect(selectElement) {
        selectElement.innerHTML = `<option value="">Seleccione ${selectElement.id.replace('select', '')}</option>`;
    }

    // Configurar el DataTable para listar las vías de administración
    document.getElementById("btnVerViasAdministracion").addEventListener("click", function () {
        if (!$.fn.DataTable.isDataTable('#tablaViasAdministracion')) {
            $('#tablaViasAdministracion').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/haras/table-ssp/viasadministracion_datatable.php',
                    type: 'GET',
                    dataSrc: function (json) {
                        if (!json || json.error) {
                            return [];
                        }
                        return json.data;
                    },
                    error: function (xhr, error, thrown) {
                    }
                },
                columns: [
                    { data: 'idViaAdministracion', title: 'ID' },
                    { data: 'nombreVia', title: 'Nombre' },
                    { data: 'descripcion', title: 'Descripción' },
                    {
                        data: null,
                        title: 'Acciones',
                        render: function (data, type, row) {
                            return `<button onclick="window.editarViaAdministracion(${row.idViaAdministracion}, '${row.nombreVia}', '${row.descripcion}')" class="btn btn-warning btn-sm">Editar</button>`;
                        }
                    }
                ],
                language: {
                    url: '/haras/data/es_es.json'
                },
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-inline-flex me-3"l><"d-inline-flex"f>>rtip',
                initComplete: function () {
                    $('#tablaViasAdministracion_wrapper .dataTables_filter').css({
                        'margin-top': '15px'
                    });

                    $('#tablaViasAdministracion_wrapper').css({
                        'padding': '10px'
                    });
                }
            });
        }
    });

    // Función global para abrir el modal de edición
    window.editarViaAdministracion = function (id, nombre, descripcion) {
        // Obtener la instancia del modal de listado y cerrarlo
        const modalListado = bootstrap.Modal.getInstance(document.getElementById('modalVerViasAdministracion'));
        if (modalListado) {
            modalListado.hide();
        }

        // Rellenar los campos del formulario
        document.getElementById('editarIdVia').value = id;
        document.getElementById('editarNombreVia').value = nombre;
        document.getElementById('editarDescripcionVia').value = descripcion;

        // Abrir el modal de edición
        const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarViaAdministracion'), {
            backdrop: 'static',
            keyboard: false
        });
        modalEditar.show();
    };

    // Manejar el formulario de edición
    document.getElementById('formEditarViaAdministracion').addEventListener('submit', async function (event) {
        event.preventDefault();

        const id = document.getElementById('editarIdVia').value;
        const nombre = document.getElementById('editarNombreVia').value.trim();
        const descripcion = document.getElementById('editarDescripcionVia').value.trim();

        try {
            const response = await fetch(`../../controllers/historialme.controller.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ operation: 'editarViaAdministracion', id, nombre, descripcion })
            });

            const result = await response.json();
            if (result.status === 'success') {
                showToast('Vía de Administración actualizada con éxito.', 'SUCCESS');

                // Recargar el DataTable
                $('#tablaViasAdministracion').DataTable().ajax.reload();

                // Cerrar el modal de edición y reabrir el modal listado
                const modalEditar = bootstrap.Modal.getInstance(document.getElementById('modalEditarViaAdministracion'));
                if (modalEditar) {
                    modalEditar.hide();
                }

                const modalListado = new bootstrap.Modal(document.getElementById('modalVerViasAdministracion'));
                modalListado.show();
            } else {
                showToast('Error al actualizar: ' + result.message, 'ERROR');
            }
        } catch (error) {
            showToast('Ocurrió un error al guardar los cambios.', 'ERROR');
        }
    });

    loadMedicamentos();
    notificarTratamientosVeterinarios();
});