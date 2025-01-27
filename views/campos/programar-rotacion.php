<?php require_once '../header.php'; ?>

<!-- Librerías necesarias de FullCalendar -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js'></script>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-center text-uppercase" style="font-weight: bold; font-size: 32px; color: #000;">Programación de Rotación de Campos</h1>

    <div class="card">
        <div class="card-header bg-success text-black">
            <h3 class="card-title">Calendario</h3>
        </div>
        <div class="card-body">
            <div id="calendar" class="table-responsive"></div>
        </div>
    </div>
</div>

<script src="../../Js/programar-rotacion.js"></script>
<?php require_once '../footer.php'; ?>