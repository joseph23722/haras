document.addEventListener("DOMContentLoaded", () => {
    const table = $('#listadobasico').DataTable({
    });
    async function loadRevisiones() {
        try {
            const response = await fetch('../../controllers/revisionbasica.controller.php?operation=listarRevisionBasica', {
                method: 'GET',
            });

            const data = await response.json();

            if (Array.isArray(data) && data.length > 0) {
                table.clear();

                data.forEach(({
                    idRevision,
                    nombreEquino,
                    nombreHaras,
                    tiporevision,
                    fecharevision,
                    observaciones,
                    costorevision
                }) => {
                    table.row.add([
                        idRevision,
                        nombreEquino,
                        nombreHaras || 'Haras Rancho Sur',
                        tiporevision,
                        fecharevision,
                        observaciones,
                        costorevision || 'N/A'
                    ]).draw();
                });
            } else {
                console.log('No se encontraron revisiones disponibles.');
            }
        } catch (error) {
            console.error('Error al cargar las revisiones:', error);
        }
    }
    loadRevisiones();
});