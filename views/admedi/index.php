<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <!-- Título principal -->
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #ff6347;">Registro de Medicamentos</h1>

    <div class="card mb-4 shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0 text-uppercase">Datos del Medicamento</h5>
        </div>

        <!-- Formulario -->
        <div class="card-body p-4 bg-light rounded">
            <form action="" id="form-medicamento" autocomplete="off">
                <div class="row g-3">
                    <!-- Nombre del Medicamento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" name="nombreMedicamento" id="nombreMedicamento" class="form-control" required>
                            <label for="nombreMedicamento"><i class="fas fa-capsules" style="color: #ff7f50;"></i> Nombre del Medicamento</label>
                        </div>
                    </div>

                    <!-- Cantidad -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" name="cantidad" id="cantidad" class="form-control" required>
                            <label for="cantidad"><i class="fas fa-balance-scale" style="color: #32cd32;"></i> Cantidad</label>
                        </div>
                    </div>

                    <!-- Fecha de Caducidad -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="date" name="caducidad" id="caducidad" class="form-control" required>
                            <label for="caducidad"><i class="fas fa-calendar-alt" style="color: #ba55d3;"></i> Fecha de Caducidad</label>
                        </div>
                    </div>

                    <!-- Precio Unitario -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="number" step="0.01" name="precioUnitario" id="precioUnitario" class="form-control" required>
                            <label for="precioUnitario"><i class="fas fa-dollar-sign" style="color: #1e90ff;"></i> Precio Unitario</label>
                        </div>
                    </div>

                    <!-- Tipo de Movimiento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idTipomovimiento" id="idTipomovimiento" class="form-select" required>
                                <option value="">Seleccione Movimiento</option>
                            </select>
                            <label for="idTipomovimiento"><i class="fas fa-exchange-alt" style="color: #ffa500;"></i> Tipo de Movimiento</label>
                        </div>
                    </div>

                    <!-- Usuario -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select name="idUsuario" id="idUsuario" class="form-select" required>
                                <option value="">Seleccione Usuario</option>
                            </select>
                            <label for="idUsuario"><i class="fas fa-user" style="color: #ff6347;"></i> Usuario</label>
                        </div>
                    </div>

                    <!-- Visita -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea name="visita" id="visita" class="form-control" style="height: 90px;"></textarea>
                            <label for="visita"><i class="fas fa-clipboard-list" style="color: #6a5acd;"></i> Visita</label>
                        </div>
                    </div>

                    <!-- Tratamiento -->
                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea name="tratamiento" id="tratamiento" class="form-control" style="height: 90px;" required></textarea>
                            <label for="tratamiento"><i class="fas fa-pills" style="color: #32cd32;"></i> Tratamiento</label>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="col-md-12 text-end mt-3">
                        <button type="submit" class="btn btn-primary btn-lg shadow-lg" id="registrar-medicamento"><i class="fas fa-save"></i> Registrar Medicamento</button>
                        <button type="reset" class="btn btn-secondary btn-lg shadow-sm"><i class="fas fa-times"></i> Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const formMedicamento = document.querySelector("#form-medicamento");

    // Cargar listas de movimientos y usuarios (ejemplo)
    // Implementa aquí la lógica para cargar los datos de los selectores

    // Manejar el envío del formulario
    formMedicamento.addEventListener("submit", async (event) => {
        event.preventDefault();

        const formData = new FormData(formMedicamento);
        const data = {};

        formData.forEach((value, key) => { data[key] = value; });

        try {
            const response = await fetch('../../controllers/admedi.controller.php', {
                method: "POST",
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ operation: 'add', ...data })
            });

            const result = await response.json();
            alert(result.message);

            if (result.status === 'success') {
                formMedicamento.reset();
            }
        } catch (error) {
            alert('Hubo un problema al registrar el medicamento.');
            console.error('Error en la solicitud:', error);
        }
    });
});
</script>
