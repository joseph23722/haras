<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 36px; color: #0056b3;">
        <i class="fas fa-horse-head" style="color: #a0ffb8;"></i> Historial Equino
    </h1>

    <div class="card mb-4 shadow-lg border-0">
        <div class="card-header text-center" style="background: linear-gradient(90deg, #a0ffb8, #9be8e4); color: #003366;">
            <h5 class="m-0" style="font-weight: bold;">
                <i class="fas fa-info-circle" style="color: #3498db;"></i> Información del Equino
            </h5>
        </div>

        <div class="card-body" style="background-color: #f7f9fc;">
            <form action="" id="form-historial-equino" autocomplete="off">
                <div class="row">
                    <!-- Columnas principales -->
                    <div class="col-md-8">
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
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="fechaNacimiento" disabled>
                                    <label for="fechaNacimiento"><i class="fas fa-calendar-alt" style="color: #3498db;"></i> Fecha Nacimiento</label>
                                </div>
                            </div>
                        </div>

                        <!-- Segunda fila: Información del Propietario y Características del Equino -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="nacionalidad" disabled>
                                    <label for="nacionalidad"><i class="fas fa-flag" style="color: #3498db;"></i> Nacionalidad</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="idPropietario" disabled>
                                    <label for="idPropietario"><i class="fas fa-user" style="color: #3498db;"></i> Propietario</label>
                                </div>
                            </div>
                        </div>

                        <!-- Tercera fila: Información adicional del Equino -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="sexo" disabled>
                                    <label for="sexo"><i class="fas fa-venus-mars" style="color: #3498db;"></i> Sexo</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="tipoEquino" disabled>
                                    <label for="tipoEquino"><i class="fas fa-horse" style="color: #3498db;"></i> Tipo de Equino</label>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="idEstadoMonta" disabled>
                                    <label for="idEstadoMonta"><i class="fas fa-chart-line" style="color: #3498db;"></i> Estado Monta</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="pesokg" disabled>
                                    <label for="pesokg"><i class="fas fa-weight" style="color: #3498db;"></i> Peso (kg)</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terca COLUMNA: Fotografía del equino -->
                    <div class="col-md-4 text-center">
                        <div class="card bg-gradient equinos-card">
                            <h5 class="card-title fancy-title text-center">
                                <strong><u>Foto del Equino</u></strong>
                            </h5>
                            <div class="text-center">
                                <img src="https://via.placeholder.com/400x365?text=Imagen+No+Disponible"
                                    class="img-fluid equino-image"
                                    id="fotografia"
                                    alt="Foto del Equino"
                                    style="width: 100%; height: auto; max-width: 300px; object-fit: cover;">
                            </div>
                        </div>
                    </div>

                    <!-- Cuarta fila: Información adicional -->
                    <div class="row g-4 mb-4">
                        <h4 class="m-0" style="font-weight: bold;">
                            <i class="fas fa-info-circle" style="color: #3498db;"></i>
                            Nota:
                            <h6>En este apartado se registrará toda información como Padre, Madre, carreras ganadas y otra información adicional relevante del Equino.</h6>
                        </h4>
                        <div class="col-md-12">
                            <div class="form-floating">
                                <!-- Aquí añadimos el editor Quill -->
                                <div id="descripcion" style="height: 200px;"></div>
                                <input type="hidden" name="descripcion" id="descripcion">
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="row g-3">
                        <div class="col-md-12 text-end mt-3">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm me-2" id="registrar-historial" style="background-color: #0056b3; border: none;">
                                <i class="fas fa-save"></i> Registrar Historial
                            </button>
                            <button type="reset" class="btn btn-secondary btn-lg shadow-sm" style="background-color: #adb5bd; border: none;">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../swalcustom.js"></script>
<!-- Incluir las dependencias de Quill -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script src="../../JS/historialequino.js"></script>