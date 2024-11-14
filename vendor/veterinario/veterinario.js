// Variables capturadas desde PHP
const nombreEmpresa = "HARAS RANCHO SUR S.A.C";
const direccionEmpresa = "Calle 10 #123, Colonia San Pedro, Ciudad de Peru, Código Postal 01230";
const modulo = "Historial Médico";

const configurarDataTableHistorial = () => {
    const fechaActual = new Date().toLocaleString();

    return {
        ajax: {
            url: '../../controllers/historialme.controller.php',
            type: 'GET',
            data: { operation: 'consultarHistorialMedico' },
            dataSrc: 'data',
            complete: function () {
                console.log("Datos cargados exitosamente en DataTable de historial.");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error en DataTable AJAX:", textStatus, errorThrown);
            }
        },
        columns: [
            { data: 'nombreEquino', title: 'Equino' },
            { data: 'pesokg', title: 'Peso (kg)' },
            { data: 'tipoTratamiento', title: 'Tipo' },
            { data: 'estadoTratamiento', title: 'Estado' },
            { data: 'nombreMedicamento', title: 'Medicamento' },
            { data: 'dosis', title: 'Dosis' },
            { data: 'frecuenciaAdministracion', title: 'Frecuencia' },
            { data: 'viaAdministracion', title: 'Vía' },
            { data: 'fechaInicio', title: 'Registro' },
            { data: 'fechaFin', title: 'Fin' },
            {
                data: 'observaciones',
                title: 'Observaciones',
                render: function (data) {
                    return data ? data : 'Ninguna';
                }
            },
            {
                data: 'reaccionesAdversas',
                title: 'Reacciones',
                render: function (data) {
                    return data ? data : 'Ninguna';
                }
            },
            {
                data: null,
                orderable: false,
                title: 'Acciones',
                render: function (data, type, row) {
                    return `
                        <div class="btn-group" role="group" aria-label="Acciones">
                            <button class="btn btn-sm btn-warning" onclick="pausarRegistro(${row.idRegistro})" title="Pausar">
                                <i class="fas fa-pause-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-success" onclick="continuarRegistro(${row.idRegistro})" title="Continuar">
                                <i class="fas fa-play-circle"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="eliminarRegistro(${row.idRegistro})" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: '/haras/data/es_es.json'
        },
        dom: '<"d-flex justify-content-between align-items-center mb-2"<"mr-auto"l><"ml-auto"f><"d-inline-flex"B>>rt<"d-flex justify-content-between"ip>',
        buttons: [
            {
                extend: 'csvHtml5',
                text: 'Exportar CSV',
                className: 'btn btn-secondary',
                title: nombreEmpresa,
                orientation: 'landscape', // Aquí se especifica la orientación horizontal
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
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                }
            },
            {
                extend: 'pdfHtml5',
                text: 'Exportar PDF',
                className: 'btn btn-danger',
                title: nombreEmpresa,
                orientation: 'landscape',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
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
                orientation: 'landscape', // Aquí se especifica la orientación horizontal
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
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
        order: [[0, 'asc']]
    };
};
