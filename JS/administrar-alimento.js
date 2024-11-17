document.addEventListener("DOMContentLoaded", () => {
    // Elementos del formulario
    const formRegistrarAlimento = document.querySelector("#form-registrar-alimento");
    const formEntradaAlimento = document.querySelector("#form-entrada-alimento");
    const formSalidaAlimento = document.querySelector("#form-salida-alimento");
    const alimentosTable = document.querySelector("#alimentos-table");
    const alimentoSelectEntrada = document.querySelector("#alimento-select-entrada");
    const alimentoSelectSalida = document.querySelector("#alimento-select-salida");
    const mensajeDiv = document.querySelector("#mensaje");  // Div para mostrar los mensajes dinámicos

    // Elementos del tipo de alimento y unidad de medida para ambos modales
    const tipoAlimentoElementRegistrar = document.getElementById('tipoAlimento');
    const unidadMedidaElementRegistrar = document.getElementById('unidadMedida');

    const unidadMedidaSelectEntrada = document.getElementById('unidadMedidaEntrada');
    const unidadMedidaSelectSalida = document.querySelector("#unidadMedidaSalida");

    // Elementos de fecha de caducidad e ingreso
    const fechaCaducidadElement = document.getElementById('fechaCaducidad');

    const loteInput = document.querySelector('#lote');
    const nombreAlimentoInput = document.querySelector('#nombreAlimento');

    // Coloca la función `guardarTipoUnidad` aquí, fuera del bloque `DOMContentLoaded`
    // Verificar campos
    const verificarCampos = () => {
        const categoriaAlimento = document.getElementById("inputCategoriaAlimento")?.value?.trim();
        const unidadMedida = document.getElementById("inputUnidadMedida")?.value?.trim();
        const mensajeModal = document.getElementById("mensajeModal");

        mensajeModal.innerHTML = ""; // Limpiar mensajes previos

        if (!categoriaAlimento) {
            console.log("Campo 'Categoría de Alimento' vacío");
            mensajeModal.innerHTML = '<p class="text-danger">Por favor, complete el campo "Categoría de Alimento".</p>';
            return false;
        }
        if (!unidadMedida) {
            console.log("Campo 'Unidad de Medida' vacío");
            mensajeModal.innerHTML = '<p class="text-danger">Por favor, complete el campo "Unidad de Medida".</p>';
            return false;
        }
        return { categoriaAlimento, unidadMedida };
    };

    // Función de utilidad para realizar una solicitud GET y obtener datos
    const fetchData = async (url) => {
        try {
            const response = await fetch(url);
            const result = await response.json();
            console.log("Respuesta de la API para URL:", url, "->", result);
            // Asegúrate de que `result` tiene el formato esperado antes de devolver `result.data`
            return result.status === "success" && Array.isArray(result.data) ? result.data : null;
        } catch (error) {
            console.error("Error en la solicitud:", error.message);
            return null;
        }
    };

    // Función para llenar opciones en un select
    const fillSelect = (selectElement, options, placeholder) => {
        selectElement.innerHTML = `<option value="" disabled selected>${placeholder}</option>`;
        // Verificar si `options` es un array antes de usar `forEach`
        if (Array.isArray(options) && options.length > 0) {
            options.forEach(({ id, nombre }) => {
                selectElement.innerHTML += `<option value="${id}">${nombre}</option>`;
            });
        } else {
            console.warn("fillSelect: No se encontraron opciones para llenar el select.");
        }
    };

    // Cargar tipos de alimento en el formulario de registro
    const cargarTiposAlimento = async () => {
        const tipos = await fetchData(`../../controllers/alimento.controller.php?operation=obtenerTiposAlimento`);
        // Verificar si se encontraron tipos de alimento antes de pasarlos a `fillSelect`
        if (tipos) {
            fillSelect(tipoAlimentoElementRegistrar, tipos.map(tipo => ({ id: tipo.idTipoAlimento, nombre: tipo.tipoAlimento })), "Seleccione el tipo de alimento");
        } else {
            console.warn("cargarTiposAlimento: No se encontraron tipos de alimento.");
            fillSelect(tipoAlimentoElementRegistrar, [], "Seleccione el tipo de alimento");
        }
    };

    // Función para cargar las unidades de medida según tipo o nombre de alimento
    const cargarUnidadesMedida = async (param, selectElement, byType = true) => {
        const operation = byType ? `obtenerUnidadesPorTipoAlimento&idTipoAlimento=${param}` : `getUnidadesMedida&nombreAlimento=${param}`;

        console.log(`Ejecutando cargarUnidadesMedida con parámetro: ${param}, operación: ${operation}`); // Log para verificar la llamada

        const unidades = await fetchData(`../../controllers/alimento.controller.php?operation=${operation}`);

        console.log(`Unidades obtenidas para ${param} ->`, unidades); // Log para verificar los datos obtenidos

        if (unidades && unidades.length > 0) {
            // Crear un Set para almacenar solo unidades de medida únicas
            const uniqueUnitsSet = new Set();
            const uniqueUnits = [];

            // Filtrar duplicados usando el Set
            unidades.forEach(u => {
                const unitName = u.nombreUnidad;
                if (!uniqueUnitsSet.has(unitName)) {
                    uniqueUnitsSet.add(unitName);
                    uniqueUnits.push({ id: u.idUnidadMedida, nombre: unitName });
                }
            });

            // Llenar el select con las unidades únicas
            fillSelect(selectElement, uniqueUnits, "Seleccione la Unidad de Medida");
        } else {
            console.warn("cargarUnidadesMedida: No se encontraron unidades de medida.");
            fillSelect(selectElement, [], "Seleccione la Unidad de Medida");
        }
    };

    // Eventos de cambio para los selects de alimento y tipo de alimento
    document.getElementById("alimento-select-entrada")?.addEventListener("change", e => {
        console.log("Evento de cambio en alimento-select-entrada activado con valor:", e.target.value);
        cargarUnidadesMedida(e.target.value, document.getElementById("unidadMedidaEntrada"), false);
    });

    document.getElementById("alimento-select-salida")?.addEventListener("change", e => {
        console.log("Evento de cambio en alimento-select-salida activado con valor:", e.target.value);
        cargarUnidadesMedida(e.target.value, document.getElementById("unidadMedidaSalida"), false);
    });

    document.getElementById("tipoAlimento")?.addEventListener("change", e => {
        console.log("Evento de cambio en tipoAlimento activado con valor:", e.target.value);
        cargarUnidadesMedida(e.target.value, document.getElementById("unidadMedida"));
    });


    // Inicialización al cargar la página
    document.addEventListener("DOMContentLoaded", cargarTiposAlimento);


    // Guardar nueva categoría y unidad de medida
    const guardarCategoriaMedida = async () => {
        const valores = verificarCampos();
        if (!valores) return;

        const { categoriaAlimento, unidadMedida } = valores;

        try {
            console.log("Enviando datos...");
            const response = await fetch(`../../controllers/alimento.controller.php`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    operation: "agregarTipoUnidadMedidaNuevo",
                    tipoAlimento: categoriaAlimento,
                    nombreUnidad: unidadMedida
                })
            });

            const result = await response.json();

            const mensajeModal = document.getElementById("mensajeModal");
            mensajeModal.innerHTML = result.status === "success"
                ? '<p class="text-success">¡Agregado correctamente!</p>'
                : `<p class="text-danger">${result.message}</p>`;

            if (result.status === "success") {
                setTimeout(() => {
                    document.getElementById("formNuevaCategoriaMedida").reset();
                    mensajeModal.innerHTML = "";
                    bootstrap.Modal.getInstance(document.getElementById("modalAgregarCategoriaMedida")).hide();
                }, 1500);

                // Llamar a `cargarTiposAlimento` para actualizar la lista de tipos
                await cargarTiposAlimento();
            }
        } catch (error) {
            mensajeModal.innerHTML = '<p class="text-danger">Error al enviar los datos.</p>';
        }
    };

    // Asignar evento al botón de guardar
    const btnGuardar = document.getElementById("btnGuardarCategoriaMedida");
    if (btnGuardar) {
        console.log("Botón 'Guardar' encontrado");
        btnGuardar.addEventListener("click", guardarCategoriaMedida);
    } else {
        console.error("El botón #btnGuardarCategoriaMedida no se encontró.");
    }

    // **Función para mostrar notificaciones dinámicas para alimentos**
    const mostrarMensajeDinamico = (mensaje, tipo = 'INFO') => {
        const mensajeDiv = document.getElementById('mensaje'); // Asegúrate de tener un div con el id 'mensaje'

        if (mensajeDiv) {
            // Definición de estilos para cada tipo de mensaje
            const estilos = {
                'INFO': { color: '#3178c6', bgColor: '#e7f3ff', icon: 'ℹ️' },
                'SUCCESS': { color: '#3c763d', bgColor: '#dff0d8', icon: '✅' },
                'ERROR': { color: '#a94442', bgColor: '#f2dede', icon: '❌' },
                'WARNING': { color: '#8a6d3b', bgColor: '#fcf8e3', icon: '⚠️' }
            };

            // Obtener los estilos correspondientes al tipo de mensaje
            const estilo = estilos[tipo] || estilos['INFO'];

            // Aplicar estilos al contenedor del mensaje
            mensajeDiv.style.display = 'flex';
            mensajeDiv.style.alignItems = 'center';
            mensajeDiv.style.color = estilo.color;
            mensajeDiv.style.backgroundColor = estilo.bgColor;
            mensajeDiv.style.fontWeight = 'bold';
            mensajeDiv.style.padding = '15px';
            mensajeDiv.style.marginBottom = '15px';
            mensajeDiv.style.border = `1px solid ${estilo.color}`;
            mensajeDiv.style.borderRadius = '8px';
            mensajeDiv.style.boxShadow = '0 4px 8px rgba(0, 0, 0, 0.1)';

            // Mostrar el mensaje con un icono
            mensajeDiv.innerHTML = `<span style="margin-right: 10px; font-size: 1.2em;">${estilo.icon}</span>${mensaje}`;

            // Ocultar el mensaje después de 5 segundos
            setTimeout(() => {
                mensajeDiv.style.display = 'none';
                mensajeDiv.innerHTML = ''; // Limpiar contenido
                mensajeDiv.style.border = 'none';
                mensajeDiv.style.boxShadow = 'none';
                mensajeDiv.style.backgroundColor = 'transparent';
            }, 5000);
        } else {
            console.warn('El contenedor de mensajes para alimentos no está presente en el DOM.');
        }
    };


    // **Función para mostrar notificaciones usando showToast**
    const mostrarNotificacion = (mensaje, tipo = 'INFO') => {
        showToast(mensaje, tipo);
    };

    // Validar cantidad positiva en movimientos de entrada y salida
    const cantidadEntrada = document.querySelector("#cantidad-entrada");
    const cantidadSalida = document.querySelector("#cantidad-salida");

    // **Fecha de Caducidad**: No permitir fechas pasadas, establecer mínimo como hoy
    if (fechaCaducidadElement) {
        const hoy = new Date().toISOString().split('T')[0]; // Obtener solo la fecha en formato YYYY-MM-DD
        fechaCaducidadElement.setAttribute('min', hoy); // Establecer la fecha mínima como hoy
    }

    // Función para validar la fecha de caducidad
    const validarFechaCaducidad = () => {
        const fechaCaducidadStr = fechaCaducidadElement.value; // Obtener la fecha en formato YYYY-MM-DD
        const hoyStr = new Date().toISOString().split('T')[0]; // Obtener la fecha de hoy en formato YYYY-MM-DD

        // Comparar las fechas en formato YYYY-MM-DD
        if (fechaCaducidadStr < hoyStr) {
            mostrarMensajeDinamico("La fecha de caducidad no puede ser en el pasado.", 'ERROR');
            console.log('Error: La fecha de caducidad es menor que hoy.');
            return false;
        }
        return true;
    };

    // Función para cargar los alimentos registrados y mostrarlos en la tabla y en los select de entrada/salida
    const loadAlimentos = async () => {
        try {
            const params = new URLSearchParams({ operation: 'getAllAlimentos' });
            const response = await fetch(`../../controllers/alimento.controller.php?${params.toString()}`, {
                method: "GET"
            });

            const textResponse = await response.text();
            if (textResponse.startsWith("<")) {
                mostrarMensajeDinamico("Error en la respuesta del servidor.", 'ERROR');
                showToast("Error en la respuesta del servidor", 'ERROR'); // Si `showToast` también es una función válida
                return;
            }

            const result = JSON.parse(textResponse);
            const alimentos = result.data;

            // Cargar datos en la tabla de alimentos
            if ($.fn.dataTable.isDataTable('#alimentos-table')) {
                $('#alimentos-table').DataTable().clear().rows.add(alimentos).draw();
            } else {
                configurarDataTableAlimentos(); // Inicializa DataTable si no está inicializado
            }

            // Llamar a función para cargar los select de entrada y salida con los alimentos
            cargarAlimentosEnSelects(alimentos);

        } catch (error) {
            mostrarMensajeDinamico("Error al cargar alimentos: " + error.message, 'ERROR');
            showToast("Error al cargar alimentos", 'ERROR'); // Si `showToast` también es una función válida
        }
    };

    // Función para cargar los alimentos en los select de entrada y salida
    const cargarAlimentosEnSelects = (alimentos) => {
        const alimentoSelectEntrada = document.getElementById('alimento-select-entrada');
        const alimentoSelectSalida = document.getElementById('alimento-select-salida');

        // Limpiar las opciones actuales en los select
        alimentoSelectEntrada.innerHTML = '<option value="">Seleccione un Alimento</option>';
        alimentoSelectSalida.innerHTML = '<option value="">Seleccione un Alimento</option>';

        // Añadir cada alimento a ambos select
        alimentos.forEach(alimento => {
            const optionEntrada = document.createElement('option');
            optionEntrada.value = alimento.nombreAlimento;  // Cambiar a `nombreAlimento`
            optionEntrada.textContent = alimento.nombreAlimento;

            const optionSalida = document.createElement('option');
            optionSalida.value = alimento.nombreAlimento;  // Cambiar a `nombreAlimento`
            optionSalida.textContent = alimento.nombreAlimento;

            // Agregar las opciones a los select
            alimentoSelectEntrada.appendChild(optionEntrada);
            alimentoSelectSalida.appendChild(optionSalida);
        });
    };

    // Función para cargar las categorías de equinos con sus cantidades en el select del modal
    const loadCategoriaEquinos = async () => {
        try {
            const response = await fetch('../../controllers/alimento.controller.php?operation=getTipoEquinos', {
                method: "GET"
            });

            const parsedResponse = await response.json();

            if (parsedResponse.status === 'success' && Array.isArray(parsedResponse.data)) {
                const categorias = parsedResponse.data;
                const idEquinoSelect = document.getElementById('idEquino');

                if (!idEquinoSelect) {
                    return;
                }

                // Limpiar opciones previas
                idEquinoSelect.innerHTML = '<option value="">Seleccione Categoría de Equino</option>';

                // Agregar opciones y verificar que `idEquino` sea válido
                categorias.forEach(categoria => {

                    if (categoria.idEquino) {  // Asegurarse de que el idEquino existe y no está undefined
                        const option = document.createElement('option');
                        option.value = categoria.idEquino;
                        option.textContent = `${categoria.Categoria} (${categoria.Cantidad})`;
                        idEquinoSelect.appendChild(option);
                    } else {
                    }
                });
            } else {
                console.warn('No se encontraron categorías de equinos.');
            }
        } catch (error) {
            console.error("Error al cargar categorías de equinos:", error);
        }
    };

    // Función para validar si el lote ya existe para el mismo alimento y unidad de medida
    async function validarLote() {
        // Verificar si los elementos del formulario existen en el DOM
        if (!loteInput || !nombreAlimentoInput || !unidadMedidaElementRegistrar) {
            console.error('Uno o más elementos de entrada no están definidos.');
            mostrarMensajeDinamico('Error: Uno o más campos están vacíos o no existen.', 'ERROR');
            return false;
        }

        // Obtener los valores de lote, nombre del alimento y unidad de medida
        const lote = loteInput.value.trim();
        const nombreAlimento = nombreAlimentoInput.value.trim();
        const unidadMedida = unidadMedidaElementRegistrar.value.trim();
        console.log("Lote a validar:", lote);
        console.log("Nombre del alimento:", nombreAlimento);
        console.log("Unidad de medida:", unidadMedida);


        // Verificar si los campos están vacíos
        if (!lote || !nombreAlimento || !unidadMedida) {
            mostrarMensajeDinamico('El lote, nombre del alimento y la unidad de medida no pueden estar vacíos.', 'ERROR');
            return false;
        }

        try {
            // Petición al servidor para verificar si el lote ya está registrado
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: 'POST',
                body: new URLSearchParams({
                    operation: 'verificarLote',  // Operación para verificar lote, alimento y unidad
                    lote: lote,
                    nombreAlimento: nombreAlimento,
                    unidadMedida: unidadMedida
                })
            });

            const result = await response.json();

            // Si el lote ya existe, mostrar mensaje de error
            if (result.status === 'error') {
                mostrarMensajeDinamico(result.message, 'ERROR');
                return false;
            }

            // Si la combinación es válida, retornar true
            return true;

        } catch (error) {
            mostrarMensajeDinamico('Error al verificar el lote: ' + error.message, 'ERROR');
            return false;
        }
    }

    // Función para registrar un nuevo alimento
    if (formRegistrarAlimento) {
        formRegistrarAlimento.addEventListener("submit", async (event) => {
            event.preventDefault();

            console.log("Formulario enviado. Iniciando validaciones...");

            // Validar fecha de caducidad
            if (!validarFechaCaducidad()) {
                mostrarMensajeDinamico('Error en las fechas de caducidad.', 'ERROR');
                console.log("Error en la validación de la fecha de caducidad.");
                return;
            }

            // Validar si el lote es único para ese alimento y unidad
            console.log("Validando el lote...");
            const loteValido = await validarLote();
            if (!loteValido) {
                mostrarMensajeDinamico('Lote inválido o ya registrado. Verifica los datos.', 'ERROR');
                console.log("Error en la validación del lote.");
                return;
            }
            console.log("Lote válido.");

            // Crear un FormData a partir del formulario
            const formData = new FormData(formRegistrarAlimento);
            const stockActual = parseFloat(formData.get('stockActual'));
            const stockMinimo = parseFloat(formData.get('stockMinimo'));

            // Validar que el stock mínimo no supere al stock actual
            if (stockMinimo > stockActual) {
                mostrarMensajeDinamico("El stock mínimo no puede ser mayor que el stock actual.", 'ERROR');
                console.log("Error: Stock mínimo mayor que el stock actual.");
                return;
            }
            console.log("Stock válido.");

            // Confirmación antes de registrar el alimento
            if (await ask("¿Confirmar registro de nuevo alimento?")) {
                console.log("Confirmación del usuario recibida. Enviando datos...");
                const data = new URLSearchParams(formData);
                data.append('operation', 'registrar');

                try {
                    console.log("Enviando solicitud al servidor...");
                    const response = await fetch('../../controllers/alimento.controller.php', {
                        method: "POST",
                        body: data
                    });

                    const textResult = await response.text();
                    console.log("Respuesta en texto recibida:", textResult);

                    try {
                        const jsonResult = JSON.parse(textResult);
                        console.log("Respuesta en JSON recibida:", jsonResult);

                        // Verificar si el registro fue exitoso
                        if (jsonResult.status === "success") {
                            mostrarMensajeDinamico(jsonResult.message, 'SUCCESS');
                            showToast(jsonResult.message, 'SUCCESS');
                            formRegistrarAlimento.reset();
                            loadAlimentos();
                            console.log("Alimento registrado exitosamente.");
                            await cargarLotes();
                            console.log("Lotes actualizados en los selectores.");
                        } else {
                            mostrarMensajeDinamico(jsonResult.message || "Error en la operación.", 'ERROR');
                            console.log("Error en la respuesta del servidor:", jsonResult.message || "Error en la operación.");
                        }
                    } catch (jsonParseError) {
                        mostrarMensajeDinamico("Error inesperado en la respuesta del servidor. Ver consola.", 'ERROR');
                        console.log("Error al parsear el JSON. Respuesta cruda:", textResult);
                    }
                } catch (error) {
                    mostrarMensajeDinamico("Error en la solicitud: " + error.message, 'ERROR');
                    console.log("Error en la solicitud:", error);
                }
            } else {
                mostrarMensajeDinamico('El usuario canceló la operación.', 'INFO');
                console.log("El usuario canceló la operación.");
            }
        });
    }

    // Función para ajustar las fechas basadas en el filtro seleccionado
    const modalElement = document.getElementById('modalHistorialAlimentos');
    const modalAlimentos = new bootstrap.Modal(modalElement, {
        keyboard: false
    });


    const setFechaFiltroAlimentos = () => {
        const filtroRango = document.getElementById('filtroRangoAlimentos').value;
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
            case 'todos':
                fechaInicio = '1900-01-01';
                fechaFin = new Date().toISOString().split('T')[0];
                break;
            default:
                fechaInicio = '';
                fechaFin = '';
        }

        document.getElementById('filtroRangoAlimentos').setAttribute('data-fecha-inicio', fechaInicio);
        document.getElementById('filtroRangoAlimentos').setAttribute('data-fecha-fin', fechaFin);
    };

    // Función para recargar las tablas de Entradas y Salidas
    const reloadHistorialAlimentos = () => {
        if ($.fn.DataTable.isDataTable('#tabla-entradas-alimentos')) {
            $('#tabla-entradas-alimentos').DataTable().ajax.reload(null, false);
        }
        if ($.fn.DataTable.isDataTable('#tabla-salidas-alimentos')) {
            $('#tabla-salidas-alimentos').DataTable().ajax.reload(null, false);
        }
    };

    // Evento para actualizar el filtro de fecha y recargar las tablas cuando el usuario selecciona un nuevo rango
    document.getElementById('filtroRangoAlimentos').addEventListener('change', () => {
        setFechaFiltroAlimentos();
        reloadHistorialAlimentos();
    });

    // **Función para manejar la notificación de stock bajo/agotado**
    const notificarStockBajo = async () => {
        try {
            // Realizar la solicitud al backend
            const response = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                body: new URLSearchParams({ operation: 'notificarStockBajo' })
            });

            const textResponse = await response.text();
            const result = JSON.parse(textResponse);

            // Verifica si 'data' es un array y contiene las notificaciones
            if (Array.isArray(result.data) && result.data.length > 0) {
                result.data.forEach(notificacion => {
                    // Crear el mensaje dinámico con espacios adicionales para claridad
                    const mensajeDinamico = `
                        <span class="text-primary">Alimento:</span> <strong>${notificacion.nombreAlimento}   , 
                        <span class="text-success">Lote:</span> ${notificacion.loteAlimento}  , 
                        <span class="text-warning">Stock:</span> ${notificacion.stockActual}  ,
                        <span class="text-danger">(Mínimo: ${notificacion.stockMinimo})   , 
                        <span class="text-info">Estado:</span> ${notificacion.mensaje} 
                    `.replace(/\s+/g, ' ').trim(); // Elimina espacios extra

                    mostrarMensajeDinamico(mensajeDinamico, 'WARNING');
                });
            } else {
                mostrarMensajeDinamico('No hay productos con stock bajo o agotados.', 'INFO');
            }
        } catch (error) {
            mostrarMensajeDinamico('Error al notificar stock bajo.', 'ERROR');
        }
    };

    // Función para cargar los lotes en los select de entrada y salida de alimentos
    const cargarLotes = async () => {
        const entradaLoteSelect = document.querySelector("#entradaLote");
        const salidaLoteSelect = document.getElementById('salidaLote');

        try {
            const response = await fetch('../../controllers/alimento.controller.php?operation=listarLotes', {
                method: 'GET',
            });

            const result = await response.json();

            if (result.status === "success") {
                entradaLoteSelect.innerHTML = '<option value="">Seleccione un lote</option>';
                salidaLoteSelect.innerHTML = '<option value="">Seleccione un lote</option>';

                result.data.forEach(lote => {
                    const optionEntrada = document.createElement("option");
                    optionEntrada.value = lote.lote;
                    optionEntrada.textContent = `${lote.lote} - ${lote.nombreAlimento}`;
                    entradaLoteSelect.appendChild(optionEntrada);

                    const optionSalida = document.createElement("option");
                    optionSalida.value = lote.lote;
                    optionSalida.textContent = `${lote.lote} - ${lote.nombreAlimento}`;
                    salidaLoteSelect.appendChild(optionSalida);
                });
            } else {
                mostrarMensajeDinamico("No se encontraron lotes registrados.", 'error');
            }
        } catch (error) {
            mostrarMensajeDinamico("Error al cargar los lotes: " + error.message, 'error');
        }
    };

    // Función para manejar entradas de alimentos
    const registrarEntrada = async () => {
        const cantidadField = document.getElementById('stockActual-entrada');
        const loteField = document.getElementById('entradaLote');
        const alimentoSelectEntrada = document.getElementById('alimento-select-entrada');
        const unidadMedidaEntrada = document.getElementById('unidadMedidaEntrada');
        const formEntradaAlimento = document.querySelector("#form-entrada-alimento");

        if (!cantidadField || !loteField || !alimentoSelectEntrada || !unidadMedidaEntrada) {
            console.error("Error: Uno o más elementos del formulario no se encontraron en el DOM.");
            showToast("Error en el formulario: faltan elementos.", 'ERROR');
            return;
        }

        const cantidad = parseFloat(cantidadField.value) || 0;
        const lote = loteField.value ? loteField.value : null;


        if (await ask("¿Confirmar entrada de alimento?")) {
            console.log("Usuario confirmó la entrada de alimento.");

            const params = {
                operation: 'entrada',
                nombreAlimento: alimentoSelectEntrada.value,
                unidadMedida: unidadMedidaEntrada.value,
                lote: lote,
                cantidad: cantidad
            };

            console.log("Parámetros enviados:", params);

            const data = JSON.stringify(params);

            try {
                const response = await fetch('../../controllers/alimento.controller.php', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: data
                });

                const textResponse = await response.text();
                console.log("Respuesta completa del servidor:", textResponse);

                const result = JSON.parse(textResponse);
                console.log("Respuesta procesada (JSON):", result);

                if (result.status === "success") {
                    showToast(result.message || "Entrada registrada exitosamente.", 'SUCCESS');
                    formEntradaAlimento.reset();
                    $('#modalEntradaAlimento').modal('hide');

                    await loadAlimentos();
                    await setFechaFiltroAlimentos();
                    console.log("Stock actualizado en la interfaz.");
                } else {
                    showToast(result.message || "Error al registrar la entrada.", 'ERROR');
                }
            } catch (error) {
                console.error("Error en la solicitud:", error.message);
                showToast("Error en la solicitud: " + error.message, 'ERROR');
            }
        } else {
            console.log("El usuario canceló la operación.");
        }
    };

    // Añadir el evento al botón de entrada
    document.getElementById("guardarEntrada").addEventListener("click", registrarEntrada);

    // Función para registrar la salida de alimento 
    const registrarSalida = async () => {
        try {
            const nombreAlimento = document.getElementById('alimento-select-salida')?.value || '';
            const cantidad = parseFloat(document.getElementById('cantidad-salida')?.value || 0);
            const uso = parseFloat(document.getElementById('uso')?.value || 0);
            const merma = parseFloat(document.getElementById('merma')?.value || 0);
            const idEquino = document.getElementById('idEquino')?.value || '';
            const unidadMedida = document.getElementById('unidadMedidaSalida')?.value || '';
            const lote = document.getElementById('salidaLote')?.value || '';

            // Validar que la cantidad de uso y merma sumen la cantidad total de salida
            if (cantidad !== (uso + merma)) {
                console.warn("La cantidad total de salida debe ser igual a la suma de uso y merma.");
                showToast("La cantidad total debe ser igual a la suma de uso y merma.", 'WARNING');
                return;
            }

            if (!nombreAlimento || !cantidad || !unidadMedida || !lote || idEquino === '' || unidadMedida === '') {
                console.warn("Faltan datos necesarios para registrar la salida.");
                showToast("Por favor, complete todos los campos requeridos.", 'WARNING');
                return;
            }

            // Parámetros con el campo de uso incluido
            const params = {
                operation: 'salida',
                nombreAlimento,
                cantidad,
                uso,
                merma,
                idEquino,
                unidadMedida,
                lote
            };

            console.log("Parámetros que se enviarán al servidor:", params);

            const response = await fetch('../../controllers/alimento.controller.php', {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(params)
            });

            console.log("Estado de la respuesta:", response.status, response.statusText);

            const result = await response.json();

            console.log("Respuesta de la API:", result);

            if (result.status === "success") {
                showToast(result.message || "Salida registrada exitosamente.", 'SUCCESS');
                document.getElementById("form-salida-alimento").reset();
                $('#modalSalidaAlimento').modal('hide');

                await loadAlimentos();
                await setFechaFiltroAlimentos();
                await notificarStockBajo();
                console.log("Stock y movimientos actualizados en la interfaz.");
            } else {
                showToast(result.message || "Error al registrar la salida.", 'ERROR');
            }
        } catch (error) {
            console.error("Error en registrarSalida:", error);
            showToast("Error en la solicitud: " + error.message, 'ERROR');
        }
    };

    // Función para actualizar automáticamente el valor de merma
    const actualizarMermaAutomatica = () => {
        const cantidadTotal = parseFloat(document.getElementById('cantidad-salida')?.value || 0);
        const uso = parseFloat(document.getElementById('uso')?.value || 0);
        const mermaField = document.getElementById('merma');

        // Calcular merma automáticamente y ajustar valores si se exceden los límites
        if (cantidadTotal >= uso) {
            const mermaCalculada = (cantidadTotal - uso).toFixed(2);

            // Si la merma calculada es mayor que la cantidad total, ajustarla
            if (parseFloat(mermaCalculada) > cantidadTotal) {
                mermaField.value = cantidadTotal;
            } else if (parseFloat(mermaCalculada) < 0) {
                // Si la merma es negativa, establecerla en cero
                mermaField.value = 0;
            } else {
                mermaField.value = mermaCalculada;
            }
        } else {
            mermaField.value = 0;
            console.warn("El valor de uso no puede ser mayor que la cantidad total.");
        }
    };

    // Añadir el evento al campo de uso para calcular automáticamente la merma
    document.getElementById("uso").addEventListener("input", actualizarMermaAutomatica);

    // Añadir el evento al campo de cantidad total para recalcular automáticamente la merma cuando cambia la cantidad
    document.getElementById("cantidad-salida").addEventListener("input", actualizarMermaAutomatica);

    // Añadir el evento al botón de salida
    document.getElementById("guardarSalida").addEventListener("click", registrarSalida);

    // Función para eliminar un alimento
    window.eliminarAlimento = async (idAlimento) => {
        if (await ask('¿Estás seguro de que deseas eliminar este alimento?')) {
            const data = new URLSearchParams();
            data.append('operation', 'eliminar');
            data.append('idAlimento', idAlimento);

            try {
                // Realizar la solicitud al backend
                const response = await fetch('../../controllers/alimento.controller.php', {
                    method: "POST",
                    body: data
                });

                const result = JSON.parse(await response.text());

                // Validar y mostrar el resultado de la operación
                if (result.status === "success" && result.data && result.data.status === "success") {
                    mostrarMensajeDinamico(result.data.message, 'SUCCESS');
                    loadAlimentos(); // Recargar la lista de alimentos
                } else {
                    mostrarMensajeDinamico(result.data?.message || result.message || "Error en la operación.", 'ERROR');
                }
            } catch (error) {
                mostrarMensajeDinamico("Error en la solicitud: " + error.message, 'ERROR');
            }
        }
    };

    cargarLotes();
    cargarTiposAlimento();
    loadAlimentos();
    loadCategoriaEquinos();
    notificarStockBajo();
});