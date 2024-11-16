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

            if (!response.ok) throw new Error('Error en la respuesta del servidor');

            const data = await response.json();
            console.log(data);

            let numeroFila = 1;
            data.forEach(personal => {
                const correoUsuario = personal.correo || '---';
                const tieneUsuario = personal.tieneUsuario == 1 ? '✔️' : `<button data-id="${personal.idPersonal}" class="btn btn-sm btn-outline-primary register-user" data-bs-toggle="modal" data-bs-target="#modalRegistrarUsuario">Registrar Usuario</button>`;
                const cambiarEstadoBtn = `<button class="btn btn-sm btn-outline-success cambiar-estado" data-idusuario="${personal.idUsuario}" data-estado="${personal.estado}" data-correo="${personal.correo}">Cambiar Estado</button>`;

                const nuevaFila = `
                    <tr>
                        <td>${numeroFila}</td>
                        <td>${personal.tipodoc}</td>
                        <td>${personal.nrodocumento}</td>
                        <td>${personal.apellidos}</td>
                        <td>${personal.nombres}</td>
                        <td>${personal.direccion}</td>
                        <td class="text-center">${tieneUsuario}</td>
                        <td>${correoUsuario}</td>
                        <td>${cambiarEstadoBtn}</td>
                        <td style="display:none;" class="idUsuario">${personal.idUsuario}</td>
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

            document.querySelectorAll(".cambiar-estado").forEach(button => {
                button.addEventListener("click", async (event) => {
                    const idUsuario = event.currentTarget.getAttribute("data-idusuario");
                    const correoUsuario = event.currentTarget.getAttribute("data-correo");
                    const estadoActual = event.currentTarget.getAttribute("data-estado");
                    const nuevoEstado = estadoActual === '1' ? '0' : '1';

                    if (await ask(`¿Deseas cambiar el estado del usuario ${correoUsuario}?`)) {
                        try {
                            const respuesta = await fetch("../../controllers/Persona.controller.php", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: new URLSearchParams({
                                    operation: 'modificarestadousuario',
                                    idUsuario: idUsuario,
                                }),
                            });

                            const resultado = await respuesta.json();

                            if (resultado.status === 'success') {
                                console.log('Estado cambiado correctamente');
                                showToast(resultado.mensaje, 'SUCCESS');
                                obtenerPersonal();
                            } else {
                                console.error('Error:', resultado.mensaje);
                                showToast(resultado.mensaje, 'ERROR');
                            }

                        } catch (error) {
                            console.error('Error al cambiar el estado:', error);
                            showToast('Hubo un problema con la conexión', 'ERROR');
                        }
                    }
                });
            });
        } catch (error) {
            console.error("Error al obtener personal:", error);
            showToast('Hubo un problema al obtener los datos del personal', 'ERROR');
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

        const confirmarRegistro = await ask("¿Deseas registrar este usuario?");

        if (!confirmarRegistro) {
            showToast("Registro cancelado", "ERROR");
            return;
        }

        const parametros = new FormData();
        parametros.append("operation", "add");
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
                    showToast('Usuario registrado exitosamente', 'SUCCESS');
                    $('#modalRegistrarUsuario').modal('hide');
                    await obtenerPersonal();
                } else {
                    showToast('Error al registrar usuario', 'ERROR');
                }
            } else {
                showToast('Error en la respuesta del servidor', 'ERROR');
            }
        } catch (error) {
            console.error("Error al registrar usuario:", error);
            showToast('Hubo un problema al registrar el usuario', 'ERROR');
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
