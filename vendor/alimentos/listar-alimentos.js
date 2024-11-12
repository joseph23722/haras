// Variables capturadas desde PHP
const nombreEmpresa = "HARAS RANCHO SUR S.A.C";
const direccionEmpresa = "Calle 10 #123, Colonia San Pedro, Ciudad de Peru, Código Postal 01230";
const modulo = "Alimentos";

const configurarDataTableAlimentos = () => {
    const fechaActual = new Date().toLocaleString();

    const table = $('#alimentos-table').DataTable({
        ajax: {
            url: '/haras/table-ssp/alimento.ssp.php', // URL del archivo PHP que retorna los datos en formato JSON
            type: 'GET',
            dataSrc: 'data',
            error: function (xhr, status, error) {
                console.error("Error al cargar datos de la tabla:", error);
            }
        },
        processing: true,
        serverSide: true,
        pageLength: 10,
        paging: true,
        pagingType: "full_numbers",
        responsive: true, // Activa la responsividad en el DataTable
        autoWidth: false, // Desactiva el ancho automático
        language: {
            url: '/haras/data/es_es.json'
        },
        dom: '<"d-flex justify-content-between align-items-center mb-2"<"mr-auto"l><"ml-auto"f><"text-center"B>>rt<"d-flex justify-content-between"ip>',
        columns: [
            { data: 'idAlimento', visible: false, searchable: false }, // Ocultar y deshabilitar búsqueda en ID
            { data: 'nombreAlimento', searchable: true },
            { data: 'nombreTipoAlimento', searchable: true },
            { data: 'unidadMedidaNombre', searchable: false },
            { data: 'lote', searchable: false },
            { data: 'stockActual', searchable: false },
            { data: 'stockMinimo', searchable: false },
            { data: 'costo', searchable: false },
            { data: 'fechaCaducidad', searchable: true },
            { data: 'estado', searchable: false },
            { 
                data: null, 
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<button class="btn btn-danger btn-sm" onclick="eliminarAlimento('${row.idAlimento}')">
                                <i class="fas fa-trash"></i>
                            </button>`;
                }
            }
        ],
        buttons: [
            {
                extend: 'csvHtml5',
                text: 'Exportar CSV',
                className: 'btn btn-secondary',
                title: nombreEmpresa,
                messageTop: `
                    <div style="text-align: center; font-weight: bold; font-size: 20px; color: #004080; margin-bottom: 10px;">
                        ${nombreEmpresa}
                    </div>
                    <div style="text-align: center; font-size: 14px; color: #333; margin-bottom: 20px;">
                        ${direccionEmpresa}
                    </div>
                    <div style="border: 1px solid #ccc; padding: 10px; font-size: 12px; color: #555; margin-bottom: 10px;">
                        <strong>Nombre del Usuario:</strong> ${nombreCompletoUsuario}<br>
                        <strong>Identificador:</strong> ${identificadorUsuario}<br>
                        <strong>Fecha y Hora:</strong> ${fechaActual}<br>
                        <strong>Módulo:</strong> ${modulo}
                    </div>
                `,
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] // Excluye ID (0) y Acciones (última columna)
                }
            },
            {
                extend: 'pdfHtml5',
                text: 'Exportar PDF',
                className: 'btn btn-danger',
                title: nombreEmpresa,
                orientation: 'landscape',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] // Excluye ID (0) y Acciones (última columna)
                },
                customize: function (doc) {
                    doc.content.splice(0, 0, {
                        text: nombreEmpresa,
                        style: 'header',
                        alignment: 'center',
                        margin: [0, 10, 0, 10]
                    }, {
                        text: direccionEmpresa,
                        style: 'subheader',
                        alignment: 'center',
                        margin: [0, 0, 0, 10]
                    }, {
                        text: `Nombre del Personal: ${nombreCompletoUsuario}\nIdentificador: ${identificadorUsuario}\nFecha y Hora: ${fechaActual}\nMódulo: ${modulo}`,
                        style: 'subheader',
                        alignment: 'center',
                        margin: [0, 0, 0, 20]
                    });

                    doc.styles = {
                        header: { fontSize: 18, bold: true, color: '#004080' },
                        subheader: { fontSize: 12, color: '#333' },
                        tableHeader: { fillColor: '#4CAF50', color: 'white', alignment: 'center' }
                    };

                    const tableContent = doc.content.find(content => content.table);
                    if (tableContent) {
                        const columnCount = tableContent.table.body[0].length;
                        tableContent.table.widths = Array(columnCount).fill('*');
                        tableContent.layout = {
                            hLineWidth: function () { return 0.5; },
                            vLineWidth: function () { return 0.5; },
                            hLineColor: function () { return '#aaa'; },
                            vLineColor: function () { return '#aaa'; },
                        };
                    }
                }
            },
            {
                extend: 'print',
                text: 'Imprimir',
                className: 'btn btn-info',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9] // Excluye ID (0) y Acciones (última columna)
                },
                customize: function (win) {
                    $(win.document.body)
                        .css('font-size', '10pt')
                        .prepend(`
                            <div style="text-align: center; font-size: 20px; font-weight: bold; color: #004080;">
                                ${nombreEmpresa}
                            </div>
                            <div style="text-align: center; font-size: 14px; color: #333; margin-bottom: 20px;">
                                ${direccionEmpresa}
                            </div>
                            <div style="border: 1px solid #ccc; padding: 10px; font-size: 12px; color: #555; margin-bottom: 10px;">
                                <strong>Nombre del Personal:</strong> ${nombreCompletoUsuario}<br>
                                <strong>Identificador:</strong> ${identificadorUsuario}<br>
                                <strong>Fecha y Hora:</strong> ${fechaActual}<br>
                                <strong>Módulo:</strong> ${modulo}
                            </div>
                            <br>
                        `);
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }
        ]
    });

    // Filtro de búsqueda personalizado para las columnas específicas
    $('#alimentos-table_filter input').off('keyup').on('keyup', function() {
        const searchValue = $(this).val().trim();

        console.log("Valor de búsqueda ingresado:", searchValue); // Log del valor ingresado

        // Aplicar búsqueda en columnas específicas individualmente y combinar
        const searchResult = table
            .columns([1, 2, 8])
            .every(function (index) {
                this.search(searchValue);
                console.log("Aplicando búsqueda en columna", index); // Confirma que se aplica búsqueda a cada columna
                return true;
            })
            .draw();

        console.log("Búsqueda completada:", searchResult); // Confirma el resultado de la búsqueda
    });
};
