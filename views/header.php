<?php
session_start();

// Si el usuario NO ha iniciado sesión, entonces se va a LOGIN
if (!isset($_SESSION['login']) || $_SESSION['login']['estado'] == false) {
    header('location:http://localhost/haras');
} else {
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
        <!-- Navbar Search-->
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Buscar cliente..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i><?= $_SESSION['login']['nombres'] ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!">Configuración</a></li>
                    <li><a class="dropdown-item" href="#!">Cambiar Contraseña</a></li>
                    <li><a class="dropdown-item" href="#!">Historial</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
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
                    <div class="small">Logged in as:</div>
                    <?= $_SESSION['login']['nombres'] ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">