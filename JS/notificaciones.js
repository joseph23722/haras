// No declares 'notificaciones' nuevamente si ya está declarada
// Asegúrate de que esta declaración solo ocurra una vez en todo el código
let notificaciones = [
    { mensaje: "Felicitaciones Lettie 🎉 Ganó la insignia de oro del mejor vendedor del mes", tipo: "INFO", hora: "Hace 1 hora" },
    { mensaje: "Charles Franklin - Aceptó su conexión", tipo: "SUCCESS", hora: "Hace 12 horas" },
    { mensaje: "Nuevo mensaje ✉️ Tienes un nuevo mensaje de Natalie", tipo: "INFO", hora: "Hace 1 hora" },
    { mensaje: "¡Guau! Tienes un nuevo pedido", tipo: "SUCCESS", hora: "Hace 2 horas" }
];

// Función para mostrar las notificaciones al hacer clic en el ícono
function mostrarNotificaciones() {
    const container = document.getElementById('notificationsContainer');
    const notificationCount = document.getElementById('notificationCount');
    
    // Limpiar el contenedor antes de agregar nuevas notificaciones
    const notificationsList = document.getElementById('notificationsList');
    notificationsList.innerHTML = "";

    // Actualizar el contador de notificaciones
    const totalNotificaciones = notificaciones.length;
    notificationCount.innerText = totalNotificaciones;

    // Añadir las notificaciones al contenedor
    notificaciones.forEach((notificacion, index) => {
        const notificationDiv = document.createElement('div');
        notificationDiv.classList.add('notification-item');
        
        // Icono según el tipo de notificación
        const icon = notificacion.tipo === 'INFO' ? 'ℹ️' : '✅';
        
        notificationDiv.innerHTML = `
            <span class="notification-icon">${icon}</span>
            <div class="notification-text">${notificacion.mensaje}</div>
            <div class="notification-time">${notificacion.hora}</div>
        `;
        
        notificationDiv.style.animation = `fadeIn 0.5s ease forwards`;
        notificationDiv.style.animationDelay = `${index * 0.2}s`;

        notificationsList.appendChild(notificationDiv);
    });

    // Asegura que el contenedor de notificaciones sea visible
    container.style.display = 'block';
}

// Función para marcar todas las notificaciones como leídas
function marcarComoLeidas() {
    notificaciones.length = 0;  // Limpiar las notificaciones
    mostrarNotificaciones();  // Refrescar las notificaciones en el contenedor
}

// Función para ver todas las notificaciones
function verTodasNotificaciones() {
    // Implementa la funcionalidad adicional si es necesario
    alert('Ver todas las notificaciones');
}

// Función para cerrar el contenedor de notificaciones
function cerrarNotificaciones() {
    const container = document.getElementById('notificationsContainer');
    container.style.display = 'none';  // Ocultar el contenedor de notificaciones
}
