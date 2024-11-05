<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 36px; color: #005b99;">
        <i class="fas fa-horse-head" style="color: #a0ffb8;"></i> Listado de Equinos
    </h1>

    <!-- Sección de Listado de Equinos -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow border-0 mt-4">
                <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #9be8e4); color: #003366;">
                    <h5 class="text-center m-0" style="font-weight: bold;">
                        <i class="fas fa-database" style="color: #3498db;"></i> Datos de los Equinos
                    </h5>
                </div>
                <div class="card-body p-4" style="background-color: #f9f9f9;">
                    <table id="tabla-equinos" class="table table-striped table-hover table-bordered">
                        <thead style="background-color: #caf0f8; color: #003366;">
                            <tr>
                                <th><i class="fas fa-hashtag"></i> #</th>
                                <th><i class="fas fa-horse"></i> Nombre Equino</th>
                                <th><i class="fas fa-calendar-alt"></i> Fecha Nacimiento</th>
                                <th><i class="fas fa-venus-mars"></i> Sexo</th>
                                <th><i class="fas fa-tag"></i> Tipo Equino</th>
                                <th><i class="fas fa-info-circle"></i> Detalles</th>
                                <th><i class="fas fa-heartbeat"></i> Estado Monta</th>
                                <th><i class="fas fa-weight-hanging"></i> Peso (kg)</th>
                                <th><i class="fas fa-flag"></i> Nacionalidad</th>
                                <th><i class="fas fa-image"></i> Fotografía</th>
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

    <!-- Modal para ver la fotografía del equino -->
    <div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-light shadow-sm">
                <div class="modal-header" style="background: linear-gradient(to right, #005b99, #0077b6); color: white;">
                    <h5 class="modal-title" id="fotoModalLabel">
                        <i class="fas fa-image"></i> Foto del Equino
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" style="background-color: #f9f9f9;">
                    <img id="modalFoto" src="" alt="Foto del Equino" class="img-fluid rounded shadow-sm" style="max-width: 100%; height: auto;" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

</div> <!-- .container-fluid -->
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
                const response = await fetch(`../../controllers/registrarequino.controller.php?operation=getAll`, {
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
                <td>${element.pesokg}</td>
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

<?php require_once '../footer.php'; ?>