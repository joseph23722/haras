<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Registro de Historial Médico</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Historial Médico</h5>
        </div>

        <!-- Formulario -->
        <div class="card-body p-4" style="background-color: #f9f9f9;">
            <form action="" id="form-historial-medico" autocomplete="off">
                <div class="row g-3">
                    <!-- Selector para el Tipo de Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="tipoEquinoSelect" class="form-select" name="tipoEquino" required>
                                <option value="">Seleccione Tipo de Equino</option>
                                <option value="1">Yegua</option>
                                <option value="2">Padrillo</option>
                                <option value="3">Potranca</option>
                                <option value="4">Potrillo</option>
                            </select>
                            <label for="tipoEquinoSelect"><i class="fas fa-horse" style="color: #00b4d8;"></i> Tipo de Equino</label>
                        </div>
                    </div>

                    <!-- Selector para el nombre del Tipo de Equino -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="equinoSelect" class="form-select" name="idEquino" required>
                                <option value="">Seleccione Equino</option>
                            </select>
                            <label for="equinoSelect"><i class="fas fa-horse" style="color: #00b4d8;"></i> Equino</label>
                        </div>
                    </div>
                    <!-- Peso Equino (opcional) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input name="pesokg" id="pesokg" class="form-control" readonly disabled>
                            <label for="pesokg"><i class="fas fa-weight" style="color: #ff6f61;"></i> Peso Equino (kg)</label>
                        </div>
                    </div>

                    <!-- Select de Medicamentos -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select id="selectMedicamento" class="form-select" name="idMedicamento" required>
                                <option value="">Seleccione Medicamento</option>
                            </select>
                            <label for="selectMedicamento"><i class="fas fa-pills" style="color: #ffa500;"></i> Medicamento</label>
                        </div>
                    </div>

                    <!-- Tipo de Tratamiento (Primario o Complementario) -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="tipoTratamiento" id="tipoTratamiento" class="form-select" required>
                                <option value="">Seleccione Tipo de Tratamiento</option>
                                <option value="Primario">Primario</option>
                                <option value="Complementario">Complementario</option>
                            </select>
                            <label for="tipoTratamiento"><i class="fas fa-list-alt" style="color: #ff8c00;"></i> Tipo de Tratamiento</label>
                        </div>
                    </div>

                    <!-- Fecha Fin -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="fechaFin" id="fechaFin" class="form-control" required>
                            <label for="fechaFin"><i class="fas fa-calendar-alt" style="color: #ba55d3;"></i> Fecha de Fin</label>
                        </div>
                    </div>

                    <!-- Dosis -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="dosis" id="dosis" placeholder="" class="form-control" required>
                            <label for="dosis"><i class="fas fa-syringe" style="color: #ff6347;"></i> Dosis</label>
                        </div>
                    </div>

                    <!-- Frecuencia de Administración -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="frecuenciaAdministracion" id="frecuenciaAdministracion" class="form-control" required>
                            <label for="frecuenciaAdministracion"><i class="fas fa-stopwatch" style="color: #6a5acd;"></i> Frecuencia de Administración</label>
                        </div>
                    </div>

                    <!-- Vía de Administración -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="viaAdministracion" id="viaAdministracion" class="form-select" required>
                                <option value="">Seleccione Vía de Administración</option>
                                <option value="Oral">Oral - Administración por la boca</option>
                                <option value="Intramuscular">Intramuscular - Inyección en el músculo</option>
                                <option value="Intravenosa">Intravenosa - Inyección en la vena</option>
                                <option value="Subcutánea">Subcutánea - Inyección debajo de la piel</option>
                                <option value="Intranasal">Intranasal - Administración en las fosas nasales</option>
                                <option value="Tópica">Tópica - Aplicación en la piel o mucosa</option>
                            </select>
                            <label for="viaAdministracion"><i class="fas fa-route" style="color: #00b4d8;"></i> Vía de Administración</label>
                        </div>
                    </div>


                    <!-- Observaciones -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <textarea name="observaciones" id="observaciones" class="form-control" style="height: 100px;"></textarea>
                            <label for="observaciones"><i class="fas fa-notes-medical" style="color: #007bff;"></i> Observaciones</label>
                        </div>
                    </div>

                    <!-- Reacciones Adversas (opcional) -->
                    <div class="col-md-12">
                        <div class="form-floating">
                            <textarea name="reaccionesAdversas" id="reaccionesAdversas" class="form-control" style="height: 100px;"></textarea>
                            <label for="reaccionesAdversas"><i class="fas fa-exclamation-circle" style="color: #dc3545;"></i> Reacciones Adversas</label>
                        </div>
                    </div>

                    <div id="mensaje" style="margin-top: 10px; color: green; font-weight: bold;"></div>

                    <!-- Botones -->
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="registrar-historial" style="background-color: #0077b6; border: none;">
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
    <!-- Tabla para DataTable -->
    <div class="card mt-4">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Historiales Médicos</h5>
        </div>
        <div class="card-body">
            <table id="historialTable" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Equino</th>
                        <th>Peso (kg)</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Medicamento</th>
                        <th>Dosis</th>
                        <th>Frecuencia</th>
                        <th>Vía</th>
                        <th>Registro</th>
                        <th>Fin</th>
                        <th>Observaciones</th>
                        <th>Reacciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Se llenará dinámicamente -->
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once '../footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Incluye SweetAlert -->
<script src="../../swalcustom.js"></script>

<script src="../../JS/historialmedico.js"></script>