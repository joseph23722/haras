<!-- Footer -->
<footer class="py-4 mt-auto" style="background: linear-gradient(to right, #a0c4ff, #c9f0ff);">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted" style="font-weight: bold; color: #003366;">
                <i class="fas fa-copyright"></i> 2024 Haras Rancho Sur. Todos los derechos reservados.
            </div>
            <div>
                <a href="#" style="color: #0077b6; text-decoration: none; font-weight: bold;">
                    <i class="fas fa-user-secret"></i> Privacy Policy
                </a>
                &middot;
                <a href="#" style="color: #0077b6; text-decoration: none; font-weight: bold;">
                    <i class="fas fa-file-contract"></i> Terms &amp; Conditions
                </a>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<!-- Carga condicional de jQuery -->
<script id="jqueryScript" src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- SimpleDataTables CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.css">

<!-- SimpleDataTables JS -->
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

<!-- AdminLTE JS -->
<script src="<?= $host ?>/plugins/js/adminlte.min.js"></script>
<!-- Tus propios scripts -->
<script src="<?= $host ?>/plugins/js/scripts.js"></script>

</div> <!-- Cierre de layoutSidenav_content -->
</div> <!-- Cierre de layoutSidenav -->
</body>
<script src="http://localhost/haras/JS/notificaciones.js" defer></script>

<script>
    const sidebarToggle = document.getElementById("sidebarToggle");
    const sidenav = document.getElementById("layoutSidenav_nav");
    sidebarToggle.addEventListener("click", function(event) {
        event.preventDefault();
        sidenav.classList.toggle("sb-sidenav-toggled");
    });
</script>

<!-- Cargar jQuery condicionalmente -->
<script>
    // Condición para verificar si jQuery es necesario
    const necesitaJQuery = false; // Cambia esto dependiendo de si en alguna parte del proyecto necesitas jQuery.

    if (necesitaJQuery) {
        document.getElementById("jqueryScript").setAttribute("defer", ""); // Esto carga jQuery cuando lo necesites
    } else {
        // Si no necesitas jQuery, eliminas el script de jQuery para evitar conflictos
        const jqueryScript = document.getElementById("jqueryScript");
        if (jqueryScript) {
            jqueryScript.remove();
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../swalcustom.js"></script>

</html>