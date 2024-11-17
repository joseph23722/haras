document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function (fetchInfo, successCallback, failureCallback) {
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