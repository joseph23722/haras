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
                            <button class="btn btn-warning btn-sm edit-btn" data-id="${row.idCampo}"><i class="fas fa-edit"></i> Editar</button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="${row.idCampo}"><i class="fas fa-trash-alt"></i> Eliminar</button>
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
                    console.error(data.message);
                    ultimaAccionTextarea.value = "No hay acciones registradas.";
                }
            } catch (error) {
                console.error('Error fetching ultima accion:', error);
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
                console.log(data);
                if (data.status !== "error") {
                    document.getElementById('numeroCampoEdit').value = data.numeroCampo;
                    document.getElementById('tamanoCampoEdit').value = data.tamanoCampo;
                    document.getElementById('estadoEdit').value = data.estado;
                    document.getElementById('idCampoEdit').value = idCampo;

                    obtenerTipoSuelo().then(() => {
                        document.getElementById('tipoSueloEdit').value = data.idTipoSuelo;
                        console.log("ID de tipo de suelo seleccionado:", data.idTipoSuelo);
                    });

                    $('#editarCampoModal').modal('show');
                } else {
                    console.error(data.message);
                }
            })
            .catch(error => console.error('Error obteniendo datos del campo:', error));
    }

    document.getElementById('guardarCampoEditar').addEventListener('click', function () {
        const formData = new FormData(document.getElementById('form-editar-campo'));
        formData.append('operation', 'editarCampo');

        fetch('../../controllers/campos.controller.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status !== "error") {
                    alert("Campo editado exitosamente.");
                    $('#editarCampoModal').modal('hide');
                    inicializarDataTable();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error editando campo:', error));
    });

    // Función para eliminar un campo
    function eliminarCampo(idCampo) {
        if (confirm('¿Estás seguro de que deseas eliminar este campo?')) {
            const formData = new FormData();
            formData.append('operation', 'eliminarCampo');
            formData.append('idCampo', idCampo);

            fetch('../../controllers/campos.controller.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status !== "error") {
                        alert('Campo eliminado exitosamente.');
                        inicializarDataTable();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error eliminando campo:', error));
        }
    }

    // Registrar nuevo campo
    document.getElementById('guardarCampo').addEventListener('click', function () {
        const nuevoCampoForm = document.getElementById('form-nuevo-campo');
        const numeroCampo = parseInt(document.getElementById('numeroCampo').value);

        if (numeroCampo < 1) {
            alert("El número del campo debe ser mayor que 0.");
            return;
        }

        const formData = new FormData(nuevoCampoForm);
        formData.append('operation', 'registrarCampo');

        fetch('../../controllers/campos.controller.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status !== "error") {
                    $('#registerFieldModal').modal('hide');
                    alert("Campo registrado exitosamente.");
                    obtenerCampos();
                    inicializarDataTable();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error registrando campo:', error));
    });

    // Registrar rotación
    document.getElementById('form-rotacion-campos').addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(this);
        formData.append('operation', 'rotacionCampos');

        fetch('../../controllers/campos.controller.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.status !== "error" && data.idRotacion) {
                    alert('Rotación registrada con éxito. ID Rotación: ' + data.idRotacion);
                    inicializarDataTable();
                    this.reset();
                } else {
                    alert('Error registrando rotación: ' + (data.message || 'Respuesta inesperada'));
                }
            })
            .catch(error => console.error('Error registrando rotación:', error));
    });

    obtenerCampos();
    obtenerTiposRotaciones();
    obtenerTipoSuelo();
    inicializarDataTable();
});
