document.addEventListener("DOMContentLoaded", () => {

    function inicializarDataTable() {
        if ($.fn.DataTable.isDataTable('#tabla-personal')) {
            $('#tabla-personal').DataTable().destroy();
        }

        $('#tabla-personal').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            pageLength: 10
        });
    }

    async function obtenerPersonal() {
        try {
            $("#tabla-personal tbody").empty();

            const response = await fetch(`../../controllers/Persona.controller.php?operation=getAll&nocache=${new Date().getTime()}`, {
                method: 'GET',
                cache: 'no-cache'
            });

            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }

            const data = await response.json();
            console.log(data);

            let numeroFila = 1;
            data.forEach(personal => {
                const tieneUsuario = personal.tieneUsuario == 1 ? '✔️' : `<button data-id="${personal.idPersonal}" class="btn btn-sm btn-outline-primary register-user" data-bs-toggle="modal" data-bs-target="#modalRegistrarUsuario">Registrar Usuario</button>`;

                const nuevaFila = `
                    <tr>
                        <td>${numeroFila}</td>
                        <td>${personal.tipodoc}</td>
                        <td>${personal.nrodocumento}</td>
                        <td>${personal.apellidos}</td>
                        <td>${personal.nombres}</td>
                        <td>${personal.direccion}</td>
                        <td class="text-center">${tieneUsuario}</td>
                    </tr>
                `;

                $("#tabla-personal tbody").append(nuevaFila);
                numeroFila++;
            });

            inicializarDataTable(); 

            document.querySelectorAll(".register-user").forEach(button => {
                button.addEventListener("click", (event) => {
                    const idPersonal = event.currentTarget.getAttribute("data-id");
                    $("#idPersonal").val(idPersonal);
                });
            });

        } catch (error) {
            console.error("Error al obtener personal:", error);
        }
    }

    // Registrar Personal
    async function registrarPersonal() {
        const parametros = new FormData();
        parametros.append("operation", "add");
        parametros.append("nombres", $("#nombres").val());
        parametros.append("apellidos", $("#apellidos").val());
        parametros.append("direccion", $("#direccion").val());
        parametros.append("tipodoc", $("#tipodoc").val());
        parametros.append("nrodocumento", $("#dni").val());
        parametros.append("numeroHijos", $("#numeroHijos").val());
        parametros.append("fechaIngreso", $("#fechaIngreso").val());
        parametros.append("tipoContrato", $("#tipoContrato").val());

        try {
            const response = await fetch(`../../controllers/persona.controller.php`, {
                method: 'POST',
                body: parametros
            });

            if (response.ok) {
                const data = await response.json();
                console.log(data); 
                if (data['idPersonal'] > 0) {
                    alert('Personal registrado exitosamente');
                    $("#formulario-personal")[0].reset();
                } else {
                    alert('Error al registrar personal');
                }
            } else {
                alert('Error en la respuesta del servidor');
            }
        } catch (error) {
            console.error('Error en la solicitud:', error);
            alert('Hubo un error en la solicitud');
        }
    }

    // Registrar Usuario
    async function registrarUsuario(event) {
        event.preventDefault();
        const parametros = new FormData();
        parametros.append("operation", "registerUser");
        parametros.append("idPersonal", $("#idPersonal").val());
        parametros.append("correo", $("#correo").val());
        parametros.append("clave", $("#clave").val());
        parametros.append("idRol", $("#idRol").val());

        try {
            const response = await fetch(`../../controllers/usuario.controller.php`, {
                method: 'POST',
                body: parametros
            });

            if (response.ok) {
                const data = await response.json();
                if (data.status) {
                    alert('Usuario registrado exitosamente');
                    $('#modalRegistrarUsuario').modal('hide');
                    await obtenerPersonal(); // Recargar la lista
                } else {
                    alert('Error al registrar usuario');
                }
            } else {
                alert('Error en la respuesta del servidor');
            }
        } catch (error) {
            console.error("Error al registrar usuario:", error);
        }
    }

    // Listeners
    document.querySelector("#formulario-personal").addEventListener("submit", (event) => {
        event.preventDefault();
        registrarPersonal();
    });

    document.querySelector("#formulario-usuario").addEventListener("submit", registrarUsuario);

    // Carga inicial
    obtenerPersonal();
});
