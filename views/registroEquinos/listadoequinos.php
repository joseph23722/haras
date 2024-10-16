<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #005b99;">LISTADO DE EQUINOS</h1>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Datos de los equinos</div>
                <div class="card-body">
                    <table id="tabla-equinos" class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre Equino</th>
                                <th>Fecha Nacimiento</th>
                                <th>Sexo</th>
                                <th>Tipo Equino</th>
                                <th>Detalles</th>
                                <th>Estado Monta</th>
                                <th>Nacionalidad</th>
                                <th>Fotografía</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Los datos se cargarán aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div> <!-- .col-md-12 -->
    </div> <!-- .row -->
    <!-- Fin contenido -->

    <!-- Modal -->
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fotoModalLabel">Foto del Equino</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Aquí puedes incluir el contenido que desees, como la imagen del equino -->
                    <img id="modalFoto" src="" alt="Foto del Equino" class="img-fluid" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        // Función para abrir el modal y cargar la imagen
        const fotoModal = document.getElementById('fotoModal');
        const modalFoto = document.getElementById('modalFoto');

        fotoModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget; // Botón que activó el modal
            const fotoSrc = button.getAttribute('data-foto'); // Obtiene la URL de la foto
            modalFoto.src = fotoSrc; // Cambia la fuente de la imagen en el modal
        });



        async function obtenerDatos() {
            try {
                const response = await fetch(`../../../controllers/registrarequino.controller.php?operation=getAll`, {
                    method: 'GET'
                });
                const data = await response.json();
                console.log(data);

                if (data.length > 0) {
                    let numeroFila = 1;
                    let tabla = $('#tabla-equinos tbody');
                    tabla.empty(); // Limpiar tabla antes de agregar nuevos datos

                    data.forEach(element => {
                        const fotografia = element.fotografia ? `<img src="${element.fotografia}" alt="Foto del equino" width="50" />` : 'No disponible';
                        const nuevaFila = `
              <tr>
                <td>${numeroFila}</td>
                <td>${element.nombreEquino}</td>
                <td>${element.fechaNacimiento}</td>
                <td>${element.sexo}</td>
                <td>${element.tipoEquino}</td>
                <td>${element.detalles || 'Sin detalles'}</td>
                <td>${element.nombreEstado || 'Sin estado'}</td>
                <td>${element.nacionalidad}</td>
                <td>
                <a href='#' data-idusuario='${element.idusuario}' class='btn btn-sm btn-success photo' data-bs-toggle="modal" data-bs-target="#fotoModal" data-foto="${element.fotografia}">Foto</a>
                    <div class="dropdown d-inline">
                        <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton${numeroFila}" data-bs-toggle="dropdown" aria-expanded="false">
                            &#x22EE;
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton${numeroFila}">
                            <li><a href='#' data-idusuario='${element.idusuario}' class='dropdown-item edit'>Editar</a></li>
                            <li><a href='#' data-idusuario='${element.idusuario}' class='dropdown-item delete'>Eliminar</a></li>
                            <li><a href='#' data-idusuario='${element.idusuario}' class='dropdown-item info'>Info</a></li>
                        </ul>
                    </div>
                </td>
              </tr>`;
                        numeroFila++;
                        tabla.append(nuevaFila);
                    });

                    // Inicializa DataTables después de que la tabla esté llena
                    $('#tabla-equinos').DataTable();
                }
            } catch (error) {
                console.error("Error al obtener los datos:", error);
            }
        }

        obtenerDatos();
    });
</script>

<?php require_once '../../footer.php'; ?>