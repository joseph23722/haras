// Función para establecer las fechas según el filtro seleccionado
const setFechaFiltroAlimentos = () => {
  try {
    const filtroRango = document.getElementById('filtroRangoAlimentos').value;
    console.log('Filtro de rango seleccionado:', filtroRango);

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
      case 'todos':
        fechaInicio = '1900-01-01';
        fechaFin = new Date().toISOString().split('T')[0];
        break;
      default:
        console.warn('Filtro de rango no válido seleccionado.');
        fechaInicio = '';
        fechaFin = '';
    }

    // Establecer fechas en atributos personalizados del filtro
    const filtroElement = document.getElementById('filtroRangoAlimentos');
    filtroElement.setAttribute('data-fecha-inicio', fechaInicio);
    filtroElement.setAttribute('data-fecha-fin', fechaFin);

    console.log(`Fechas configuradas -> Inicio: ${fechaInicio}, Fin: ${fechaFin}`);
  } catch (error) {
    console.error('Error al configurar las fechas del filtro:', error);
  }
};

// Función para recargar las tablas de Entradas y Salidas
const reloadHistorialAlimentos = () => {
  try {
    console.log('Recargando tablas...');
    if ($.fn.DataTable.isDataTable('#tabla-entradas-alimentos')) {
      $('#tabla-entradas-alimentos').DataTable().ajax.reload(
        (json) => console.log('Tabla Entradas Recargada:', JSON.stringify(json)),
        false
      );
    } else {
      console.warn('La tabla de entradas no está inicializada.');
    }

    if ($.fn.DataTable.isDataTable('#tabla-salidas-alimentos')) {
      $('#tabla-salidas-alimentos').DataTable().ajax.reload(
        (json) => console.log('Tabla Salidas Recargada:', JSON.stringify(json)),
        false
      );
    } else {
      console.warn('La tabla de salidas no está inicializada.');
    }
  } catch (error) {
    console.error('Error al recargar las tablas:', error);
  }
};

// Asegurarse de que reloadHistorialAlimentos esté disponible globalmente
window.reloadHistorialAlimentos = reloadHistorialAlimentos;

// Evento para actualizar tablas al cambiar el filtro
document.getElementById('filtroRangoAlimentos').addEventListener('change', () => {
  console.log('Cambio en el filtro de rango.');
  setFechaFiltroAlimentos();
  reloadHistorialAlimentos();
});

// Configurar y recargar tablas al cargar la página
document.addEventListener('DOMContentLoaded', () => {
  console.log('DOM completamente cargado. Configurando filtro y tablas...');
  setFechaFiltroAlimentos();
  reloadHistorialAlimentos();
});