// Función para ajustar las fechas basadas en el filtro seleccionado
const setFechaFiltro = () => {
  const filtroRango = document.getElementById('filtroRango').value;
  const hoy = new Date();
  let fechaInicio, fechaFin;

  switch (filtroRango) {
    case 'hoy':
      fechaInicio = fechaFin = hoy.toISOString().split('T')[0];
      break;
    case 'ultimaSemana':
      fechaInicio = new Date(hoy.setDate(hoy.getDate() - 7)).toISOString().split('T')[0];
      fechaFin = new Date().toISOString().split('T')[0];
      break;
    case 'ultimoMes':
      fechaInicio = new Date(hoy.setMonth(hoy.getMonth() - 1)).toISOString().split('T')[0];
      fechaFin = new Date().toISOString().split('T')[0];
      break;
    default:
      fechaInicio = '';
      fechaFin = '';
  }

  document.getElementById('filtroRango').setAttribute('data-fecha-inicio', fechaInicio);
  document.getElementById('filtroRango').setAttribute('data-fecha-fin', fechaFin);
};