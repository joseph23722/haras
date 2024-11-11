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
                    d.fechaInicio = document.getElementById('filtroRango').getAttribute('data-fecha-inicio') || '';
                    d.fechaFin = document.getElementById('filtroRango').getAttribute('data-fecha-fin') || '';
                    d.idUsuario = 0;  // ID del usuario si es necesario
                    d.limit = d.length;  // Define el límite de resultados basados en la configuración de la tabla
                    d.offset = d.start;  // Define el offset para paginación
                },
                dataSrc: 'data'
            },
            columns: [
                { data: 'idMedicamento' },
                { data: 'nombreMedicamento' },
                { data: 'descripcion' },
                { data: 'lote' },
                { data: 'stockActual' },
                { data: 'cantidad' },
                { data: 'fechaMovimiento' }
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
                    d.fechaInicio = document.getElementById('filtroRango').getAttribute('data-fecha-inicio') || '';
                    d.fechaFin = document.getElementById('filtroRango').getAttribute('data-fecha-fin') || '';
                    d.idUsuario = 0;  // ID del usuario si es necesario
                    d.limit = d.length;  // Define el límite de resultados basados en la configuración de la tabla
                    d.offset = d.start;  // Define el offset para paginación
                },
                dataSrc: 'data'
            },
            columns: [
                { data: 'idMedicamento' },
                { data: 'nombreMedicamento' },
                { data: 'descripcion' },
                { data: 'lote' },
                { data: 'tipoEquino' },
                { data: 'cantidad' },
                { data: 'motivo' },
                { data: 'fechaMovimiento' }
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
        case 'todos':
            fechaInicio = '1900-01-01';  // Fecha muy anterior para incluir todos los registros
            fechaFin = new Date().toISOString().split('T')[0];  // Fecha de hoy
            break;
        default:
            fechaInicio = '';
            fechaFin = '';
    }

    // Asigna las fechas como atributos del filtro para uso en AJAX
    document.getElementById('filtroRango').setAttribute('data-fecha-inicio', fechaInicio);
    document.getElementById('filtroRango').setAttribute('data-fecha-fin', fechaFin);
};

// Función para recargar las tablas de Entradas y Salidas sin reinicializarlas
const reloadHistorialMovimientos = () => {
    $('#tabla-entradas').DataTable().ajax.reload(null, false); // Recarga datos de Entradas sin reiniciar
    $('#tabla-salidas').DataTable().ajax.reload(null, false);  // Recarga datos de Salidas sin reiniciar
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
