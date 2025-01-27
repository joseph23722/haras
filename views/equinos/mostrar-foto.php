<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 36px; color: #000;">
        <i class="fas fa-horse-head" style="color: #000;"></i> Buscador de fotos
    </h1>

    <div class="card mb-4 shadow-lg border-0">
        <div class="card-header text-start" style="background: linear-gradient(to left, #123524, #356C56); color: #EFE3C2;">
            <h6 class="m-0" style="font-weight: bold;">
                <i class="fas fa-info-circle" style="color: #EFE3C2;"></i> NOTA: Busque a un Equino por su Nombre para cargar las imágenes respectivas
            </h6>
        </div>

        <div class="card-body" style="background-color: #f7f9fc;">
            <form action="" id="form-historial-equino" autocomplete="off">
                <div class="row">
                    <!-- Columnas principales -->
                    <div class="col-md-12">
                        <!-- Primera fila: Búsqueda y Datos Básicos -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <div class="form-floating flex-grow-1">
                                        <input type="text" class="form-control" id="buscarEquino" placeholder="Buscar Equino" autofocus>
                                        <label for="buscarEquino"><i class="fas fa-search" style="color: #3498db;"></i> Buscar Equino</label>
                                        <!-- Campo oculto para el idEquino -->
                                        <input type="hidden" id="idEquino" name="idEquino">
                                    </div>
                                    <button type="button" id="buscar-equino" class="btn btn-outline-success" title="Buscar">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="container-fluid">
                                <!-- Sección para mostrar las fotos del equino -->
                                <div class="row mt-4" id="foto-equino-container">
                                    <!-- Las fotos se agregarán aquí de forma dinámica -->
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> <!-- .container-fluid -->

<?php require_once '../footer.php'; ?>

<script>
    // Evento de clic para buscar equino
    document.querySelector("#buscar-equino").addEventListener("click", async function() {
        const nombreEquino = document.getElementById("buscarEquino").value;

        // Verificar si el campo de búsqueda no está vacío
        if (nombreEquino.trim() === '') {
            showToast("Por favor ingresa el nombre del equino.", 'WARNING');
            return;
        }

        try {
            // Limpiar las fotos anteriores antes de hacer la búsqueda
            const fotoContainer = document.getElementById("foto-equino-container");
            fotoContainer.innerHTML = ''; // Limpiar las fotos previas

            // Hacer la llamada al backend para obtener el id del equino
            const response = await fetch('../../controllers/registrarequino.controller.php', {
                method: 'POST',
                body: JSON.stringify({
                    operation: 'buscarEquinoPorNombre',
                    nombreEquino: nombreEquino
                }),
                headers: {
                    'Content-Type': 'application/json'
                }
            });

            // Obtener los datos en formato JSON
            const data = await response.json();
            console.log(data);

            if (data.status === 'error') {
                showToast(data.message, 'WARNING');
                return;
            } else {
                // Obtener el idEquino de los resultados
                const idEquino = data[0].idEquino;
                console.log("ID del equino encontrado:", idEquino);

                // Obtener las fotos del equino con el idEquino
                const fotoResponse = await fetch('../../controllers/nuevafotoequino.controller.php?operation=listarFotografias&idEquino=' + idEquino, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const fotoData = await fotoResponse.json();
                console.log("Datos de las fotos:", fotoData);

                if (fotoData.status === 'error') {
                    console.log("No se encontraron fotos para este equino.");
                } else {
                    fotoData.fotografias.forEach(foto => {
                        const photoCard = document.createElement('div');
                        photoCard.classList.add('col-lg-2', 'col-md-3', 'col-sm-6', 'mb-4');

                        // Cambiar el texto a la fecha de creación (created_at)
                        const fechacreacionIMG = new Date(foto.created_at);
                        const formatoFechas = fechacreacionIMG.toLocaleDateString('es-ES', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });

                        photoCard.innerHTML = `
                            <div class="card shadow-sm border-0">
                                <img src="https://res.cloudinary.com/dtbhq7drd/image/upload/${foto.public_id}" class="card-img-top" alt="Imagen Equino" style="height: 250px; object-fit: cover;">
                                <div class="card-body text-center">
                                    <p class="card-text">${formatoFechas}</p> <!-- Mostrar la fecha de creación -->
                                </div>
                            </div>
                        `;
                        fotoContainer.appendChild(photoCard);
                    });
                }
            }
        } catch (error) {
            console.error('Error al realizar la búsqueda:', error);
            showToast("Hubo un error al realizar la búsqueda.", 'ERROR');
        }
    });
</script>