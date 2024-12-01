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



// Llamadas para cada acción
const pausarRegistro = (idRegistro) => sendRequest(idRegistro, 'pausar');
const continuarRegistro = (idRegistro) => sendRequest(idRegistro, 'continuar');
const eliminarRegistro = (idRegistro) => sendRequest(idRegistro, 'eliminar');

// Adjuntar las funciones a botones
window.pausarRegistro = pausarRegistro;
window.continuarRegistro = continuarRegistro;
window.eliminarRegistro = eliminarRegistro;