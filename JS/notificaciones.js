let notificaciones = [];

// Función para cargar notificaciones dinámicas desde el backend
async function cargarNotificacionesDinamicas() {
    try {
        // Realizar solicitudes al backend para alimentos y medicamentos
        const [responseAlimentos, responseMedicamentos] = await Promise.all([
            fetch('../../controllers/alimento.controller.php', {
                method: 'POST',
                body: new URLSearchParams({ operation: 'notificarStockBajo' })
            }),
            fetch('../../controllers/admedi.controller.php?operation=notificarStockBajo')
        ]);

        const alimentosResult = await responseAlimentos.json();
        const medicamentosResult = await responseMedicamentos.json();

        console.log("Alimentos Result:", alimentosResult);
        console.log("Medicamentos Result:", medicamentosResult);

        // Limpiar el array de notificaciones actual
        notificaciones = [];

        // Procesar notificaciones de alimentos
        if (alimentosResult.status === 'success' && Array.isArray(alimentosResult.data)) {
            alimentosResult.data.forEach((notificacion) => {
                notificaciones.push({
                    mensaje: `
                        <span class="text-primary">Alimento:</span> <strong>${notificacion.nombreAlimento}</strong> , 
                        <span class="text-success">Lote:</span> ${notificacion.loteAlimento} , 
                        <span class="text-warning">Stock:</span> ${notificacion.stockActual} 
                        <span class="text-danger">(Mínimo: ${notificacion.stockMinimo})</span> , 
                        <span class="text-info">Estado:</span> ${notificacion.mensaje}
                    `.replace(/\s+/g, ' ').trim(),
                    tipo: 'WARNING',
                    timestamp: new Date().toISOString() // Marca de tiempo exacta
                });
            });
        }

        // Procesar notificaciones de medicamentos
        if (medicamentosResult.status === 'success' && medicamentosResult.data) {
            if (Array.isArray(medicamentosResult.data.agotados)) {
                medicamentosResult.data.agotados.forEach((notificacion) => {
                    notificaciones.push({
                        mensaje: `
                            <span class="text-primary">Medicamento:</span> <strong>${notificacion.nombreMedicamento}</strong> , 
                            <span class="text-success">Lote:</span> ${notificacion.loteMedicamento} , 
                            <span class="text-warning">Stock:</span> ${notificacion.stockActual} 
                            <span class="text-info">Estado:</span> Agotado
                        `.replace(/\s+/g, ' ').trim(),
                        tipo: 'ERROR',
                        timestamp: new Date().toISOString()
                    });
                });
            }

            if (Array.isArray(medicamentosResult.data.bajoStock)) {
                medicamentosResult.data.bajoStock.forEach((notificacion) => {
                    notificaciones.push({
                        mensaje: `
                            <span class="text-primary">Medicamento:</span> <strong>${notificacion.nombreMedicamento}</strong> , 
                            <span class="text-success">Lote:</span> ${notificacion.loteMedicamento} , 
                            <span class="text-warning">Stock:</span> ${notificacion.stockActual} 
                            <span class="text-danger">(Mínimo: ${notificacion.stockMinimo})</span> , 
                            <span class="text-info">Estado:</span> Stock Bajo
                        `.replace(/\s+/g, ' ').trim(),
                        tipo: 'WARNING',
                        timestamp: new Date().toISOString()
                    });
                });
            }
        }

        // Mostrar notificaciones en la interfaz
        mostrarNotificaciones();

        // Eliminar notificaciones antiguas
        eliminarNotificacionesAntiguas();

    } catch (error) {
        console.error('Error al cargar notificaciones:', error);
    }
}

// Función para calcular tiempo relativo
function calcularTiempoRelativo(timestamp) {
    const now = new Date();
    const then = new Date(timestamp);
    const diffMs = now - then;

    const minutes = Math.floor(diffMs / (1000 * 60));
    const hours = Math.floor(diffMs / (1000 * 60 * 60));
    const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));

    if (days > 0) return `Hace ${days} día(s)`;
    if (hours > 0) return `Hace ${hours} hora(s)`;
    if (minutes > 0) return `Hace ${minutes} minuto(s)`;
    return "Hace unos segundos";
}

// Función para eliminar notificaciones con más de 30 días
function eliminarNotificacionesAntiguas() {
    const now = new Date();
    notificaciones = notificaciones.filter(notificacion => {
        const notificacionFecha = new Date(notificacion.timestamp);
        const diffDays = (now - notificacionFecha) / (1000 * 60 * 60 * 24);
        return diffDays <= 30; // Mantener solo las notificaciones más recientes
    });
}

// Función para mostrar las notificaciones en la interfaz
function mostrarNotificaciones() {
    const container = document.getElementById('notificationsContainer');
    const notificationCount = document.getElementById('notificationCount');
    const notificationsList = document.getElementById('notificationsList');

    // Limpiar el contenido actual
    notificationsList.innerHTML = "";
    notificationCount.innerText = notificaciones.length; // Actualizar el contador

    // Renderizar cada notificación
    notificaciones.forEach((notificacion) => {
        const notificationDiv = document.createElement('div');
        notificationDiv.classList.add('notification-item', 'd-flex', 'align-items-start', 'p-2');
        notificationDiv.innerHTML = `
            <span class="notification-icon me-2">
                <i class="${notificacion.tipo === 'WARNING' ? 'fas fa-exclamation-triangle text-warning' : 'fas fa-info-circle text-info'}"></i>
            </span>
            <div>
                <div class="notification-text mb-1">${notificacion.mensaje}</div>
                <small class="notification-time text-muted">${calcularTiempoRelativo(notificacion.timestamp)}</small>
            </div>
        `;
        notificationsList.appendChild(notificationDiv);
    });

    container.style.display = 'block'; // Mostrar contenedor de notificaciones
}

// Función para cerrar el contenedor de notificaciones
function cerrarNotificaciones() {
    const container = document.getElementById('notificationsContainer');
    container.style.display = 'none';
}

// Función para marcar todas las notificaciones como leídas
function marcarComoLeidas() {
    notificaciones = []; // Vaciar el array de notificaciones
    mostrarNotificaciones(); // Refrescar el contenedor
}

// Evento para cargar notificaciones dinámicamente al cargar la página
document.addEventListener("DOMContentLoaded", () => {
    cargarNotificacionesDinamicas();
    window.cerrarNotificaciones = cerrarNotificaciones;
});
