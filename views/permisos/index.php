<?php require_once '../../header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase fw-bold" style="color: #0d6efd;">Gestión de Permisos</h1>

    <!-- Seleccionar Rol -->
    <div class="card mb-4 shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-user-shield"></i> Seleccionar Rol</h5>
        </div>
        <div class="card-body p-4 bg-light">
            <form id="form-seleccion-rol" autocomplete="off">
                <div class="form-floating mb-3">
                    <select name="idRol" id="idRol" class="form-select" required>
                        <option value="" disabled selected>Seleccione Rol</option>
                    </select>
                    <label for="idRol"><i class="fas fa-users-cog"></i> Rol</label>
                </div>
            </form>
        </div>
    </div>

    <!-- Gestión de Permisos -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-key"></i> Permisos</h5>
        </div>
        <div class="card-body p-4 bg-light">
            <form id="form-gestionar-permisos">
                <div id="permisos-secciones" class="row">
                    <!-- Aquí se agregarán dinámicamente los permisos -->
                </div>
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-success btn-lg shadow"><i class="fas fa-save"></i> Guardar Permisos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../../footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const idRolSelect = document.querySelector("#idRol");
    const permisosSecciones = document.querySelector("#permisos-secciones");
    const formPermisos = document.querySelector("#form-gestionar-permisos");

    // Función para cargar roles desde el servidor
    async function loadRoles() {
        try {
            const response = await fetch('../../controllers/permisos.controller.php?operation=listarRoles');
            const data = await response.json();

            idRolSelect.innerHTML = '<option value="" disabled selected>Seleccione Rol</option>';
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(rol => {
                    const option = document.createElement('option');
                    option.value = rol.idRol;
                    option.textContent = rol.nombreRol;
                    idRolSelect.appendChild(option);
                });
            } else {
                console.error('No se encontraron roles o el formato de datos es incorrecto.');
            }
        } catch (error) {
            console.error('Error al cargar los roles:', error);
        }
    }

    // Función para cargar permisos y sus switches
    async function loadPermisos(idRol) {
        try {
            // Obtener la lista de permisos
            const response = await fetch('../../controllers/permisos.controller.php?operation=listarPermisos');
            const permisos = await response.json();

            // Obtener permisos actuales del rol
            const responseRolPermisos = await fetch(`../../controllers/permisos.controller.php?operation=obtenerPermisosRol&idRol=${idRol}`);
            const permisosRol = await responseRolPermisos.json();

            permisosSecciones.innerHTML = ''; // Limpiar las secciones

            // Categorías de permisos (adaptables según tus necesidades)
            const categorias = {
                "Gestión de Alimentos": ["GESTIONAR_ALIMENTOS"],
                "Gestión de Registro": ["REGISTRAR_PERSONAL", "REGISTRAR_USUARIOS"],
                "Servicios": ["REGISTRAR_SERVICIO_PROPIO", "REGISTRAR_SERVICIO_MIXTO"],
                "Medicamentos": ["REGISTRAR_MEDICAMENTOS", "REGISTRAR_HISTORIAL_MEDICO"],
                "Entrenamientos": ["REGISTRAR_ENTRENAMIENTO"],
                "Categorías": ["GESTIONAR_CATEGORIAS"],
                "Ventas/Servicios": ["VER_VENTAS_SERVICIOS"],
                "Gestión de Equinos": ["REGISTRAR_EQUINO"],
                "Gestión de Haras": ["LISTAR_HARAS"],
                "Rotación de Campos": ["REGISTRAR_ROTACION_CAMPOS"],
                "Asistencia del Personal": ["REGISTRAR_ASISTENCIA_PERSONAL"]
            };

            // Generar switches para cada permiso en su categoría
            for (const [categoria, permisosArr] of Object.entries(categorias)) {
                const section = document.createElement('div');
                section.classList.add('col-12', 'mb-4');
                section.innerHTML = `
                    <div class="p-3 border rounded bg-white shadow-sm mb-3">
                        <h5 class="text-uppercase" style="font-weight: bold; color: #0d6efd;">${categoria}</h5>
                    </div>
                `;

                permisosArr.forEach(nombrePermiso => {
                    const permiso = permisos.find(p => p.nombrePermiso === nombrePermiso);
                    if (permiso) {
                        const isChecked = permisosRol.includes(permiso.idPermiso.toString()) ? 'checked' : '';
                        const permisoSwitch = `
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="permisos[]" value="${permiso.idPermiso}" id="permiso${permiso.idPermiso}" ${isChecked}>
                                <label class="form-check-label" for="permiso${permiso.idPermiso}" style="font-size: 1.1rem;">
                                    ${permiso.descripcion}
                                </label>
                            </div>
                        `;
                        section.insertAdjacentHTML('beforeend', permisoSwitch);
                    }
                });

                permisosSecciones.appendChild(section);
            }
        } catch (error) {
            console.error('Error al cargar los permisos:', error);
        }
    }

    // Cargar roles al iniciar la página
    loadRoles();

    // Cargar permisos cuando se selecciona un rol
    idRolSelect.addEventListener('change', (event) => {
        const selectedRol = event.target.value;
        if (selectedRol) {
            loadPermisos(selectedRol);
        } else {
            permisosSecciones.innerHTML = ''; // Limpiar si no hay rol seleccionado
        }
    });

    // Manejar la acción de guardar permisos
    formPermisos.addEventListener("submit", async (event) => {
        event.preventDefault();

        const formData = new FormData(formPermisos);
        const permisosSeleccionados = [];
        formData.forEach((value, key) => {
            if (key === 'permisos[]') {
                permisosSeleccionados.push(value);
            }
        });

        try {
            const response = await fetch('../../controllers/permisos.controller.php?operation=asignarPermisos', {
                method: "POST",
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    operation: 'asignarPermisos',
                    idRol: idRolSelect.value,
                    permisos: permisosSeleccionados
                })
            });

            const result = await response.json();
            alert(result.status === 'success' ? 'Permisos actualizados correctamente.' : result.message);
        } catch (error) {
            alert('Hubo un problema al asignar los permisos.');
            console.error('Error en la solicitud:', error);
        }
    });
});
</script>
