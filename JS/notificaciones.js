let notificaciones = []; // Almacena todas las notificaciones

// Función para cargar notificaciones dinámicas desde el backend
async function cargarNotificacionesDinamicas() {
    try {
        // Realizar solicitudes al backend para alimentos, medicamentos y tratamientos veterinarios
        const [responseAlimentos, responseMedicamentos, responseTratamientos] = await Promise.all([
            fetch('../../controllers/alimento.controller.php', {
                method: 'POST',
                body: new URLSearchParams({ operation: 'notificarStockBajo' })
            }),
            fetch('../../controllers/admedi.controller.php?operation=notificarStockBajo'),
            fetch('../../controllers/historialme.controller.php?operation=notificarTratamientosVeterinarios')
        ]);

        const alimentosResult = await responseAlimentos.json();
        const medicamentosResult = await responseMedicamentos.json();
        const tratamientosResult = await responseTratamientos.json();

        // Limpiar el array de notificaciones actual
        notificaciones = [];

        // Procesar notificaciones de alimentos
        if (alimentosResult.status === 'success' && Array.isArray(alimentosResult.data)) {
            alimentosResult.data.forEach((notificacion) => {
                notificaciones.push({
                    mensaje: `
                        <span class="text-primary">Alimento:</span> <strong>${notificacion.nombreAlimento} , 
                        <span class="text-success">Lote:</span> ${notificacion.loteAlimento} , 
                        <span class="text-warning">Stock:</span> ${notificacion.stockActual}  
                        <span class="text-danger">(Mínimo: ${notificacion.stockMinimo}) , 
                        <span class="text-info">Estado:</span> ${notificacion.mensaje} 
                    `.replace(/\s+/g, ' ').trim(),
                    tipo: 'WARNING',
                    timestamp: new Date().toISOString()
                });
            });
        }

        // Procesar notificaciones de medicamentos
        if (medicamentosResult.status === 'success' && medicamentosResult.data) {
            if (Array.isArray(medicamentosResult.data.agotados)) {
                medicamentosResult.data.agotados.forEach((notificacion) => {
                    notificaciones.push({
                        mensaje: `
                            <span class="text-primary">Medicamento:</span> <strong>${notificacion.nombreMedicamento}  , 
                            <span class="text-success">Lote:</span> ${notificacion.loteMedicamento} , 
                            <span class="text-warning">Stock:</span> ${notificacion.stockActual} , 
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
                            <span class="text-primary">Medicamento:</span> <strong>${notificacion.nombreMedicamento}  , 
                            <span class="text-success">Lote:</span> ${notificacion.loteMedicamento}  , 
                            <span class="text-warning">Stock:</span> ${notificacion.stockActual}  
                            <span class="text-danger">(Mínimo: ${notificacion.stockMinimo})  , 
                            <span class="text-info">Estado:</span> Stock Bajo
                        `.replace(/\s+/g, ' ').trim(),
                        tipo: 'WARNING',
                        timestamp: new Date().toISOString()
                    });
                });
            }
        }

        // Procesar notificaciones de tratamientos veterinarios
        if (tratamientosResult.status === 'success' && Array.isArray(tratamientosResult.data)) {
            tratamientosResult.data.forEach((notificacion) => {
                notificaciones.push({
                    mensaje: `
                        <span class="text-primary">Equino:</span> <strong>${notificacion.nombreEquino}  , 
                        <span class="text-success">Medicamento:</span> <strong>${notificacion.nombreMedicamento}  , 
                        <span class="text-warning">Fecha Fin:</span> ${notificacion.fechaFin} , 
                        <span class="text-danger">Estado:</span> ${notificacion.TipoNotificacion}
                    `.replace(/\s+/g, ' ').trim(),
                    tipo: notificacion.TipoNotificacion === 'PRONTO' ? 'WARNING' : 'INFO',
                    timestamp: new Date().toISOString()
                });
            });
        }

        // Actualizar el contador al inicio
        actualizarContadorNotificaciones();

    } catch (error) {

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

// Función para actualizar el contador de notificaciones
function actualizarContadorNotificaciones() {
    const notificationCount = document.getElementById('notificationCount');
    if (notificationCount) {
        notificationCount.innerText = notificaciones.length;
    }
}

// Función para mostrar las notificaciones en la interfaz
function mostrarNotificaciones(limit = 6) {
    const container = document.getElementById('notificationsContainer');
    const notificationsList = document.getElementById('notificationsList');

    // Limpiar el contenido actual
    notificationsList.innerHTML = "";

    // Renderizar las primeras `limit` notificaciones
    notificaciones.slice(0, limit).forEach((notificacion) => {
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

    // Mostrar el contenedor
    container.style.display = 'block';
}

// Función para cerrar el contenedor de notificaciones
function cerrarNotificaciones() {
    const container = document.getElementById('notificationsContainer');
    container.style.display = 'none';
}

// Función para marcar todas las notificaciones como leídas
function marcarComoLeidas() {
    notificaciones = [];
    actualizarContadorNotificaciones();
    mostrarNotificaciones(6);
}

// Función para ver todas las notificaciones
window.verTodasNotificaciones = function () {

    mostrarNotificaciones(10); // Mostrar hasta 10 notificaciones con scroll
};

// Evento para inicializar notificaciones
document.addEventListener("DOMContentLoaded", () => {
    const btnNotifications = document.getElementById('btnNotifications');
    if (btnNotifications) {
        btnNotifications.addEventListener("click", () => mostrarNotificaciones(6));
    }

    const btnVerTodas = document.querySelector(".btn-view-all");
    if (btnVerTodas) {
        btnVerTodas.addEventListener("click", verTodasNotificaciones);
    }

    cargarNotificacionesDinamicas(); // Cargar notificaciones al inicio sin mostrarlas
    window.cerrarNotificaciones = cerrarNotificaciones;
});
