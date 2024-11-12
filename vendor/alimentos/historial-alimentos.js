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
                    d.tipoMovimiento = 'Entrada';
                    d.fechaInicio = document.getElementById('filtroRangoAlimentos').getAttribute('data-fecha-inicio') || '';
                    d.fechaFin = document.getElementById('filtroRangoAlimentos').getAttribute('data-fecha-fin') || '';
                },
                dataSrc: 'data'
            },
            columns: [
                { data: 'idAlimento' },
                { data: 'nombreAlimento' },
                { data: 'nombreTipoAlimento' }, // Cambiado para coincidir con el procedimiento
                { data: 'nombreUnidadMedida' }, // Cambiado para coincidir con el procedimiento
                { data: 'cantidad' },
                { data: 'lote' },
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
                    d.tipoMovimiento = 'Salida';
                    d.fechaInicio = document.getElementById('filtroRangoAlimentos').getAttribute('data-fecha-inicio') || '';
                    d.fechaFin = document.getElementById('filtroRangoAlimentos').getAttribute('data-fecha-fin') || '';
                },
                dataSrc: 'data'
            },
            columns: [
                { data: 'ID' }, // Cambiado para coincidir con el procedimiento
                { data: 'Alimento' }, // Cambiado para coincidir con el procedimiento
                { data: 'TipoEquino' }, // Cambiado para coincidir con el procedimiento
                { data: 'CantidadEquino' }, // Cambiado para coincidir con el procedimiento
                { data: 'Cantidad' },
                { data: 'Unidad' },
                { data: 'Merma' },
                { data: 'Lote' },
                { data: 'FechaSalida' } // Cambiado para coincidir con el procedimiento
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

// Inicializar DataTables de Entradas y Salidas cuando el DOM esté listo
$(document).ready(() => {
    configurarDataTableEntradasAlimentos();
    configurarDataTableSalidasAlimentos();
});
