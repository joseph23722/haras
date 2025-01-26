<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #000;">Seleccione Tipo de Revisión</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to left, #123524, #356C56);">
        </div>

        <div class="card-body p-1" style="background-color: #f9f9f9;">
            <div class="d-flex justify-content-center mt-1">
                <a href="./revisar-equino"
                    class="btn btn-warning btn-lg mx-3"
                    style="font-size: 1.1em; padding: 12px 30px; background-color: #123524; color: #EFE3C2;">
                    Revisión Básica
                </a>
                <a href="./diagnosticar-equino"
                    class="btn btn-warning btn-lg mx-3"
                    style="font-size: 1.1em; padding: 12px 30px; background-color: #001F3F; color: #EFE3C2;">
                    Revisión Avanzada
                </a>
            </div>

        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>