document.addEventListener("DOMContentLoaded", function () {
    obtenerPesos();

    document.getElementById('pesoaprox').addEventListener('input', calcularPesoDiario);
    document.getElementById('cantidadsacos').addEventListener('input', calcularPesoDiario);
});

function calcularPesoDiario() {
    const pesoAprox = parseFloat(document.getElementById('pesoaprox').value) || 0;
    const cantidadSacos = parseInt(document.getElementById('cantidadsacos').value) || 0;

    const pesoDiario = pesoAprox * cantidadSacos;
    document.getElementById('peso_diario').value = pesoDiario.toFixed(2);
}

document.getElementById('form-registro-bostas').addEventListener('submit', async function (event) {
    event.preventDefault();

    if (await ask("¿Está seguro de registrar esta bosta?", "Registro de Bostas")) {
        const fechaRegistro = document.getElementById('fecha').value;
        const cantidadBostas = document.getElementById('pesoaprox').value;
        const cantidadSacos = document.getElementById('cantidadsacos').value;

        fetch('../../controllers/bostas.controller.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                operation: 'registrarBosta',
                fecha: fechaRegistro,
                cantidadsacos: cantidadSacos,
                pesoaprox: cantidadBostas,
            }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message, 'SUCCESS');
                    obtenerPesos();
                    document.getElementById('form-registro-bostas').reset();
                    calcularPesoDiario();
                } else {
                    showToast(data.message, 'ERROR');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Ocurrió un error al registrar la bosta.', 'ERROR');
            });
    }
});

// Función para obtener pesos
function obtenerPesos() {
    fetch('../../controllers/bostas.controller.php?operation=obtenerPesos', {
        method: 'GET',
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('peso_semanal').value = data.data.peso_semanal || 0;
                document.getElementById('peso_mensual').value = data.data.peso_mensual || 0;
            } else {
                console.error('Error al obtener pesos:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}