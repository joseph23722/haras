<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Usuarios</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Personas con acceso al sistema</li>
    </ol>
    <div class="card mb-4">
        <div class="card-header">
            Complete los datos
        </div>
        <div class="card-body">
            <form action="" id="form-registro-usuarios" autocomplete="off">
                <div class="row g-2">
                    <div class="col-md mb-2">
                        <div class="form-floating">
                            <select name="tipodoc" id="tipodoc" class="form-select">
                                <option value="DNI">DNI</option>
                                <option value="Pasaporte">Pasaporte</option>
                            </select>
                            <label for="tipodoc">Tipo de Documento</label>
                        </div>
                    </div>

                    <div class="col-md mb-2">
                        <div class="input-group">
                            <div class="form-floating">
                                <input type="text" id="nrodocumento" class="form-control" autofocus maxlength="8" minlength="8" title="Solo números" required>
                                <label for="nrodocumento">Nro Documento</label>
                            </div>
                            <button class="input-group-text" type="button" id="buscar-nrodocumento">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </div>         
                    </div>

                    <div class="col-md mb-2">
                        <div class="form-floating">
                            <input type="text" id="direccion" class="form-control" required>
                            <label for="direccion">Dirección</label>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-mb mb-2">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="apellidos" required>
                            <label for="apellidos">Apellidos</label>
                        </div>
                    </div>

                    <div class="col-mb mb-2">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="nombres" required>
                            <label for="nombres">Nombres</label>
                        </div>
                    </div>

                    <div class="col-md mb-2">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="numeroHijos" required>
                            <label for="numeroHijos">Número de Hijos</label>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row g-2">
                    <div class="col-md mb-3">
                        <div class="form-floating">
                            <input type="email" id="correo" maxlength="150" class="form-control" required>
                            <label for="correo">Correo Electrónico</label>
                        </div>
                    </div>

                    <div class="col-md mb-3">
                        <div class="form-floating">
                            <input type="password" id="clave" maxlength="20" minlength="8" class="form-control" required>
                            <label for="clave">Contraseña</label>
                        </div>
                    </div>

                    <div class="col-md mb-3">
                        <div class="form-floating">
                            <select name="rol" id="rol" class="form-select" required>
                                <option value="">Seleccione</option>
                            </select>
                            <label for="rol">Nivel de acceso</label>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary btn-sm" id="registrar-usuario">Registrar usuario</button>
                    <button type="reset" class="btn btn-secondary btn-sm">Cancelar proceso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const nrodocumento = document.querySelector("#nrodocumento");
    const rolSelect = document.querySelector("#rol");
    let datosNuevos = true;

    // Cargar roles dinámicamente
    async function loadRoles() {
        try {
            const response = await fetch('../../controllers/permisos.controller.php?operation=listarRoles');
            const roles = await response.json();

            roles.forEach(rol => {
                const option = document.createElement('option');
                option.value = rol.idRol;
                option.textContent = rol.nombreRol;
                rolSelect.appendChild(option);
            });
        } catch (error) {
            console.error('Error al cargar roles:', error);
        }
    }

    // Registrar persona y usuario combinados
    async function registrarPersonaUsuario() {
        const params = new FormData();
        params.append("operation", "register");
        params.append("apellidos", document.querySelector("#apellidos").value);
        params.append("nombres", document.querySelector("#nombres").value);
        params.append("nrodocumento", nrodocumento.value);
        params.append("direccion", document.querySelector("#direccion").value);
        params.append("tipodoc", document.querySelector("#tipodoc").value);
        params.append("numeroHijos", document.querySelector("#numeroHijos").value);
        params.append("correo", document.querySelector("#correo").value);
        params.append("clave", document.querySelector("#clave").value);
        params.append("idRol", rolSelect.value);

        const options = {
            method: "POST",
            body: params
        };

        const response = await fetch('../../controllers/persona.controller.php', options);
        return response.json();
    }

    // Buscar persona por DNI
    async function buscarDocumento() {
        const params = new URLSearchParams();
        params.append("operation", "searchByDoc");
        params.append("nrodocumento", nrodocumento.value);

        try {
            const response = await fetch('../../controllers/persona.controller.php', {
                method: 'POST',
                body: params
            });

            if (!response.ok) {
                throw new Error("Error en la respuesta del servidor.");
            }

            const result = await response.json();
            return result;
        } catch (error) {
            console.error("Error al buscar documento:", error);
            return [];
        }
    }

    // Validar y rellenar el formulario con los datos de la persona
    function validarDocumento(response) {
        if (response.length === 0) {
            // No se encontró la persona
            document.querySelector("#apellidos").value = "";
            document.querySelector("#nombres").value = "";
            document.querySelector("#direccion").value = "";
            document.querySelector("#numeroHijos").value = "";
            document.querySelector("#correo").value = "";
            document.querySelector("#clave").value = "";
            adPersona(true);
            datosNuevos = true;
        } else {
            // Se encontró la persona
            datosNuevos = false;
            const persona = response[0];
            document.querySelector("#apellidos").value = persona.apellidos;
            document.querySelector("#nombres").value = persona.nombres;
            document.querySelector("#direccion").value = persona.direccion;
            document.querySelector("#numeroHijos").value = persona.numeroHijos;
            document.querySelector("#correo").value = persona.correo || "";
            adPersona(false);
        }
    }

    // Habilitar o deshabilitar los campos de persona
    function adPersona(sw = false) {
        document.querySelector("#apellidos").disabled = !sw;
        document.querySelector("#nombres").disabled = !sw;
        document.querySelector("#direccion").disabled = !sw;
        document.querySelector("#numeroHijos").disabled = !sw;
    }

    // Evento para buscar por DNI
    document.querySelector("#buscar-nrodocumento").addEventListener("click", async () => {
        const response = await buscarDocumento();
        validarDocumento(response);
    });

    // Evento para registrar usuario
    document.querySelector("#form-registro-usuarios").addEventListener("submit", async (event) => {
        event.preventDefault();

        if (confirm("¿Está seguro de proceder?")) {
            const response = await registrarPersonaUsuario();
            if (response.idPersonal === -1) {
                alert("No se pudo registrar el usuario, revise los datos.");
            } else {
                document.querySelector("#form-registro-usuarios").reset();
                alert("Registro exitoso.");
            }
        }
    });

    // Cargar roles al inicio
    loadRoles();
});

</script>
