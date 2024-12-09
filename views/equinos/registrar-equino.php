<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Registro de Equino</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Equino</h5>
        </div>

        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-registro-equino" autocomplete="off">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nombreEquino" id="nombreEquino" placeholder="" class="form-control" required autofocus>
                            <label for="nombreEquino"><i class="fas fa-horse-head" style="color: #00b4d8;"></i> Nombre del Equino</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idPropietario" id="idPropietario" class="form-select">
                                <option value="">Haras Rancho Sur</option>
                            </select>
                            <label for="idPropietario"><i class="fas fa-home" style="color: #ffa500;"></i> Propietario (Opcional)</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fechaNacimiento" id="fechaNacimiento" class="form-control">
                            <label for="fechaNacimiento"><i class="fas fa-calendar-alt" style="color: #32cd32;"></i> Fecha de Nacimiento</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="sexo" id="sexo" class="form-select" required>
                                <option value="">Seleccione Sexo</option>
                                <option value="Macho">Macho</option>
                                <option value="Hembra">Hembra</option>
                            </select>
                            <label for="sexo"><i class="fas fa-venus-mars" style="color: #ba55d3;"></i> Sexo</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idTipoEquino" id="TipoEquino" class="form-select" required>
                                <option value="">Seleccione Tipo Equino</option>
                            </select>
                            <label for="TipoEquino"><i class="fas fa-venus-mars" style="color: #ba55d3;"></i> Tipo Equino</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="hidden" id="idNacionalidad" name="idNacionalidad" value="">
                            <input type="text" placeholder="" id="nacionalidad" name="nacionalidad" class="form-control" required placeholder="" list="sugerenciasNacionalidad">
                            <datalist id="sugerenciasNacionalidad"></datalist>
                            <label for="nacionalidad"><i class="fas fa-flag" style="color: #1e90ff;"></i> Busque Nacionalidad</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea type="number" name="detalles" id="detalles" placeholder="" class="form-control" style="height: 60px;"></textarea>
                            <label for="detalles"><i class="fas fa-info-circle" style="color: #1e90ff;"></i> Detalles</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="pesokg" id="pesokg" placeholder="" class="form-control" min="10" max="1000" step="0.1" required>
                            <label for="pesokg"><i class="fas fa-weight" style="color: #2d6a4f;"></i> Peso (kg)</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-floating">
                            <button name="fotografia" id="upload_button" class="form-control justify-content-center align-items-center" accept="image/*" style="text-align: center; padding: 10px; background-color: #e0f2ff; color: #007bff; border: 1px solid #007bff;">
                                <span><i class="fas fa-camera" style="color: #007bff;"></i> Seleccionar Fotografía</span>
                                <input type="hidden" id="fotografia" name="fotografia">
                            </button>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Checkbox para seleccionar si requiere estadía -->
                        <div class="form-check" id="checkboxEstadiaWrapper" style="display: none;">
                            <input type="checkbox" id="requiereEstadia" class="form-check-input">
                            <label class="form-check-label" for="requiereEstadia">
                                <i class="fas fa-home" style="color: #2d6a4f;"></i> ¿Requiere estadía?
                            </label>
                        </div>

                        <!-- Campos adicionales para fecha de entrada y salida -->
                        <div id="fechasEstadia" class="mt-3" style="display: none;">
                            <div class="form-floating mb-2">
                                <input type="date" name="fechaentrada" id="fechaentrada" placeholder="Fecha de Inicio" class="form-control">
                                <label for="fechaentrada"><i class="fas fa-calendar-alt"></i> Fecha de Entrada</label>
                            </div>
                            <div class="form-floating">
                                <input type="date" name="fechasalida" id="fechasalida" placeholder="Fecha de Fin" class="form-control">
                                <label for="fechasalida"><i class="fas fa-calendar-alt"></i> Fecha de Salida</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 text-center text-md-end mt-3">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <!-- Botón de Registrar Equino -->
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="registrar-equino" style="background-color: #0077b6; border: none;">
                                <i class="fas fa-save"></i> Registrar Equino
                            </button>

                            <!-- Botón de Cancelar -->
                            <button type="reset" class="btn btn-secondary btn-lg shadow-sm" style="background-color: #adb5bd; border: none;">
                                <i class="fas fa-times"></i> Cancelar
                            </button>

                            <!-- Botón de Editar Equinos -->
                            <button type="button" class="btn btn-warning btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#editarEquinosModal">
                                <i class="fas fa-save"></i> Editar Equinos
                            </button>

                            <!-- Botón de Registrar Propietario -->
                            <button type="button" class="btn btn-success btn-lg shadow-sm" data-bs-toggle="modal" data-bs-target="#registrarPropietario">
                                <i class="fas fa-save"></i> Registrar Propietario
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal para editar equino-->
        <div class="modal fade" id="editarEquinosModal" tabindex="-1" aria-labelledby="editarEquinosModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" style="border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);">
                    <div class="modal-header" style="background: linear-gradient(90deg, #74c0fc, #4dabf7); color: #fff;">
                        <h5 class="modal-title" id="editarEquinosModalLabel">
                            <i class="fas fa-edit me-2"></i>Editar Información del Equino
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="background-color: #f8f9fa;">
                        <!-- Campo de búsqueda de Equino -->
                        <div class="input-group mb-4">
                            <div class="form-floating flex-grow-2">
                                <input type="text" class="form-control" id="buscarEquino" placeholder="Buscar Equino" autofocus style="border-radius: 5px;">
                                <label for="buscarEquino"><i class="fas fa-search" style="color: #4dabf7;"></i> Buscar Equino</label>
                                <input type="hidden" id="idEquino" name="idEquino">
                            </div>
                            <button type="button" id="buscar-equino" class="btn btn-outline-success" title="Buscar" style="border-radius: 5px;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>

                        <!-- Primera fila: Información básica del Equino -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="fechanacimiento" style="border-radius: 5px;">
                                    <label for="fechaNacimiento"><i class="fas fa-calendar-alt" style="color: #4dabf7;"></i> Fecha Nacimiento</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="nacionalidades" style="border-radius: 5px;">
                                    <label for="nacionalidad"><i class="fas fa-flag" style="color: #4dabf7;"></i> Nacionalidad</label>
                                </div>
                            </div>
                        </div>

                        <!-- Segunda fila: Información del Propietario y Características del Equino -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="propietario" style="border-radius: 5px;">
                                    <label for="idPropietario"><i class="fas fa-user" style="color: #4dabf7;"></i> Propietario</label>
                                    <input type="hidden" id="idPropietario" name="idPropietario">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="genero" style="border-radius: 5px;">
                                    <label for="sexo"><i class="fas fa-venus-mars" style="color: #4dabf7;"></i> Sexo</label>
                                </div>
                            </div>
                        </div>

                        <!-- Tercera fila: Información adicional del Equino -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="tipoEquino" style="border-radius: 5px;">
                                    <label for="tipoEquino"><i class="fas fa-horse" style="color: #4dabf7;"></i> Tipo de Equino</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="idEstadoMonta" style="border-radius: 5px;">
                                    <label for="idEstadoMonta"><i class="fas fa-chart-line" style="color: #4dabf7;"></i> Estado Monta</label>
                                </div>
                            </div>
                        </div>

                        <!-- Cuarta fila: Peso, Fotografía y Estado -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="peso" style="border-radius: 5px;">
                                    <label for="pesokg"><i class="fas fa-weight" style="color: #4dabf7;"></i> Peso (kg)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control bg-light" id="estado" style="border-radius: 5px;">
                                    <label for="estado"><i class="fas fa-heart" style="color: #4dabf7;"></i> Estado</label>
                                </div>
                            </div>
                        </div>

                        <!-- Quinta fila: ENTRADA Y SALIDA -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control bg-light" id="ingreso" style="border-radius: 5px;">
                                    <label for="fechaentrada"><i class="fas fa-weight" style="color: #4dabf7;"></i> Fecha Entrada</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control bg-light" id="salida" style="border-radius: 5px;">
                                    <label for="fechasalida"><i class="fas fa-heart" style="color: #4dabf7;"></i> Fecha Salida</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer" style="background: #f8f9fa;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 5px;">
                            <i class="fas fa-times-circle"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" style="border-radius: 5px;">
                            <i class="fas fa-save"></i> Guardar cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Registrar Propietario -->
        <div class="modal fade" id="registrarPropietario" tabindex="-1" aria-labelledby="registrarPropietarioLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registrarPropietarioLabel">Registrar Propietario</h5>
                        <button type="button" class="btn-close" id="closeModalButton" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Formulario para registrar propietario -->
                        <form id="formRegistrarPropietario">
                            <div class="mb-3">
                                <label for="nombreHaras" class="form-label">Nombre del Haras</label>
                                <input type="text" class="form-control" id="nombreHaras" name="nombreHaras" required>
                                <div class="invalid-feedback">Por favor ingresa el nombre del Haras.</div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-lg">Registrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>
<script
    src="https://widget.cloudinary.com/v2.0/global/all.js"
    type="text/javascript">
</script>

<script src="../../JS/registro-equino.js"></script>
<script src="../../JS/editar-equino.js"></script>