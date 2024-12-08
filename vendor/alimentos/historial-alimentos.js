// Configuración de DataTable para Entradas de Alimentos
const configurarDataTableEntradasAlimentos = () => {
    if (!$.fn.DataTable.isDataTable('#tabla-entradas-alimentos')) {
        $('#tabla-entradas-alimentos').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/haras/table-ssp/historial-alimentos.ssp.php',
                type: 'GET',
                data: function (d) {
                    // Parámetros para el servidor
                    d.tipoMovimiento = 'Entrada';
                    d.filtroFecha = document.getElementById('filtroRangoAlimentos').value || 'hoy';
                    d.search.value = d.search.value || ''; // Asegurarse de que el valor de búsqueda se envíe
                },
                dataSrc: 'data'
            },
            columns: [
                { data: 'idAlimento', title: 'ID Alimento' },
                { data: 'nombreAlimento', title: 'Nombre Alimento' },
                { data: 'nombreTipoAlimento', title: 'Tipo' },
                { data: 'nombreUnidadMedida', title: 'Unidad' },
                { data: 'cantidad', title: 'Cantidad' },
                { data: 'lote', title: 'Lote' },
                { data: 'fechaMovimiento', title: 'Fecha Movimiento' }
            ],
            language: {
                url: '/haras/data/es_es.json'
            },
            paging: true,
            searching: true,
            autoWidth: false,
            responsive: true,
            order: [[6, 'desc']] // Ordenar por fecha de movimiento
        });
    } else {
        $('#tabla-entradas-alimentos').DataTable().ajax.reload();
    }
};

// Configuración de DataTable para Salidas de Alimentos
const configurarDataTableSalidasAlimentos = () => {
    if (!$.fn.DataTable.isDataTable('#tabla-salidas-alimentos')) {
        $('#tabla-salidas-alimentos').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/haras/table-ssp/historial-alimentos.ssp.php',
                type: 'GET',
                data: function (d) {
                    // Parámetros para el servidor
                    d.tipoMovimiento = 'Salida';
                    d.filtroFecha = document.getElementById('filtroRangoAlimentos').value || 'hoy';
                    d.search.value = d.search.value || ''; // Asegurarse de que el valor de búsqueda se envíe
                    
                },
                dataSrc: 'data'
            },
            columns: [
                { data: 'ID', title: 'ID Movimiento' },
                { data: 'Alimento', title: 'Nombre Alimento' },
                { data: 'TipoEquino', title: 'Tipo Equino' },
                { data: 'CantidadEquino', title: 'Cantidad Equinos' },
                { data: 'Cantidad', title: 'Cantidad Salida' },
                { data: 'Unidad', title: 'Unidad' },
                { data: 'Merma', title: 'Merma' },
                { data: 'Lote', title: 'Lote' },
                { data: 'FechaSalida', title: 'Fecha Movimiento' }
            ],
            language: {
                url: '/haras/data/es_es.json'
            },
            paging: true,
            searching: true,
            autoWidth: false,
            responsive: true,
            order: [[8, 'desc']] // Ordenar por fecha de salida
        });
    } else {
        $('#tabla-salidas-alimentos').DataTable().ajax.reload();
    }
};

// Inicializar DataTables de Entradas y Salidas cuando el DOM esté listo
$(document).ready(() => {
    configurarDataTableEntradasAlimentos();
    configurarDataTableSalidasAlimentos();

    // Escuchar cambios en el filtro de rango de fechas
    $('#filtroRangoAlimentos').on('change', () => {
        configurarDataTableEntradasAlimentos();
        configurarDataTableSalidasAlimentos();
    });
});
