// Variables capturadas desde PHP
const nombreEmpresa = "HARAS RANCHO SUR S.A.C";
const direccionEmpresa = "CAL.FUNDO CHACARILLA NRO. S/N FND. CHACARILLA (SAN LUIS - EL OLIVAR CHINCHA) ICA - CHINCHA - CHINCHA BAJA";
const modulo = "Historial Médico";




// Función para cargar la imagen y convertirla a Base64
function cargarImagenBase64(url, callback) {
    const img = new Image();
    img.crossOrigin = 'Anonymous';  // Permite acceder a imágenes desde otros dominios (CORS)

    img.onload = function () {
        // Crear un canvas para convertir la imagen a Base64
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = img.width;
        canvas.height = img.height;
        ctx.drawImage(img, 0, 0);
        const dataUrl = canvas.toDataURL('image/jpeg');  // Convertir a Base64
        callback(dataUrl);  // Llamar a la función callback con el resultado Base64
    };

    img.src = url;
}

// Función para generar el PDF
function generarPDF() {
    // La URL de la imagen en el servidor
    const imagenURL = 'https://corsproxy.io/?https://contactohipico.pe/wp-content/uploads/2020/10/IMG-20201009-WA0001.jpg';

    // Llamar a la función para convertir la imagen a Base64
    cargarImagenBase64(imagenURL, function (base64Image) {
        if (!base64Image) {
            console.error('Error: La imagen no se pudo cargar.');
            return;
        }

        // Obtener los datos de la tabla
        const tableData = $('#historialTable').DataTable().rows().data().toArray().map(item => [
            item.nombreEquino,
            item.pesokg,
            item.tipoTratamiento,
            item.estadoTratamiento,
            item.nombreMedicamento,
            item.dosis,
            item.frecuenciaAdministracion,
            item.viaAdministracion,
            item.fechaInicio,
            item.fechaFin
        ]);

        if (tableData.length === 0) {
            console.error('Error: No hay datos en la tabla.');
            return;
        }

        // Definir el contenido del PDF
        const docDefinition = {
            pageOrientation: 'portrait', // Orientación horizontal :'landscape'  y para vertical es :   'portrait', // Orientación vertical
            content: [
                {
                    image: base64Image, // Usar la imagen convertida a Base64
                    width: 220,         // Ajustar el tamaño de la imagen
                    alignment: 'center', // Alineación centrada
                    margin: [0, 0, 0, 12] // Márgenes
                },
                {
                    text: direccionEmpresa, // Dirección de la empresa
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 20]
                },
                {
                    text: `Módulo: ${modulo}`, // Módulo
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 20]
                },
                {
                    text: `Fecha de creación: ${new Date().toLocaleString()}` , // Fecha
                    style: 'subheader',
                    alignment: 'center',
                    margin: [0, 0, 0, 20]
                },
                {
                    text: 'Lista de Medicamentos:', // Título para la tabla
                    style: 'subheader',
                    margin: [0, 10, 0, 10]
                },
                {
                    table: {
                        headerRows: 1,
                        widths: ['auto', 'auto', 'auto',75, 'auto', 'auto', 'auto', 'auto'], // Definir el ancho de las columnas
                        body: [
                            [
                                { text: 'Equino', style: 'tableHeader' },
                                { text: 'Peso (kg)', style: 'tableHeader' },
                                { text: 'Tipo', style: 'tableHeader' },
                                { text: 'Estado', style: 'tableHeader' },
                                { text: 'Medicamento', style: 'tableHeader' },
                                { text: 'Dosis', style: 'tableHeader' },
                                { text: 'Frecuencia', style: 'tableHeader' },
                                { text: 'Vía', style: 'tableHeader' },
                                { text: 'Registro', style: 'tableHeader' },
                                { text: 'Fin', style: 'tableHeader' }
                            ], // Cabecera de la tabla
                            ...tableData
                        ]
                    },
                    layout: {
                        fillColor: function (rowIndex) {
                            return (rowIndex % 2 === 0) ? '#f2f2f2' : null; // Alternar color de fondo
                        },
                        hLineColor: '#cccccc',
                        vLineColor: '#cccccc',
                        paddingLeft: function (i) { return i === 0 ? 8 : 4; },
                        paddingRight: function (i, node) { return (i === node.table.widths.length - 1) ? 8 : 4; }
                    }
                }
            ],
            styles: {
                header: {
                    fontSize: 18,
                    bold: true,
                    margin: [0, 0, 0, 10]
                },
                subheader: {
                    fontSize: 14,
                    italics: true,
                    margin: [0, 10, 0, 10]
                },
                tableHeader: {
                    bold: true,
                    fontSize: 12,
                    color: 'white',
                    fillColor: '#4CAF50', // Color de fondo de la cabecera
                    alignment: 'center'
                }
            },
            defaultStyle: {
                fontSize: 10
            },
            pageSize: 'A4',
            pageMargins: [40, 60, 40, 60],
            footer: function (currentPage, pageCount) {
                return {
                    text: currentPage.toString() + ' / ' + pageCount,
                    alignment: 'center',
                    margin: [0, 30, 0, 0]
                };
            }
        };

        // Crear el PDF y abrirlo
        var pdfDoc = pdfMake.createPdf(docDefinition);

        // Abrir el PDF en una nueva ventana
        var newWindow = window.open();

        // Generar el buffer del PDF
        pdfDoc.getBuffer(function(buffer) {
            // Crear un Blob con los datos del PDF
            var blob = new Blob([buffer], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);

            // Establecer el contenido del PDF en la nueva ventana
            newWindow.location.href = url;

            // Cambiar el título de la nueva ventana
            // Usamos setTimeout para asegurarnos de que el documento esté cargado
            setTimeout(function() {
                newWindow.document.title = "HARAS RANCHO SUR"; // Título personalizado
            }, 500); // Esperar medio segundo para que la ventana cargue el PDF
        });
    });
}


// Función para generar y descargar el CSV
function generarCSV() {
    const rows = $('#historialTable').DataTable().rows().data().toArray();

    const csvContent = [
        ['Equino', 'Peso (kg)', 'Tipo', 'Estado', 'Medicamento', 'Dosis', 'Frecuencia', 'Vía', 'Registro', 'Fin'], // Cabecera
        ...rows.map(item => [
            item.nombreEquino,
            item.pesokg,
            item.tipoTratamiento,
            item.estadoTratamiento,
            item.nombreMedicamento,
            item.dosis,
            item.frecuenciaAdministracion,
            item.viaAdministracion,
            item.fechaInicio,
            item.fechaFin
        ])
    ];

    let csv = '';
    csvContent.forEach(row => {
        csv += row.join(',') + '\n';
    });

    // Crear un Blob con el contenido CSV
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });

    // Crear un enlace temporal para descargar el CSV
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'Reporte_Medicamentos.csv';
    link.click();
}



// Función para imprimir el documento
function imprimirDocumento() {
    // La URL de la imagen en el servidor
    const imagenURL = 'https://corsproxy.io/?https://contactohipico.pe/wp-content/uploads/2020/10/IMG-20201009-WA0001.jpg';

    // Llamar a la función para convertir la imagen a Base64
    cargarImagenBase64(imagenURL, function (base64Image) {
        // Crear una nueva ventana para la impresión
        const printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>HARAS RANCHO SUR</title>');
        printWindow.document.write('<style>');
        printWindow.document.write('body { font-family: Arial, sans-serif; margin: 20px; }');
        printWindow.document.write('h1, h2, h3 { text-align: center; }');
        printWindow.document.write('table { width: 100%; border-collapse: collapse; margin-top: 20px; }');
        printWindow.document.write('table, th, td { border: 1px solid black; }');
        printWindow.document.write('th, td { padding: 8px; text-align: left; }');
        printWindow.document.write('th { background-color: #f2f2f2; }');
        printWindow.document.write('</style>');
        printWindow.document.write('</head><body>');

        // Definir el contenido del documento
        printWindow.document.write('<div style="text-align: center; margin-bottom: 20px;">');
        printWindow.document.write(`<img src="${base64Image}" width="150" style="margin-bottom: 12px;">`);
        printWindow.document.write(`<div style="font-size: 14px; font-style: italic; margin-bottom: 20px;">${direccionEmpresa}</div>`);
        printWindow.document.write(`<div style="font-size: 14px; font-style: italic; margin-bottom: 20px;">Módulo: ${modulo}</div>`);
        printWindow.document.write(`<div style="font-size: 14px; font-style: italic; margin-bottom: 20px;">Fecha de creación: ${new Date().toLocaleString()}</div>`);
        printWindow.document.write('<div style="font-size: 14px; font-style: italic; margin-bottom: 10px;">Lista de Alimentos:</div>');
        printWindow.document.write('</div>');

        // Crear la tabla con los datos
        printWindow.document.write('<table>');
        printWindow.document.write('<thead><tr><th>Equino</th><th>Peso (kg)</th><th>Tipo</th><th>Estado</th><th>Medicamento</th><th>Dosis</th><th>Frecuencia</th><th>Vía</th><th>Registro</th><th>Fin</th></tr></thead>');
        printWindow.document.write('<tbody>');
        $('#tabla-medicamento').DataTable().rows().data().toArray().forEach(item => {
            printWindow.document.write('<tr>');
            printWindow.document.write(`<td>${item.nombreEquino}</td>`);
            printWindow.document.write(`<td>${item.pesokg}</td>`);
            printWindow.document.write(`<td>${item.tipoTratamiento}</td>`);
            printWindow.document.write(`<td>${item.estadoTratamiento}</td>`);
            printWindow.document.write(`<td>${item.nombreMedicamento}</td>`);
            printWindow.document.write(`<td>${item.dosis}</td>`);
            printWindow.document.write(`<td>${item.frecuenciaAdministracion}</td>`);
            printWindow.document.write(`<td>${item.viaAdministracion}</td>`);
            printWindow.document.write(`<td>${item.fechaInicio}</td>`);
            printWindow.document.write(`<td>${item.fechaFin}</td>`);
            printWindow.document.write('</tr>');
        });
        printWindow.document.write('</tbody></table>');

        printWindow.document.write('</body></html>');
        printWindow.document.close();

        // Esperar a que el contenido se cargue y luego imprimir
        printWindow.onload = function () {
            printWindow.print();
            printWindow.close();
        };
    });
}


// Configuración del DataTable
const configurarDataTableMedicamentos = () => {
    const fechaActual = new Date().toLocaleString();

    const table = $('#historialTable').DataTable({
        ajax: {
            url: '../../controllers/historialme.controller.php', // URL del archivo PHP que retorna los datos en formato JSON
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
        buttons: [
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> Generar PDF',
                className: 'btn btn-danger',
                action: function () {
                    generarPDF(); // Llamar a la función para generar el PDF
                }
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fas fa-file-csv"></i> Generar CSV',
                className: 'btn btn-success',
                action: function () {
                    generarCSV(); // Llamar a la función para generar el CSV
                }
            },
            {
                text: '<i class="fas fa-print"></i> Imprimir',
                className: 'btn btn-primary',
                action: function () {
                    imprimirDocumento(); // Llamar a la función para imprimir
                }
            }
        ]
    });
};















































