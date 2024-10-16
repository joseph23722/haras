<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
  <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #0056b3;">Registro de Rotación de Campos</h1>

  <div class="card mb-4 shadow border-0">
    <div class="card-header" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff); color: #003366;">
      <h5 class="mb-0 text-uppercase" style="font-weight: bold;">Datos del Campo</h5>
    </div>

    <div class="card-body p-4" style="background-color: #f9f9f9;">
      <form action="" id="form-registro-equino" autocomplete="off">
        <div class="row g-3">
          <div class="col-md-4">
            <div class="form-floating">
              <select name="campos" id="campos" class="form-select">
                <option value="">Seleccione un campo</option>
              </select>
              <label for="campos"><i class="fas fa-home" style="color: #ffa500;"></i> Nro de potreros</label>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-floating">
              <textarea name="ultimaAccionRealizada" id="ultimaAccionRealizada" class="form-control" style="height: 50px;" readonly>Ultima Acción</textarea>
              <label for="ultimaAccionRealizada"><i class="fas fa-info-circle" style="color: #1e90ff;"></i> Ultima Acción</label>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-floating">
              <select name="tipoRotacion" id="tipoRotacion" class="form-select">
                <option value="">Seleccione un tipo de Rotación</option>
              </select>
              <label for="tipoRotacion"><i class="fa-solid fa-arrow-rotate-left" style="color: #ffa500;"></i> Tipo de Rotación</label>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-floating">
              <input type="date" name="fechaRotacion" id="fechaRotacion" class="form-control">
              <label for="fechaRotacion"><i class="fas fa-calendar-alt" style="color: #32cd32;"></i> Fecha de Rotación</label>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-floating">
              <textarea name="detalles" id="detalles" class="form-control" style="height: 50px;"></textarea>
              <label for="detalles"><i class="fas fa-info-circle" style="color: #1e90ff;"></i> Detalles</label>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-floating">
              <input type="file" name="fotografia" id="fotografia" class="form-control" accept="image/*">
              <label for="fotografia"><i class="fas fa-camera" style="color: #007bff;"></i> Fotografía del campo</label>
            </div>
          </div>

          <div class="col-md-12 text-end mt-3">
            <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="registrar-equino" style="background-color: #0077b6; border: none;">
              <i class="fas fa-save"></i> Registrar Rotación
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

<?php require_once '../../footer.php'; ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Cargar campos
    fetch('../../controllers/campos.controller.php?operation=getCampos')
      .then(response => response.json())
      .then(data => {
        const camposSelect = document.getElementById('campos');
        if (data.status !== "error") {
          data.forEach(campo => {
            const option = document.createElement('option');
            option.value = campo.idCampo; // Cambia 'idCampo' según tu estructura de datos
            option.textContent = campo.numeroCampo; // Cambia 'numeroCampo' según tu estructura de datos
            camposSelect.appendChild(option);
          });
        } else {
          console.error(data.message);
        }
      })
      .catch(error => console.error('Error fetching campos:', error));

    // Cargar tipos de rotación
    fetch('../../controllers/campos.controller.php?operation=getTiposRotaciones')
      .then(response => response.json())
      .then(data => {
        const tipoRotacionSelect = document.getElementById('tipoRotacion');
        if (data.status !== "error") {
          data.forEach(tipo => {
            const option = document.createElement('option');
            option.value = tipo.idTipoRotacion; // Cambia 'id' según tu estructura de datos
            option.textContent = tipo.nombreRotacion; // Cambia 'nombre' según tu estructura de datos
            tipoRotacionSelect.appendChild(option);
          });
        } else {
          console.error(data.message);
        }
      })
      .catch(error => console.error('Error fetching tipos de rotación:', error));
  });
</script>