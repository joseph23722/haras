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

const loadAlimentos = async () => {
    try {
        const params = new URLSearchParams({ operation: 'getAllAlimentos' });
        const response = await fetch(`../../controllers/alimento.controller.php?${params.toString()}`, {
            method: "GET"
        });

        const textResponse = await response.text();
        if (textResponse.startsWith("<")) {
            mostrarMensajeDinamico("Error en la respuesta del servidor.", 'ERROR');
            showToast("Error en la respuesta del servidor", 'ERROR');
            return;
        }
        const result = JSON.parse(textResponse);
        const alimentos = result.data;

        // Cargar datos en la tabla de alimentos
        if ($.fn.dataTable.isDataTable('#alimentos-table')) {
            $('#alimentos-table').DataTable().clear().rows.add(alimentos).draw();
        } else {
            configurarDataTableAlimentos();
        }

    } catch (error) {
        mostrarMensajeDinamico("Error al cargar alimentos: " + error.message, 'ERROR');
        showToast("Error al cargar alimentos", 'ERROR');
    }
};

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

// Asegurarse de que el DOM esté completamente cargado antes de ejecutar el código
document.addEventListener('DOMContentLoaded', () => {
    loadAlimentos();
});