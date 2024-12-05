document.addEventListener('DOMContentLoaded', function () {
    // Función para obtener campos
    async function obtenerCampos() {
        try {
            const response = await fetch('../../controllers/campos.controller.php?operation=getCampos');
            const data = await response.json();
            const camposSelect = document.getElementById('campos');
            if (data.status !== "error") {
                data.forEach(campo => {
                    const option = document.createElement('option');
                    option.value = campo.idCampo;
                    option.textContent = campo.numeroCampo;
                    camposSelect.appendChild(option);
                });
            } else {
                console.error(data.message);
            }
        } catch (error) {
            console.error('Error fetching campos:', error);
        }
    }

    async function obtenerTipoSuelo() {
        try {
            const response = await fetch('../../controllers/campos.controller.php?operation=getTipoSuelo');
            const data = await response.json();

            if (data.status !== "error") {
                document.getElementById('tipoSuelo');
                document.getElementById('tipoSueloEdit').innerHTML = "";

                data.forEach(suelo => {
                    const option = document.createElement('option');
                    option.value = suelo.idTipoSuelo;
                    option.textContent = suelo.nombreTipoSuelo;

                    document.getElementById('tipoSuelo').appendChild(option.cloneNode(true));
                    document.getElementById('tipoSueloEdit').appendChild(option.cloneNode(true));
                });
            } else {
                console.error(data.message);
            }
        } catch (error) {
            console.error('Error fetching tipo suelo:', error);
        }
    }

    // Función para obtener tipos de rotaciones
    async function obtenerTiposRotaciones() {
        try {
            const response = await fetch('../../controllers/campos.controller.php?operation=getTiposRotaciones');
            const data = await response.json();
            const tipoRotacionSelect = document.getElementById('tipoRotacion');
            if (data.status !== "error") {
                data.forEach(tipo => {
                    const option = document.createElement('option');
                    option.value = tipo.idTipoRotacion;
                    option.textContent = tipo.nombreRotacion;
                    tipoRotacionSelect.appendChild(option);
                });
            } else {
                console.error(data.message);
            }
        } catch (error) {
            console.error('Error fetching tipos de rotación:', error);
        }
    }

    // Inicializar DataTable
    function inicializarDataTable() {
        if ($.fn.DataTable.isDataTable('#tabla-campos')) {
            $('#tabla-campos').DataTable().destroy();
        }
        $('#tabla-campos').DataTable({
            ajax: {
                url: '../../controllers/campos.controller.php?operation=getCampos',
                dataSrc: ''
            },
            columns: [
                { data: 'idCampo' },
                { data: 'numeroCampo' },
                { data: 'tamanoCampo' },
                { data: 'nombreTipoSuelo' },
                { data: 'ultimaAccionRealizada' },
                { data: 'fechaUltimaAccion' },
                { data: 'estado' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-warning btn-sm edit-btn" data-id="${row.idCampo}"> Editar</button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="${row.idCampo}"> Eliminar</button>
                        `;
                    }
                }
            ]
        });
    }

    const camposSelect = document.getElementById('campos');
    const ultimaAccionTextarea = document.getElementById('ultimaAccionRealizada');

    camposSelect.addEventListener('change', async function () {
        const idCampoSeleccionado = this.value;

        if (idCampoSeleccionado) {
            try {
                const response = await fetch(`../../controllers/campos.controller.php?operation=getUltimaAccion&idCampo=${idCampoSeleccionado}`);
                const data = await response.json();
                if (data.status !== "error") {
                    ultimaAccionTextarea.value = data.nombreRotacion || "No hay acciones registradas.";
                } else {
                    ultimaAccionTextarea.value = "No hay acciones registradas.";
                }
            } catch (error) {
                ultimaAccionTextarea.value = "Error al obtener la última acción.";
            }
        } else {
            ultimaAccionTextarea.value = "Ultima Acción";
        }
    });

    document.addEventListener('click', function (event) {
        if (event.target.matches('.edit-btn')) {
            const idCampo = event.target.getAttribute('data-id');
            editarCampo(idCampo);
        } else if (event.target.matches('.delete-btn')) {
            const idCampo = event.target.getAttribute('data-id');
            eliminarCampo(idCampo);
        }
    });

    function editarCampo(idCampo) {
        fetch(`../../controllers/campos.controller.php?operation=getCampoID&idCampo=${idCampo}`)
            .then(response => response.json())
            .then(data => {
                if (data.status !== "error") {
                    document.getElementById('numeroCampoEdit').value = data.numeroCampo;
                    document.getElementById('tamanoCampoEdit').value = data.tamanoCampo;
                    document.getElementById('estadoEdit').value = data.estado;
                    document.getElementById('idCampoEdit').value = idCampo;

                    obtenerTipoSuelo().then(() => {
                        document.getElementById('tipoSueloEdit').value = data.idTipoSuelo;
                    });

                    $('#editarCampoModal').modal('show');
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error('Error obteniendo datos del campo:', error));
    }

    // editar campo
    document.getElementById('guardarCampoEditar').addEventListener('click', async function () {
        if (await ask('¿Está seguro de que desea editar este campo?')) {
            const formData = new FormData(document.getElementById('form-editar-campo'));
            formData.append('operation', 'editarCampo');

            try {
                const response = await fetch('../../controllers/campos.controller.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.status !== "error") {
                    console.log("Campo editado exitosamente.");
                    $('#editarCampoModal').modal('hide'); // Ocultar el modal
                    inicializarDataTable(); // Actualizar la tabla de datos
                } else {
                    console.error("Error al editar el campo:", data.message);
                }
            } catch (error) {
                console.error("Error editando el campo:", error);
            }
        } else {
            console.log("El usuario canceló la edición del campo.");
        }
    });

    // Función para eliminar un campo
    async function eliminarCampo(idCampo) {
        if (await ask('¿Está seguro de que desea eliminar este campo?')) {
            const formData = new FormData();
            formData.append('operation', 'eliminarCampo');
            formData.append('idCampo', idCampo);

            try {
                const response = await fetch('../../controllers/campos.controller.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.status !== "error") {
                    inicializarDataTable(); // Actualizar la tabla de datos
                } else {
                    console.error('Error al eliminar el campo:', data.message);
                }
            } catch (error) {
                console.error('Error eliminando el campo:', error);
            }
        } else {
            console.log('El usuario canceló la operación de eliminación.');
        }
    }

    // Registrar nuevo campo
    document.getElementById('guardarCampo').addEventListener('click', async function () {
        const nuevoCampoForm = document.getElementById('form-nuevo-campo');
        const numeroCampo = parseInt(document.getElementById('numeroCampo').value);

        if (numeroCampo < 1) {
            return;
        }

        if (await ask('¿Está seguro de que desea registrar este nuevo campo?')) {
            const formData = new FormData(nuevoCampoForm);
            formData.append('operation', 'registrarCampo');
            try {
                const response = await fetch('../../controllers/campos.controller.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.status !== "error") {
                    $('#registerFieldModal').modal('hide');
                    obtenerCampos(); // Recargar los campos
                    inicializarDataTable(); // Actualizar la tabla de datos
                } else {
                    console.error("Error al registrar el campo:", data.message);
                }
            } catch (error) {
                console.error("Error registrando el campo:", error);
            }
        } else {
            console.log("Operación de registro cancelada por el usuario.");
        }
    });

    // Registrar rotación
    document.getElementById('form-rotacion-campos').addEventListener('submit', async function (event) {
        event.preventDefault();

        // Confirmar la acción con el usuario
        if (await ask('¿Está seguro de que desea registrar esta rotación de campos?')) {
            // Crear los datos del formulario
            const formData = new FormData(this);
            formData.append('operation', 'rotacionCampos');
            try {
                // Enviar la solicitud al servidor
                const response = await fetch('../../controllers/campos.controller.php', {
                    method: 'POST',
                    body: formData
                });
                // Procesar la respuesta del servidor
                const data = await response.json();
                // Manejar la respuesta según el estado
                if (data.status !== "error" && data.idRotacion) {
                    inicializarDataTable(); // Actualizar la tabla de datos
                    this.reset(); // Reiniciar el formulario
                } else {
                    console.error('Error registrando rotación:', data.message || 'Respuesta inesperada');
                }
            } catch (error) {
                console.error("Error en la solicitud:", error.message);
            }
        } else {
            console.log('La operación fue cancelada por el usuario.');
        }
    });

    obtenerCampos();
    obtenerTiposRotaciones();
    obtenerTipoSuelo();
    inicializarDataTable();
});
