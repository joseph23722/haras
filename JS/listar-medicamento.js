document.addEventListener("DOMContentLoaded", () => {
    let notificacionesMostradas = false;
    // Función para mostrar mensajes dinámicos para medicamentos
    function mostrarMensaje(mensaje, tipo = 'INFO') {
        const messageArea = document.getElementById("message-area");

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

    // Cargar datos al iniciar la página
    notificarStockBajo();
    loadMedicamentos();
});