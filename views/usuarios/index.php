<?php require_once '../../header.php'; ?>

<main>
  <div class="container-fluid px-4">
    <h1 class="mt-4">Registrar Personal</h1>

    <!-- Contenido -->
    <div class="row">
      <div class="col-md-12">
        <form action="" autocomplete="off" id="formulario-personal">
          <div class="card">
            <div class="card-header">Datos del Personal</div>
            <div class="card-body">
              <!-- Fila 1 -->
              <div class="row g-3 mb-3">
                <div class="col-md-2">
                  <div class="input-group">
                    <div class="form-floating">
                      <input 
                        type="text" 
                        class="form-control" 
                        id="dni" 
                        pattern="[0-9]+" 
                        title="Solo se permiten números" 
                        maxlength="8" 
                        required 
                        autofocus>
                      <label for="dni" class="form-label">DNI</label>
                    </div>
                    <button type="button" id="buscar-dni" class="btn btn-sm btn-outline-success">
                      <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="apellidos" maxlength="100" placeholder="Apellidos" required>
                    <label for="apellidos" class="form-label">Apellidos</label>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="nombres" maxlength="100" required>
                    <label for="nombres" class="form-label">Nombres</label>
                  </div>
                </div>
              </div>
              <!-- Fin Fila 1 -->

              <!-- Fila 2 -->
              <div class="row g-3 mb-3">
                <div class="col-md-2">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="tipodoc" value="DNI" required>
                    <label for="tipodoc" class="form-label">Tipo Documento</label>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="numeroHijos" required>
                    <label for="numeroHijos" class="form-label">Número de Hijos</label>
                  </div>
                </div>
                <div class="col-md-8">
                  <div class="form-floating">
                    <input type="text" class="form-control" id="direccion" maxlength="255">
                    <label for="direccion" class="form-label">Dirección</label>
                  </div>
                </div>
              </div>
              <!-- Fin Fila 2 -->
            </div>
            <div class="card-footer text-end">
              <button type="submit" class="btn btn-sm btn-primary">Registrar</button>
              <button type="reset" class="btn btn-sm btn-outline-secondary">Cancelar</button>
              <a href="registrar.php" class="btn btn-sm btn-outline-primary">Mostrar lista</a>
            </div>
          </div> <!-- .card -->
        </form>
      </div> <!-- .col-md-12 -->
    </div> <!-- .row -->
    <!-- Fin contenido -->
  </div> <!-- .container-fluid -->
</main>

<?php require_once '../../footer.php'; ?>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    //Función de referencia GLOBAL
    function $(object = null) {
      return document.querySelector(object);
    }

    //Buscar por DNI
    async function buscarDNI() {
      const dni = $("#dni").value;

      if (dni.length == 8) {
        const response = await fetch(`../../controllers/persona.controller.php?operation=searchByDoc&nrodocumento=${dni}`);
        const data = await response.json();
        if (data.length > 0) {
          $("#apellidos").value = data[0].apellidos;
          $("#nombres").value = data[0].nombres;
          $("#direccion").value = data[0].direccion;
          $("#numeroHijos").value = data[0].numeroHijos;
        } else {
          alert('No se encontraron resultados para este DNI.');
        }
      }
    }

    //Registrar Personal
    async function registrarPersonal() {
      const parametros = new FormData();
      parametros.append("operation", "add");
      parametros.append("nombres", $("#nombres").value);
      parametros.append("apellidos", $("#apellidos").value);
      parametros.append("direccion", $("#direccion").value);
      parametros.append("tipodoc", $("#tipodoc").value);
      parametros.append("nrodocumento", $("#dni").value);
      parametros.append("numeroHijos", $("#numeroHijos").value);

      const response = await fetch(`../../controllers/persona.controller.php`, {
        method: 'POST',
        body: parametros
      });
      const data = await response.json();
      if (data['idPersonal'] > 0) {
        alert('Personal registrado correctamente');
      }
    }

    //Evento de registro de personal
    $("#formulario-personal").addEventListener("submit", async (event) => {
      event.preventDefault();
      await registrarPersonal();
    });

    //Evento de búsqueda por DNI
    $("#buscar-dni").addEventListener("click", buscarDNI);
  });
</script>
