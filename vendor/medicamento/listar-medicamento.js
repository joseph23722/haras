// Variables capturadas desde PHP
const nombreEmpresa = "HARAS RANCHO SUR S.A.C";
const direccionEmpresa = "Calle 10 #123, Colonia San Pedro, Ciudad de Peru, Código Postal 01230";
const modulo = "Medicamentos";

// Función para configurar la tabla
const configurarDataTableMedicamentos = () => {
    const fechaActual = new Date().toLocaleString();

    $('#tabla-medicamentos').DataTable({
        ajax: {
            url: '/haras/table-ssp/medicamento.ssp.php',
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
        language: {
            url: '/haras/data/es_es.json'
        },
        dom: '<"d-flex justify-content-between align-items-center mb-2"<"mr-auto"l><"ml-auto"f><"text-center"B>>rt<"d-flex justify-content-between"ip>',
        columns: [
            { data: 'nombreMedicamento' },
            { data: 'lote' },
            { data: 'presentacion', defaultContent: 'N/A' },
            { data: 'dosis', defaultContent: 'N/A' },
            { data: 'nombreTipo', defaultContent: 'N/A' },
            { data: 'fechaCaducidad', defaultContent: 'N/A' },
            { data: 'cantidad_stock', defaultContent: 'N/A' },
            { data: 'precioUnitario', defaultContent: 'N/A' },
            { data: 'fechaIngreso', defaultContent: 'N/A' },
            { data: 'estado', defaultContent: 'N/A' },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `<button class="btn btn-danger btn-sm" onclick="borrarMedicamento('${row.idMedicamento}')">
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
            },
            {
                extend: 'pdfHtml5',
                text: 'Exportar PDF',
                className: 'btn btn-danger',
                title: nombreEmpresa,
                orientation: 'landscape',  // Establece la orientación a horizontal
                customize: function (doc) {
                    // Remover encabezado duplicado configurando messageTop directamente en el PDF
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
            
                    // Ajuste de estilos y alineación de la tabla
                    doc.styles = {
                        header: { fontSize: 18, bold: true, color: '#004080' },
                        subheader: { fontSize: 12, color: '#333' },
                        tableHeader: { fillColor: '#4CAF50', color: 'white', alignment: 'center' }
                    };
            
                    // Buscamos el contenido de la tabla y ajustamos su tamaño
                    const tableContent = doc.content.find(content => content.table);
                    if (tableContent) {
                        const columnCount = tableContent.table.body[0].length;
                        tableContent.table.widths = Array(columnCount).fill('*');  // Ajuste automático de columnas
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
};

// Llama a la función cuando el DOM esté listo
$(document).ready(() => {
    configurarDataTableMedicamentos();
});  