// Variables capturadas desde PHP
const nombreEmpresa = "HARAS RANCHO SUR S.A.C";
const direccionEmpresa = "Calle 10 #123, Colonia San Pedro, Ciudad de Peru, Código Postal 01230";
const modulo = "Historial Herrero";

// Configuración del DataTable para el historial de herrero
const configurarDataTableHerrero = (idEquino) => {
    const fechaActual = new Date().toLocaleString();

    return {
        ajax: {
            url: '/haras/table-ssp/historial_herrero.ssp.php',
            type: 'GET',
            data: { idEquino },
            dataSrc: 'data',
            complete: function () {
                console.log("Datos cargados exitosamente en DataTable de historial de herrero.");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error en DataTable AJAX:", textStatus, errorThrown);
            }
        },
        columns: [
            { data: 'nombreEquino', title: 'Nombre del Equino' },
            { data: 'tipoEquino', title: 'Tipo de Equino' },
            { data: 'fecha', title: 'Fecha' },
            { data: 'trabajoRealizado', title: 'Trabajo Realizado' },
            { data: 'herramientasUsadas', title: 'Herramienta Usada' },
            { data: 'observaciones', title: 'Observaciones' },
            { 
                data: null, 
                title: 'Acciones', 
                orderable: false,
                render: function(data, type, row) {
                    return `<button class='btn btn-warning btn-sm' onclick='actualizarEstadoFinal(${row.idHistorialHerrero})'>Actualizar Estado</button>`;
                }
            }
        ],
        language: {
            url: '/haras/data/es_es.json'
        },
        dom: '<"d-flex justify-content-between align-items-center mb-2"<"mr-auto"l><"ml-auto"f><"text-center"B>>rt<"d-flex justify-content-between"ip>',
        buttons: [
            {
                extend: 'csvHtml5',
                text: 'Exportar CSV',
                className: 'btn btn-secondary',
                title: nombreEmpresa,
                messageTop: `
                    ${nombreEmpresa}
                    ${direccionEmpresa}
                    Nombre del Usuario: ${nombreCompletoUsuario}
                    Identificador: ${identificadorUsuario}
                    Fecha y Hora: ${fechaActual}
                    Módulo: ${modulo}
                `,
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5], // Excluye la columna de Acciones
                    modifier: {
                        order: 'applied' // Usa el orden aplicado en la tabla para el CSV
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                text: 'Exportar PDF',
                className: 'btn btn-danger',
                title: nombreEmpresa,
                orientation: 'landscape',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5], // Excluye la columna de Acciones
                    modifier: {
                        order: 'applied' // Usa el orden aplicado en la tabla para el PDF
                    }
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

                    // Estilos personalizados
                    doc.styles = {
                        header: { fontSize: 18, bold: true, color: '#004080' },
                        subheader: { fontSize: 12, color: '#333' },
                        tableHeader: { fillColor: '#4CAF50', color: 'white', alignment: 'center' }
                    };

                    // Ajusta las columnas para PDF
                    const tableContent = doc.content.find(content => content.table);
                    if (tableContent) {
                        tableContent.table.widths = Array(tableContent.table.body[0].length).fill('*');
                        tableContent.layout = {
                            hLineWidth: () => 0.5,
                            vLineWidth: () => 0.5,
                            hLineColor: () => '#aaa',
                            vLineColor: () => '#aaa'
                        };
                    }
                }
            },
            {
                extend: 'print',
                text: 'Imprimir',
                className: 'btn btn-info',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5], // Excluye la columna de Acciones
                    modifier: {
                        order: 'applied' // Usa el orden aplicado en la tabla para imprimir
                    }
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
        ],
        pageLength: 10,
        paging: true,
        searching: true,
        ordering: true,
        order: [[2, 'asc']] // Ordenar inicialmente por la columna 'Fecha'
    };
};
