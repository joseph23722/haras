<?php require_once '../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Registro de Historial Médico</h1>

    <div class="card mb-4 shadow border-0">
        <div class="card-header" style="background: linear-gradient(to right, #a0ffb8, #a0ffb8); color: #003366;">
            <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Historial Médico</h5>
            <!-- Botón para abrir el modal de agregar vías de administración -->
            <button type="button" class="btn btn-success btn-sm"
                style="background-color: #28a745; border: none; position: absolute; right: 2px; top: 2px; padding: 10px 15px; font-size: 1.2em;"
                id="btnAgregarVia"
                data-bs-toggle="modal"
                data-bs-target="#modalAgregarViaAdministracion">
                <i class="fas fa-plus"></i>
            </button>

        </div>

        <div class="card-body p-1" style="background-color: #f9f9f9;">
            <div class="col-md-6">
                <div class="form-floating">
                    <select id="tipodiagnostico" class="form-select" name="tipodiagnostico">
                        <option value="">Seleccione Tipo de Diagnóstico</option>
                    </select>
                    <label for="tipodiagnostico"><i class="fas fa-warning" style="color: #00b4d8;"></i> Tipo de Diagnóstico</label>
                </div>
            </div>
        </div>