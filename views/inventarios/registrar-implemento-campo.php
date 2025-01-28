<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título de la página -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #000;">
        Gestionar Implementos de Campo
    </h1>

    <!-- Sección del formulario para registrar un Implemento -->
    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to left, #123524, #356C56);">
        </div>
        <div class="card-body p-4" style="background-color: #f9f9f9;">

            <form action="" id="form-registrar-implemento" autocomplete="off">
                <div class="row g-3">
                    <!-- Nombre del tipo de inventario -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idTipoinventario" id="idTipoinventario" class="form-select" required>
                                <option value="2">Implementos Campos</option>
                            </select>
                            <label for="idTipoinventario">
                                <i class="fas fa-box" style="color: #001F3F;"></i> Tipo de Inventario
                            </label>
                        </div>
                    </div>

                    <!-- Nombre del Producto -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nombreProducto" id="nombreProducto" placeholder="" class="form-control">
                            <label for="nombreProducto">
                                <i class="fas fa-info-circle" style="color: #001F3F;"></i> Nombre Producto
                            </label>
                        </div>
                    </div>

                    <!-- Cantidad -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="cantidad" id="cantidad" class="form-control" placeholder="">
                            <label for="cantidad">
                                <i class="fas fa-battery-quarter" style="color: #001F3F;"></i> Cantidad
                            </label>
                        </div>
                    </div>

                    <!-- Precio Unitario -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="precioUnitario" id="precioUnitario" class="form-control" required min="0" placeholder="">
                            <label for="precioUnitario">
                                <i class="fas fa-dollar-sign" style="color: #001F3F;"></i> Precio Unitario
                            </label>
                        </div>
                    </div>

                    <!-- Precio Unitario -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="precioTotal" id="precioTotal" class="form-control" placeholder="" disabled>
                            <label for="precioTotal">
                                <i class="fas fa-dollar-sign" style="color: #001F3F;"></i> Precio Total
                            </label>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="descripcion" id="descripcion" class="form-control" placeholder="" required>
                            <label for="descripcion">
                                <i class="fas fa-box" style="color: #001F3F;"></i> Descripcion
                            </label>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg" style="background-color: #123524; border: none;">
                            <i class="fas fa-save"></i> Registrar Implemento
                        </button>
                        <button type="reset" class="btn btn-secondary btn-lg" style="background-color: #adb5bd; border: none;">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Opciones de Movimiento -->
    <div class="card mb-4">
        <div class="card-header" style="background: linear-gradient(to left, #123524, #356C56); color: #EFE3C2;">
            <h5 class="text-center"><i class="fas fa-exchange-alt"></i> Opciones de Movimiento</h5>
        </div>
        <div class="card-body text-center" style="background-color: #f9f9f9;">
    <div class="row justify-content-center">
        <!-- Botón Listado Implementos -->
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <button onclick="window.location.href='./listar-implemento-campo'" class="btn btn-lg w-100" style="background-color: #001F3F; border-color: #001F3F; color: #EFE3C2;">
                <i class="fas fa-save"></i> Listado Implementos
            </button>
        </div>

        <!-- Botón Registrar Movimiento de Implementos -->
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <button class="btn btn-lg w-100 me-3 btn-custom-single" data-bs-toggle="modal" data-bs-target="#modalMovimiento" style="background-color: #dc3545; border-color: #dc3545; color: #EFE3C2;">
                <i class="fas fa-arrow-down"></i> Registrar Movimiento de Implementos
            </button>
        </div>

        <!-- Botón Historial de Movimientos -->
        <div class="col-12 col-sm-6 col-md-3 mb-3">
            <button class="btn btn-lg w-100" style="background-color: #123524; border-color: #123524; color: #EFE3C2;" onclick="window.location.href='./listar-historial-I-campo'">
                <i class="fas fa-arrow-down"></i> Historial Movimientos (E/S)
            </button>
        </div>
    </div>
</div>

    </div>

    <!-- Modal para Registrar Movimiento de Producto -->
    <div class="modal fade" id="modalMovimiento" tabindex="-1" aria-labelledby="modalMovimientoLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Encabezado del Modal -->
                <div class="modal-header" style="background-color: #5a67d8; color: white; border-top-left-radius: .3rem; border-top-right-radius: .3rem;">
                    <h5 class="modal-title" id="modalMovimientoLabel">Registrar Movimientos de Implementos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color: white;"></button>
                </div>

                <!-- Cuerpo del Modal -->
                <div class="modal-body px-4 py-3">
                    <form id="formMovimiento">
                        <!-- Selección de Tipo inventario -->
                        <div class="row mb-3">
                            <div class="col-md-6 ">
                                <label for="idTipoinventario" class="form-label fw-bold">Tipo inventario</label>
                                <select name="idTipoinventario" id="idTipoinventario" class="form-select form-select-lg" required readonly>
                                    <option value="2">Implementos Campos</option>
                                    <!-- Opciones cargadas dinámicamente -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="idTipoMovimiento" class="form-label fw-bold">Tipo movimiento</label>
                                <select name="idTipoMovimiento" id="idTipoMovimiento" class="form-select form-select-lg" required>
                                    <option value="">Seleccione un tipo movimiento</option>
                                    <!-- Opciones cargadas dinámicamente -->
                                </select>
                            </div>
                        </div>

                        <!-- Selección de idInventario a traves del nombreProducto -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="idInventario" class="form-label fw-bold">Nombre Producto</label>
                                <select name="idInventario" id="idInventario" class="form-select form-select-lg" required>
                                    <option value="">Seleccione un producto</option>
                                    <!-- Opciones cargadas dinámicamente -->
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="cantidad" class="form-label fw-bold">Cantidad</label>
                                <input type="number" name="cantidad" id="cantidad" class="form-control form-control-lg" required min="1" placeholder="Ingrese cantidad">
                            </div>
                        </div>

                        <!-- Precio Unitario -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="precioUnitario" class="form-label fw-bold">Precio Unitario</label>
                                <input type="number" name="precioUnitario" id="precioUnitario" class="form-control form-control-lg" min="1" placeholder="Ingrese precio unitario">
                            </div>
                            <!-- Motivo de la salida -->
                            <div class="col-md-6">
                                <label for="descripcion" class="form-label fw-bold">Motivo</label>
                                <input name="descripcion" id="descripcion" class="form-control form-control-lg" placeholder="Explique el motivo del movimiento"></input>
                            </div>
                        </div>

                        <!-- Botón para registrar movimiento -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger px-4">Registrar Movimiento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>

<!-- Cargar jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Cargar DataTables y sus dependencias -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="/haras/vendor/alimentos/historial-alimentos.js" defer></script>
<script src="/haras/vendor/alimentos/listar-alimentos.js" defer></script>
<script src="../../JS/implementoCampo.js"></script>