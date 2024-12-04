// Función para cargar los equinos externos desde el controlador
async function cargarEquinosExternos() {
  const url = '../../controllers/equinoexterno.controller.php?operation=listarEquinosExternos';

  try {
    const response = await fetch(url);
    const data = await response.json();

    if (data.status === 'success') {
      const equinos = data.data;
      const tableBody = $('#equinos-table tbody');
      tableBody.empty();

      equinos.forEach(equino => {
        const row = `
                    <tr>
                        <td>${equino.idEquino}</td>
                        <td>${equino.nombreEquino}</td>
                        <td>${equino.sexo}</td>
                        <td>${equino.TipoEquino}</td>
                        <td>${equino.nombreEstado}</td>
                        <td>${equino.nombreHaras}</td>
                        <td>${equino.nacionalidad}</td>
                        <td>${equino.fechaentrada || 'Sin Estadía'}</td>
                        <td>${equino.fechasalida || 'Sin Finalización'}</td>
                        <td>${equino.detalles || 'Sin Detalles'}</td>
                    </tr>
                `;
        tableBody.append(row);
      });

      $('#equinos-table').DataTable();
    } else {
      showError('No se encontraron equinos externos.');
    }
  } catch (error) {
    showError('Hubo un problema al cargar los equinos externos. Intenta nuevamente más tarde.');
  }
}

function showError(message) {
  const errorMessageDiv = $('#error-message');
  errorMessageDiv.show();
  errorMessageDiv.text(message);
}
$(document).ready(function () {
  cargarEquinosExternos();
});