$(document).ready(function () {
    $('#tabla-bostas').DataTable({
        "ajax": {
            "url": "../../controllers/bostas.controller.php",
            "type": "GET",
            "data": {
                operation: 'listarBostas'
            },
            "dataSrc": "data"
        },
        "columns": [{
            "data": "idbosta"
        },
        {
            "data": "fecha"
        },
        {
            "data": "cantidadsacos"
        },
        {
            "data": "pesoaprox"
        },
        {
            "data": "peso_diario"
        },
        {
            "data": "peso_semanal"
        },
        {
            "data": "numero_semana"
        },
        {
            "data": null,
            "render": function (data, type, row) {
                return `
                    <button class="btn btn-warning btn-sm" onclick="editarBosta(${row.idbosta})"><i class="fas fa-edit"></i> Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarBosta(${row.idbosta})"><i class="fas fa-trash-alt"></i> Eliminar</button>`;
            }
        }
        ],
        "drawCallback": function (settings) {
            var api = this.api();
            var totalAcumulado = 0;

            api.data().each(function (value) {
                totalAcumulado += parseFloat(value.peso_diario || 0);
            });

            $('#totalacumulado').text(totalAcumulado.toFixed(2));
        }
    });
});

async function eliminarBosta(idbosta) {
    if (await ask('¿Estás seguro de que deseas eliminar esta bosta?')) {
        const formData = new FormData();
        formData.append('operation', 'eliminarBosta');
        formData.append('idbosta', idbosta);

        fetch('../../controllers/bostas.controller.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status !== "error") {
                    showToast('Bosta eliminada exitosamente.', 'SUCCESS');
                    $('#tabla-bostas').DataTable().ajax.reload();
                } else {
                    showToast(data.message, 'ERROR');
                }
            })
            .catch(error => console.error('Error eliminando bosta:', error));
    }
}