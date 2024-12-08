// Función para inicializar la DataTable de Entradas
const configurarDataTableEntradas = () => {
    if (!$.fn.DataTable.isDataTable('#tabla-entradas')) { // Verifica si la tabla ya está inicializada
        $('#tabla-entradas').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/haras/table-ssp/historial-medi.ssp.php',
                type: 'GET',
                data: function (d) {
                    d.tipoMovimiento = 'Entrada';
                    d.filtroFecha = document.getElementById('filtroRango').value || 'hoy'; // Usamos filtroFecha
                    d.idUsuario = 0;  // ID del usuario si es necesario
                    d.limit = d.length;  // Define el límite de resultados basados en la configuración de la tabla
                    d.offset = d.start;  // Define el offset para paginación
                    d.search.value = d.search.value || ''; // Asegurarse de que el valor de búsqueda se envíe
                },
                dataSrc: 'data'
            },
            columns: [
                { data: 'Medicamento', title: 'Medicamento' },
                { data: 'Lote', title: 'Lote' },
                { data: 'StockActual', title: 'Stock Actual' },
                { data: 'Cantidad', title: 'Cantidad' },
                { data: 'FechaMovimiento', title: 'Fecha Movimiento' }
            ],
            language: {
                url: '/haras/data/es_es.json'
            },
            paging: true,
            searching: true,
            autoWidth: false,
            responsive: true
        });
    }
};

// Función para inicializar la DataTable de Salidas
const configurarDataTableSalidas = () => {
    if (!$.fn.DataTable.isDataTable('#tabla-salidas')) { // Verifica si la tabla ya está inicializada
        $('#tabla-salidas').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/haras/table-ssp/historial-medi.ssp.php',
                type: 'GET',
                data: function (d) {
                    d.tipoMovimiento = 'Salida';
                    d.filtroFecha = document.getElementById('filtroRango').value || 'hoy'; // Usamos filtroFecha
                    d.idUsuario = 0;  // ID del usuario si es necesario
                    d.limit = d.length;  // Define el límite de resultados basados en la configuración de la tabla
                    d.offset = d.start;  // Define el offset para paginación
                    d.search.value = d.search.value || ''; // Asegurarse de que el valor de búsqueda se envíe
                },
                dataSrc: 'data'
            },
            columns: [
                { data: 'Medicamento', title: 'Medicamento' },
                { data: 'Lote', title: 'Lote' },
                { data: 'TipoEquino', title: 'Tipo Equino' },
                { data: 'CantidadEquino', title: 'Cantidad Equinos' },
                { data: 'Cantidad', title: 'Cantidad' },
                { data: 'Motivo', title: 'Motivo' },
                { data: 'FechaSalida', title: 'Fecha Movimiento' }
            ],
            language: {
                url: '/haras/data/es_es.json'
            },
            paging: true,
            searching: true,
            autoWidth: false,
            responsive: true
        });
    }
};

// Función para recargar las tablas de Entradas y Salidas sin reinicializarlas
const reloadHistorialMovimientos = () => {
    $('#tabla-entradas').DataTable().ajax.reload(null, false); // Recarga datos de Entradas sin reiniciar
    $('#tabla-salidas').DataTable().ajax.reload(null, false);  // Recarga datos de Salidas sin reiniciar
};

// Evento para actualizar el filtro de fecha y recargar las tablas cuando el usuario selecciona un nuevo rango
document.getElementById('filtroRango').addEventListener('change', () => {
    reloadHistorialMovimientos();
});

// Llamar a las funciones de configuración cuando el DOM esté listo
$(document).ready(() => {
    configurarDataTableEntradas();
    configurarDataTableSalidas();
});
