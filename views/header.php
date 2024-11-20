<?php
session_start();

// Si el usuario NO ha iniciado sesión, entonces se va a LOGIN
if (!isset($_SESSION['login']) || $_SESSION['login']['estado'] == false) {
    header('location:http://localhost/haras');
} else {
    // Captura el nombre completo y el correo del usuario desde la sesión de PHP
    $nombreCompleto = $_SESSION['login']['nombres'] . ' ' . $_SESSION['login']['apellidos'];
    $correo = $_SESSION['login']['correo'];

    echo "<script>
        const nombreCompletoUsuario = '{$nombreCompleto}';
        const identificadorUsuario = '{$correo}';
    </script>";
        


    // El usuario a ingresado al sistema, solo se le permitirá acceso a las vistas indicadores por su PERFIL
    $url = $_SERVER['REQUEST_URI'];         // Obtener URL
    $rutaCompleta = explode("/", $url);     // URL > array()
    $rutaCompleta = array_filter($rutaCompleta);
    $totalElementos = count($rutaCompleta);
    // Buscaremos la vistaActual n la listaAcceso
    $vistaActual = $rutaCompleta[$totalElementos];
    $listaAcceso = $_SESSION['login']['accesos'];

    // Verificando permiso
    /* $encontrado = false;
  
  foreach($listaAcceso as $acceso){
    if ($vistaActual == $acceso){
      $encontrado = true;
    }
  } */

    $encontrado = false;
    $i = 0;
    while (($i < count($listaAcceso)) && !$encontrado) {
        if ($listaAcceso[$i]['ruta'] == $vistaActual) {
            $encontrado = true;
        }
        $i++;
    }

    // Validamos si se encontró...
    if (!$encontrado) {
        header("Location: http://localhost/haras/views/home/");
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Panel de administración HARAS" />
    <meta name="author" content="Haras Rancho Sur" />
    <title>HARAS</title>
    <!-- CSS de AdminLTE y DataTables -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="http://localhost/haras/plugins/css/adminlte.min.css" rel="stylesheet" />
    <link href="http://localhost/haras/css/styles.css" rel="stylesheet" />
    <link href="http://localhost/haras/css/registro-medi-ali.css" rel="stylesheet" />
    

    <!-- Notificaciones -->
    <link href="http://localhost/haras/css/notificaciones.css" rel="stylesheet" />

    <!-- Iconos de Font Awesome -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand ps-3" href="<?= $host ?>/dashboard.php">
            <i class="fas fa-hat-cowboy"></i> Haras Rancho Sur
        </a>
        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    

        <!-- Navbar Notifications -->
        <div class="ms-auto me-3 my-2 my-md-0">
            <!-- Botón único para todas las notificaciones -->
            <button class="btn btn-info position-relative" id="btnNotifications" type="button" onclick="mostrarNotificaciones()">
                <i class="fas fa-bell"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount">
                    0 <!-- Contador de notificaciones dinámico -->
                </span>
            </button>
        </div>

        <!-- Contenedor único de Notificaciones -->
        <div id="notificationsContainer" class="dropdown-menu" style="display: none; max-width: 300px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);">
            <!-- Cabecera de Notificaciones -->
            <div class="d-flex justify-content-between align-items-center p-3 custom-header">
                <span style="font-weight: bold; font-size: 18px;">Notificaciones</span>
                <button onclick="marcarComoLeidas()" style="background: none; border: none; color: #0056b3;">Marcar todo como leído</button>
                <button onclick="cerrarNotificaciones()" class="btn-close" aria-label="Close"></button>
            </div>
            <!-- Lista de Notificaciones -->
            <div id="notificationsList" style="max-height: 200px; overflow-y: auto; padding: 0 15px;">
                <!-- Aquí se agregarán las notificaciones dinámicamente -->
            </div>
            <!-- Ver todas las notificaciones -->
            <div class="text-center">
                <button class="btn btn-outline-primary btn-view-all">Ver todas las notificaciones</button>
            </div>
        </div>

        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i><?= $_SESSION['login']['nombres'] ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="http://localhost/haras/controllers/usuario.controller.php?operation=destroy">Cerrar Sesión</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <!-- Inicio -->
                        <div class="sb-sidenav-menu-heading">Inicio</div>
                        <?php
                        foreach ($listaAcceso as $acceso) {
                            if ($acceso['sidebaroption'] == 'S') {
                                echo "
                                    <a class='nav-link' href='http://localhost/haras/views/{$acceso['modulo']}/{$acceso['ruta']}'>
                                    <div class='sb-nav-link-icon'><i class='{$acceso['icono']}'></i></div>
                                    {$acceso['texto']}
                                    </a>
                            ";
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Conectado como:</div>
                    <?= $_SESSION['login']['nombres'] ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">