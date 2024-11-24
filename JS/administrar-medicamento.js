document.addEventListener("DOMContentLoaded", () => {

    let notificacionesMostradas = false;

    const formRegistrarMedicamento = document.querySelector("#form-registrar-medicamento");
    const formEntrada = document.querySelector("#formEntrada");
    const formSalida = document.querySelector("#formSalida");
    const medicamentosTable = document.querySelector("#medicamentos-table");
    const tipoMedicamentoSelect = document.querySelector("#tipo");
    const presentacionSelect = document.querySelector("#presentacion");
    const messageArea = document.getElementById("message-area");
    const btnSugerencias = document.getElementById("btnSugerencias");
    // Función para mostrar mensajes dinámicos para medicamentos
    function mostrarMensaje(mensaje, tipo = 'INFO') {
        const messageArea = document.getElementById("message-area"); // Asegúrate de tener un div con el id 'messageAreaMedicamento'

        if (messageArea) {
            // Definición de estilos para cada tipo de mensaje
            const estilos = {
                'INFO': {
                    color: '#3178c6',
                    bgColor: '#e7f3ff',
                    icon: 'ℹ️'
                },
                'SUCCESS': {
                    color: '#3c763d',
                    bgColor: '#dff0d8',
                    icon: '✅'
                },
                'ERROR': {
                    color: '#a94442',
                    bgColor: '#f2dede',
                    icon: '❌'
                },
                'WARNING': {
                    color: '#8a6d3b',
                    bgColor: '#fcf8e3',
                    icon: '⚠️'
                }
            };

            // Obtener los estilos correspondientes al tipo de mensaje
            const estilo = estilos[tipo] || estilos['INFO'];

            // Aplicar estilos al contenedor del mensaje
            messageArea.style.display = 'flex';
            messageArea.style.alignItems = 'center';
            messageArea.style.color = estilo.color;
            messageArea.style.backgroundColor = estilo.bgColor;
            messageArea.style.fontWeight = 'bold';
            messageArea.style.padding = '15px';
            messageArea.style.marginBottom = '15px';
            messageArea.style.border = `1px solid ${estilo.color}`;
            messageArea.style.borderRadius = '8px';
            messageArea.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';

            // Mostrar el mensaje con un icono
            messageArea.innerHTML = `<span style="margin-right: 10px; font-size: 1.2em;">${estilo.icon}</span>${mensaje}`;

            // Ocultar el mensaje después de 5 segundos
            setTimeout(() => {
                messageArea.style.display = 'none';
                messageArea.innerHTML = ''; // Limpiar contenido
                messageArea.style.border = 'none';
                messageArea.style.boxShadow = 'none';
                messageArea.style.backgroundColor = 'transparent';
            }, 5000);
        } else {
            console.warn('El contenedor de mensajes para medicamentos no está presente en el DOM.');
        }
    }


    // Función para validar el campo lote
    async function validarLote(loteInput) {
        const loteValue = loteInput.value.trim();
        if (loteValue === 'LOTE-') {
            await ask('El campo Lote está incompleto. Agrega algo después de "LOTE-".');
            return false;
        }
        return true;
    }


    // Evento para el botón de sugerencias
    $(document).ready(function () {
        // Configurar DataTable para sugerencias de medicamentos
        const tablaSugerencias = $('#tablaSugerencias').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/haras/table-ssp/medicamento_datatable.php',
                type: "GET",
                error: function (xhr, error, thrown) {
                    console.error("Error al cargar las sugerencias:", {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText
                    });
                }
            },
            columns: [
                { data: 'tipo', title: 'Tipo de Medicamento' },
                { data: 'presentaciones', title: 'Presentaciones' },
                { data: 'dosis', title: 'Dosis' },
                {
                    data: null,
                    title: 'Acciones',
                    render: function (data, type, row) {
                        return `
                            <button onclick="editarSugerencia(${row.idCombinacion}, '${row.tipo}', '${row.presentaciones}', '${row.dosis}')" 
                                    class="btn btn-warning btn-sm">Editar</button>`;
                    }
                }
            ],
            pageLength: 10, // Mostrar 10 registros por página
            lengthMenu: [10, 25, 50], // Opciones de cantidad de registros
            order: [[0, 'asc']], // Ordenar por tipo de medicamento
            language: {
                url: '/haras/data/es_es.json' // Traducción al español
            },
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-inline-flex me-3"l><"d-inline-flex"f>>rtip',
            initComplete: function () {
                console.log("Tabla de sugerencias inicializada correctamente.");
            }
        });
    
        // Recargar tabla al abrir el modal de sugerencias
        $('#btnSugerencias').on('click', function () {
            tablaSugerencias.ajax.reload(); // Recargar datos desde el servidor
        });
    });
    
    
    
    

    document.getElementById('formEditarSugerencia').addEventListener('submit', async (event) => {
        event.preventDefault();
    
        // Obtener los valores del formulario
        const idCombinacion = document.getElementById('editarId').value.trim();
        const tipo = document.getElementById('editarTipo').value.trim();
        const presentacion = document.getElementById('editarPresentacion').value.trim();
        const dosis = document.getElementById('editarDosis').value.trim();
    
        // Validar datos
        if (!idCombinacion || !tipo || !presentacion || !dosis) {
            showToast("Todos los campos son obligatorios.", "ERROR");
            return;
        }
    
        try {
            console.log("Enviando datos al servidor para actualizar...");
    
            // Realizar la solicitud POST al controlador
            const response = await fetch(`../../controllers/admedi.controller.php`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    operation: "editarSugerenciaMedicamento", // Asegúrate de que este valor coincida con el caso en el controlador
                    idCombinacion, // ID de la combinación
                    tipo,          // Nuevo tipo de medicamento
                    presentacion,  // Nueva presentación
                    unidad: dosis  // Nueva unidad de medida
                })
            });
    
            const result = await response.json();
    
            if (result.status === "success") {
                console.log("Actualización exitosa.");
                showToast("Sugerencia actualizada correctamente.", "SUCCESS");
                $('#modalEditarSugerencia').modal('hide'); // Cerrar el modal de edición
                $('#tablaSugerencias').DataTable().ajax.reload(); // Recargar la tabla
            } else {
                console.error("Error en la respuesta del servidor:", result.message);
                showToast("Error: " + result.message, "ERROR");
            }
        } catch (error) {
            console.error("Error al actualizar la sugerencia:", error.message);
            showToast("Ocurrió un error al actualizar la sugerencia.", "ERROR");
        }
    });
    
    
    window.editarSugerencia = function (idCombinacion, tipo, presentacion, dosis) {
        console.log("Editar sugerencia con ID:", idCombinacion);
    
        // Asignar los valores de la sugerencia a los campos del formulario de edición
        document.getElementById('editarId').value = idCombinacion; // Asigna el ID al campo oculto
        document.getElementById('editarTipo').value = tipo;
        document.getElementById('editarPresentacion').value = presentacion;
        document.getElementById('editarDosis').value = dosis;
    
        // Cerrar el modal de sugerencias y abrir el modal de edición
        $('#modalSugerencias').modal('hide');
        $('#modalEditarSugerencia').modal('show');
    };
    



    // Función para cargar las categorías de equinos con sus cantidades en el select del modal
    const loadCategoriaEquinos = async () => {
        try {
            const response = await fetch('../../controllers/alimento.controller.php?operation=getTipoEquinos', {
                method: "GET"
            });

            const parsedResponse = await response.json();
            console.log("Respuesta de la API para cargar categorías de equinos:", parsedResponse);

            if (parsedResponse.status === 'success' && Array.isArray(parsedResponse.data)) {
                const categorias = parsedResponse.data;
                const idEquinoSelect = document.getElementById('idEquino');

                if (!idEquinoSelect) {
                    console.error("El select con ID 'idEquino' no existe en el DOM.");
                    return;
                }

                // Limpiar opciones previas
                idEquinoSelect.innerHTML = '<option value="">Seleccione Categoría de Equino</option>';

                // Agregar opciones y verificar que `idEquino` sea válido
                categorias.forEach(categoria => {
                    console.log("Verificando categoría:", categoria); // Añadir este log

                    if (categoria.idEquino) { // Asegurarse de que el idEquino existe y no está undefined
                        const option = document.createElement('option');
                        option.value = categoria.idEquino; // Confirmar que `idEquino` se usa correctamente
                        option.textContent = `${categoria.Categoria} (${categoria.Cantidad})`;
                        idEquinoSelect.appendChild(option);
                    } else {
                        console.warn(`Categoría sin idEquino:`, categoria);
                    }
                });
            } else {
                console.warn('No se encontraron categorías de equinos.');
            }
        } catch (error) {
            console.error("Error al cargar categorías de equinos:", error);
        }
    };

    // Función para ajustar las fechas basadas en el filtro seleccionado
    const setFechaFiltro = () => {
        const filtroRango = document.getElementById('filtroRango').value;
        const hoy = new Date();
        let fechaInicio, fechaFin;

        switch (filtroRango) {
            case 'hoy':
                fechaInicio = fechaFin = hoy.toISOString().split('T')[0];
                break;
            case 'ultimaSemana':
                fechaInicio = new Date(hoy.setDate(hoy.getDate() - 7)).toISOString().split('T')[0];
                fechaFin = new Date().toISOString().split('T')[0];
                break;
            case 'ultimoMes':
                fechaInicio = new Date(hoy.setMonth(hoy.getMonth() - 1)).toISOString().split('T')[0];
                fechaFin = new Date().toISOString().split('T')[0];
                break;
            default:
                fechaInicio = '';
                fechaFin = '';
        }

        // Asigna las fechas como atributos del filtro para uso en AJAX
        document.getElementById('filtroRango').setAttribute('data-fecha-inicio', fechaInicio);
        document.getElementById('filtroRango').setAttribute('data-fecha-fin', fechaFin);
    };

    // Función para recargar las tablas de Entradas y Salidas
    const reloadHistorialMovimientos = () => {
        $('#tabla-entradas').DataTable().ajax.reload();
        $('#tabla-salidas').DataTable().ajax.reload();
    };

    // Evento para actualizar el filtro de fecha y recargar las tablas cuando el usuario selecciona un nuevo rango
    document.getElementById('filtroRango').addEventListener('change', () => {
        setFechaFiltro();
        reloadHistorialMovimientos();
    });

    // Llamar a las funciones de configuración cuando el DOM esté listo
    $(document).ready(() => {
        configurarDataTableEntradas();
        configurarDataTableSalidas();
    });



    

    // Cargar los tipos de medicamentos desde el servidor
    const loadTiposMedicamentos = async () => {
        try {
            console.log("Iniciando carga de tipos de medicamentos...");
            const response = await fetch(`../../controllers/admedi.controller.php?operation=listarTiposMedicamentos`, {
                method: "GET",
            });
            console.log("Respuesta recibida de tipos de medicamentos:", response);

            const result = await response.json();
            console.log("Resultado parseado de tipos de medicamentos:", result);

            // Limpiar el select y agregar opciones
            tipoMedicamentoSelect.innerHTML = '<option value="">Seleccione el Tipo de Medicamento</option>';

            result.data.forEach(tipo => {
                console.log("Procesando tipo:", tipo);
                const option = document.createElement("option");
                option.value = tipo.idTipo; // Usar idTipo para identificar cada tipo
                option.textContent = tipo.tipo;
                tipoMedicamentoSelect.appendChild(option);
            });

            // Agregar un evento para cargar presentaciones al cambiar el tipo
            tipoMedicamentoSelect.addEventListener('change', (event) => {
                const idTipo = event.target.value;
                console.log("Tipo seleccionado:", idTipo);
                if (idTipo) {
                    loadPresentaciones(idTipo); // Llamar a loadPresentaciones con el idTipo seleccionado
                } else {
                    presentacionSelect.innerHTML = '<option value="">Seleccione la Presentación</option>';
                }
            });

        } catch (error) {
            console.error("Error al cargar tipos de medicamentos:", error);
            mostrarMensaje("Error al cargar tipos de medicamentos: " + error.message, 'error');
        }
    };

    // Registrar combinaciones 
    document.querySelector("#formAgregarTipoPresentacion").addEventListener('submit', async (event) => {
        event.preventDefault();

        const nuevoTipo = document.querySelector("#nuevoTipoMedicamento").value;
        const nuevaPresentacion = document.querySelector("#nuevaPresentacion").value;
        const nuevaUnidad = document.querySelector("#nuevaUnidadMedida").value;
        const dosis = parseFloat(document.querySelector("#dosisMedicamento").value); // Siempre será 10

        // Verificar que todos los campos visibles tengan un valor
        if (!nuevoTipo || !nuevaPresentacion || !nuevaUnidad) {
            alert("Todos los campos son obligatorios.");
            return;
        }

        try {
            const response = await fetch('../../controllers/admedi.controller.php', {
                method: "POST",
                body: new URLSearchParams({
                    operation: 'agregarCombinacionMedicamento',
                    tipo: nuevoTipo,
                    presentacion: nuevaPresentacion,
                    unidad: nuevaUnidad,
                    dosis: dosis // Envía siempre 10
                })
            });

            const result = await response.json();
            if (result.status === "success") {
                mostrarMensaje(result.message, 'success');
                showToast(result.message, 'SUCCESS');
                $('#modalAgregarTipoPresentacion').modal('hide'); // Cierra el modal
            } else {
                mostrarMensaje(result.message, 'error');
                showToast(result.message, 'ERROR');
            }
        } catch (error) {
            mostrarMensaje("Error al agregar la combinación: " + error.message, 'error');
            showToast("Error al agregar la combinación", 'ERROR');
        }
    });

    // Cargar presentaciones de medicamentos desde el servidor según el tipo seleccionado
    const loadPresentaciones = async (idTipo) => {
        try {
            // Verificar si idTipo está definido
            if (!idTipo) {
                showToast("No se olvide ,Debe seleccionar un tipo de medicamento antes de cargar las presentaciones.", 'INFO');
                return;
            }

            const response = await fetch(`../../controllers/admedi.controller.php`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: new URLSearchParams({
                    operation: 'listarPresentacionesMedicamentos',
                    idTipo: idTipo
                })
            });

            const result = await response.json();

            if (result.status === "success") {
                const presentaciones = result.data;

                // Limpiar las opciones previas y agregar el mensaje inicial
                presentacionSelect.innerHTML = '<option value="">Seleccione la Presentación</option>';
                presentaciones.forEach(presentacion => {
                    const option = document.createElement("option");
                    option.value = presentacion.idPresentacion; // Usar idPresentacion como valor
                    option.textContent = presentacion.presentacion;
                    presentacionSelect.appendChild(option);
                });
            } else {
                mostrarMensaje("No se pudieron obtener las presentaciones.", 'error');
            }
        } catch (error) {
            mostrarMensaje("Error al cargar presentaciones: " + error.message, 'error');
        }
    };

    // Cargar medicamentos en los selectores de entrada y salida
    const loadSelectMedicamentos = async () => {
        try {
            // Definir los parámetros en la URL para el método GET
            const params = new URLSearchParams({
                operation: 'getAllMedicamentos'
            });
            const response = await fetch(`../../controllers/admedi.controller.php?${params.toString()}`, {
                method: "GET" // Cambiar el método a GET
            });

            const textResponse = await response.text();
            const result = JSON.parse(textResponse);
            const medicamentos = result.data;

            // Crear un Set para almacenar nombres únicos de medicamentos
            const medicamentosUnicos = new Set();

            // Limpiar los selectores
            document.querySelector("#entradaMedicamento").innerHTML = '<option value="">Seleccione un Medicamento</option>';
            document.querySelector("#salidaMedicamento").innerHTML = '<option value="">Seleccione un Medicamento</option>';

            // Recorrer los medicamentos y agregar solo los nombres únicos
            medicamentos.forEach(med => {
                if (!medicamentosUnicos.has(med.nombreMedicamento)) {
                    medicamentosUnicos.add(med.nombreMedicamento);

                    // Crear las opciones para los selectores
                    const optionEntrada = document.createElement("option");
                    optionEntrada.value = med.nombreMedicamento;
                    optionEntrada.textContent = med.nombreMedicamento;

                    const optionSalida = document.createElement("option");
                    optionSalida.value = med.nombreMedicamento;
                    optionSalida.textContent = med.nombreMedicamento;

                    // Añadir las opciones a los selectores
                    document.querySelector("#entradaMedicamento").appendChild(optionEntrada);
                    document.querySelector("#salidaMedicamento").appendChild(optionSalida);
                }
            });



        } catch (error) {
            mostrarMensaje("Error al cargar medicamentos: " + error.message, 'error');
            showToast("Error al cargar medicamentos", 'ERROR');
        }
    };

    // Cargar lista de medicamentos en la tabla
    const loadMedicamentos = async () => {
        try {
            const params = new URLSearchParams({
                operation: 'getAllMedicamentos'
            });
            const response = await fetch(`../../controllers/admedi.controller.php?${params.toString()}`, {
                method: "GET"
            });

            const textResponse = await response.text();
            if (textResponse.startsWith("<")) {
                mostrarMensaje("Error en la respuesta del servidor.", 'error');
                showToast("Error en la respuesta del servidor", 'ERROR');
                return;
            }

            const result = JSON.parse(textResponse);
            const medicamentos = result.data;

            if ($.fn.dataTable.isDataTable('#tabla-medicamentos')) {
                $('#tabla-medicamentos').DataTable().clear().rows.add(medicamentos).draw();
            } else {
                configurarDataTableMedicamentos(); // Inicializa DataTable si no está inicializado
            }

        } catch (error) {
            mostrarMensaje("Error al cargar medicamentos: " + error.message, 'error');
            showToast("Error al cargar medicamentos", 'ERROR');
        }
    };

    // **Función para manejar la notificación de stock bajo/agotado para medicamentos**
    const notificarStockBajo = async () => {
        try {
            // Realizar la solicitud GET al controlador de medicamentos
            const response = await fetch('../../controllers/admedi.controller.php?operation=notificarStockBajo', {
                method: "GET"
            });

            // Leer la respuesta y parsear a JSON
            const textResponse = await response.text();
            const result = JSON.parse(textResponse);

            // Verificar si hay datos y recorrer los resultados
            if (result.status === 'success' && result.data) {
                const {
                    agotados,
                    bajoStock
                } = result.data;

                // Mostrar notificaciones de medicamentos agotados
                agotados.forEach(notificacion => {
                    mostrarMensaje(
                        `
                        <span class="text-primary">Medicamento:</span> <strong>${notificacion.nombreMedicamento}  , 
                        <span class="text-success">Lote:</span> ${notificacion.loteMedicamento}  , 
                        <span class="text-info">Estado:</span> Agotado 
                        `,
                        'ERROR' // Puedes usar 'ERROR' para más énfasis
                    );
                });

                // Mostrar notificaciones de medicamentos con stock bajo
                bajoStock.forEach(notificacion => {
                    mostrarMensaje(
                        `
                        <span class="text-primary">Medicamento:</span> <strong>${notificacion.nombreMedicamento}   , 
                        <span class="text-success">Lote:</span> ${notificacion.loteMedicamento} , 
                        <span class="text-warning">Stock:</span> ${notificacion.stockActual}  ,
                        <span class="text-danger">(Mínimo: ${notificacion.stockMinimo})  , 
                        <span class="text-info">Estado:</span> Stock Bajo
                        `,
                        'WARNING'
                    );
                });
            } else if (result.status === 'info') {
                mostrarMensaje(result.message, 'INFO');
            }
        } catch (error) {
            mostrarMensaje('Error al notificar stock bajo.', 'ERROR');
        }
    };

    // Función para confirmar la eliminación del medicamento
    window.borrarMedicamento = async (idMedicamento, nombreMedicamento) => {
        // Confirmación antes de proceder con la eliminación
        const confirmacion = await ask(`¿Estás seguro de que deseas eliminar el medicamento "${nombreMedicamento}"?`);
        if (confirmacion) {
            try {
                // Enviar solicitud al backend
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: new URLSearchParams({
                        operation: 'deleteMedicamento',
                        idMedicamento: idMedicamento
                    })
                });

                const textResult = await response.text();

                let result;
                try {
                    result = JSON.parse(textResult);
                } catch (jsonParseError) {
                    showToast("Error al interpretar la respuesta del servidor. Respuesta no válida.", "ERROR");
                    return;
                }

                // Verificar el estado de la operación
                if (result.status === 'success') {
                    showToast("Medicamento eliminado correctamente.", "SUCCESS");

                    // Actualizar los selectores y listas después de la eliminación
                    await loadSelectMedicamentos();
                    await cargarLotes();
                    await loadMedicamentos();
                } else {
                    showToast(result.message || "Error al eliminar el medicamento.", "ERROR");
                }
            } catch (error) {
                showToast("Error al intentar eliminar el medicamento: " + error.message, "ERROR");
            }
        } else {
            showToast("Eliminación cancelada por el usuario.", "INFO");
        }
    };

    // **Nuevo - Validar combinaciones antes de registrar un medicamento**
    const validarCombinacion = async (params) => {
        try {
            const response = await fetch('../../controllers/admedi.controller.php', {
                method: "POST",
                body: new URLSearchParams({
                    operation: 'validarRegistrarCombinacion',
                    tipoMedicamento: params.tipo,
                    presentacionMedicamento: params.presentacion,
                    dosisMedicamento: params.dosis
                })
            });

            const result = await response.json();

            if (result.status === "success") {
                return true; // La combinación es válida
            } else {
                mostrarMensaje(result.message, 'error');
                return false; // La combinación es inválida
            }
        } catch (error) {
            mostrarMensaje("Error al validar combinación: " + error.message, 'error');
            return false;
        }
    };

    // **Validar campos y mostrar mensajes específicos**
    const validarCampos = (formData) => {
        const errors = [];

        if (!formData.get('nombreMedicamento')) {
            errors.push('El nombre del medicamento es obligatorio.');
        }

        if (!formData.get('lote')) {
            errors.push('El lote es obligatorio.');
        }

        if (!formData.get('tipo')) {
            errors.push('El tipo de medicamento es obligatorio.');
        }

        if (!formData.get('presentacion')) {
            errors.push('La presentación es obligatoria.');
        }

        if (!formData.get('fechaCaducidad')) {
            errors.push('La fecha de caducidad es obligatoria.');
        }

        if (!formData.get('dosis')) {
            errors.push('La dosis es obligatoria.');
        }

        if (!formData.get('cantidad_stock') || formData.get('cantidad_stock') <= 0) {
            errors.push('La cantidad de stock debe ser mayor a 0.');
        }

        return errors;
    };

    // Registrar medicamento
    formRegistrarMedicamento.addEventListener("submit", async (event) => {
        event.preventDefault();

        console.log("Iniciando registro de medicamento...");

        // Confirmación antes de registrar el medicamento
        if (await ask("¿Confirmar registro del nuevo medicamento?")) {
            console.log("Confirmación del usuario recibida. Enviando datos...");

            // Obtener el valor combinado de dosis y unidad
            const dosisCompleta = document.querySelector("#dosis").value;
            console.log("Dosis completa ingresada:", dosisCompleta);

            // Usar una expresión regular para separar la cantidad de la unidad
            const match = dosisCompleta.match(/^(\d+(\.\d+)?)(\s?[a-zA-Z]+)$/);
            if (!match) {
                mostrarMensaje("Formato de dosis inválido. Use un número seguido de una unidad (ej. 500 mg)", "error");
                console.warn("Formato de dosis inválido:", dosisCompleta);
                return;
            }

            const dosis = parseFloat(match[1]);
            const unidad = match[3].trim();
            console.log("Dosis separada:", dosis, "Unidad:", unidad);

            // Validar que ambos elementos estén presentes
            if (!dosis || !unidad) {
                mostrarMensaje("Debe ingresar una dosis válida con su unidad", "error");
                console.warn("Datos de dosis incompletos:", {
                    dosis,
                    unidad
                });
                return;
            }

            // Crear los datos para enviar al backend
            const formData = new FormData(formRegistrarMedicamento);
            formData.append('dosis', dosis);
            formData.append('unidad', unidad);
            formData.append('operation', 'registrar');

            console.log("Datos del formulario enviados:");
            formData.forEach((value, key) => {
                console.log(`${key}: ${value}`);
            });

            try {
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: formData
                });
                console.log("Respuesta del servidor:", response);

                const text = await response.text();
                console.log("Texto de respuesta del servidor:", text);

                let result;
                try {
                    result = JSON.parse(text);
                    console.log("Resultado parseado del servidor:", result);
                } catch (jsonError) {
                    console.error("Error al interpretar la respuesta del servidor:", jsonError);
                    mostrarMensaje("Error al interpretar la respuesta del servidor. Respuesta no válida.", 'error');
                    return;
                }

                if (result.status === "success") {
                    console.log("Medicamento registrado correctamente:", result);
                    showToast("Medicamento registrado correctamente", "SUCCESS");
                    formRegistrarMedicamento.reset();
                    // Llamar a las funciones para recargar los selectores de medicamentos y lotes
                    await loadSelectMedicamentos();
                    await cargarLotes();
                    await loadMedicamentos();
                } else {
                    console.warn("Error en el registro:", result.message);
                    mostrarMensaje("Error en el registro: " + result.message, 'error');
                }
            } catch (error) {
                console.error("Error al registrar el medicamento:", error);
                mostrarMensaje("Error al registrar el medicamento: " + error.message, 'error');
            }
        } else {
            console.log("El usuario canceló la operación.");
            mostrarMensaje("El registro del medicamento fue cancelado por el usuario.", "info");
        }
    });


    // Implementar para la entrada de medicamentos
    formEntrada.addEventListener("submit", async (event) => {
        event.preventDefault();

        // Confirmar la operación
        const confirmar = await ask("¿Estás seguro de que deseas registrar la entrada de este medicamento?", "Registrar Entrada de Medicamento");

        if (!confirmar) {
            showToast("Operación cancelada.", "INFO");
            return;
        }

        const formData = new FormData(formEntrada);
        const data = new URLSearchParams(formData);
        data.append('operation', 'entrada');

        try {
            const response = await fetch('../../controllers/admedi.controller.php', {
                method: "POST",
                body: data
            });
            const result = await response.json();

            if (result.status === "success") {
                showToast("Entrada registrada correctamente", "SUCCESS");
                formEntrada.reset();
                cargarLotes();
                loadMedicamentos();
            } else {
                showToast("Error en el registro de entrada: " + result.message, 'error');
            }
        } catch (error) {
            showToast("Error en la solicitud de registro de entrada: " + error.message, 'error');
        }
    });

    // Función para cargar los lotes en los select de entrada y salida de medicamentos
    const cargarLotes = async () => {
        const entradaLoteSelect = document.querySelector("#entradaLote");
        const salidaLoteSelect = document.getElementById('salidaLote');

        try {
            const response = await fetch('../../controllers/admedi.controller.php?operation=listarLotes', {
                method: 'GET',
            });

            const result = await response.json();

            if (result.status === "success") {
                entradaLoteSelect.innerHTML = '<option value="">Seleccione un lote</option>';
                salidaLoteSelect.innerHTML = '<option value="">Seleccione un lote</option>';

                result.data.forEach(lote => {
                    const optionEntrada = document.createElement("option");
                    optionEntrada.value = lote.lote;
                    optionEntrada.textContent = `${lote.lote} - ${lote.nombreMedicamento}`;
                    entradaLoteSelect.appendChild(optionEntrada);

                    const optionSalida = document.createElement("option");
                    optionSalida.value = lote.lote;
                    optionSalida.textContent = `${lote.lote} - ${lote.nombreMedicamento}`;
                    salidaLoteSelect.appendChild(optionSalida);
                });
            } else {
                mostrarMensaje("No se encontraron lotes registrados.", 'error');
            }
        } catch (error) {
            mostrarMensaje("Error al cargar los lotes: " + error.message, 'error');
        }
    };

    // Implementar para la salida de medicamentos
    if (formSalida) {
        formSalida.addEventListener("submit", async (event) => {
            event.preventDefault();

            const cantidadField = document.getElementById('salidaCantidad');
            const cantidad = parseFloat(cantidadField.value) || 0;

            // Validación de cantidad
            if (cantidad <= 0) {
                showToast("La cantidad debe ser mayor a 0.", 'ERROR');
                return;
            }

            const motivoField = document.getElementById('motivoSalida');
            const loteField = document.getElementById('salidaLote');
            const medicamentoField = document.getElementById('salidaMedicamento');
            const tipoEquinoField = document.getElementById('idEquino');


            // Validación de motivo
            const motivo = motivoField.value.trim();
            if (!motivo) {
                showToast("Debe especificar un motivo para la salida del medicamento.", 'ERROR');
                return;
            }

            // Confirmación de la operación
            const confirmar = await ask("¿Estás seguro de que deseas registrar la salida de este medicamento?", "Registrar Salida de Medicamento");
            if (!confirmar) {
                showToast("Operación cancelada.", "INFO");
                return;
            }

            // Preparar datos para enviar
            const formData = new FormData();
            formData.append('operation', 'salida');
            formData.append('nombreMedicamento', medicamentoField.value);
            formData.append('cantidad', cantidad);
            formData.append('idEquino', tipoEquinoField.value);
            formData.append('motivo', motivo);

            const lote = loteField.value.trim() || null;
            if (lote !== null) {
                formData.append('lote', lote);
            }

            // Intento de envío de datos al servidor
            try {
                const response = await fetch('../../controllers/admedi.controller.php', {
                    method: "POST",
                    body: formData
                });

                const result = await response.json();

                if (result.status === "success") {
                    showToast("Salida registrada correctamente", "SUCCESS");
                    formSalida.reset();
                    await loadMedicamentos();
                    await notificarStockBajo();
                    await loadSelectMedicamentos();
                    await cargarLotes();
                } else {
                    showToast(result.message || "Error en el registro de salida", "ERROR");
                }
            } catch (error) {
                showToast("Error en la solicitud de registro de salida", "ERROR");
            }
        });
    }

    // Cargar datos al iniciar la página
    cargarLotes();
    loadCategoriaEquinos();
    notificarStockBajo();

    loadSelectMedicamentos();
    loadMedicamentos();
    loadTiposMedicamentos();
    loadPresentaciones();
});