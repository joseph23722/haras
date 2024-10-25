<?php require_once '../header.php'; ?>

<!-- Librerías necesarias de FullCalendar -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js'></script>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #003300;">Programación de Rotación de Campos</h1>

    <div class="card">
        <div class="card-header bg-success text-white">
            <h3 class="card-title">Calendario</h3>
        </div>
        <div class="card-body">
            <div id="calendar" class="table-responsive"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch('../../controllers/campos.controller.php?operation=getRotaciones')
                    .then(response => response.json())
                    .then(data => {
                        console.log(data)
                        if (data.status !== "error") {
                            const eventos = data.map(evento => {
                                const fechaRotacion = new Date(evento.fechaRotacion);

                                if (isNaN(fechaRotacion.getTime())) {
                                    console.error('Fecha no válida:', evento.fechaRotacion);
                                    return null;
                                }

                                const hoy = new Date();
                                hoy.setHours(0, 0, 0, 0);

                                let color;
                                if (fechaRotacion < hoy) {
                                    color = '#28a745';
                                } else if (fechaRotacion.toDateString() === hoy.toDateString()) {
                                    color = '#ffc107';
                                } else {
                                    color = '#dc3545';
                                }

                                return {
                                    title: `Campo: ${evento.numeroCampo} - ${evento.nombreRotacion}`,
                                    start: fechaRotacion.toISOString().split('T')[0],
                                    allDay: true,
                                    color: color
                                };
                            }).filter(evento => evento !== null);

                            successCallback(eventos);
                        } else {
                            failureCallback();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching eventos:', error);
                        failureCallback();
                    });
            },
            selectable: true
        });

        calendar.render();
    });
</script>

<?php require_once '../footer.php'; ?>