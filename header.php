<?php
session_start(); // Para todas las vistas

// Para poder ingresar al sistema, debe existir la variable de sesión y esta debe tener acceso permitido.
if (!isset($_SESSION['login']) || (isset($_SESSION['login']) && !$_SESSION['login']['permitido'])) {
    // Redirige a la página de inicio de sesión si no está permitido
    header('Location:index.php');
    exit;
}

$host = "http://localhost/haras";
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
    <link href="<?= $host ?>/plugins/css/adminlte.min.css" rel="stylesheet" />
    <link href="<?= $host ?>/css/styles.css" rel="stylesheet" />
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
                    <li><a class="dropdown-item" href="<?= $host ?>/controllers/usuario.controller.php?operation=destroy">Cerrar Sesión</a></li>
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
                        <a class="nav-link" href="<?= $host ?>/dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>

                        <!-- Servicio Propio -->
                        <a class="nav-link" href="<?= $host ?>/views/servicioPropio/">
                            <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                            Servicio Propio
                        </a>

                        <!-- Servicio Mixto -->
                        <a class="nav-link" href="<?= $host ?>/views/servicioMixto/">
                            <div class="sb-nav-link-icon"><i class="fas fa-exchange-alt"></i></div>
                            Servicio Mixto
                        </a>
                        
                        <!-- Listado de Servicios -->
                        <a class="nav-link" href="<?= $host ?>/views/listadoServicios/">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-list-ol"></i></div>
                            Listado Servicios
                        </a>

                        <!-- Campos -->
                        <a class="nav-link" href="<?= $host ?>/views/rotacionCampos">
                            <div class="sb-nav-link-icon"><i class="fa-solid fa-group-arrows-rotate"></i></div>
                            Rotación Campos
                        </a>

                        <!-- Historial Médico -->
                        <a class="nav-link" href="<?= $host ?>/views/historialMedico/">
                            <div class="sb-nav-link-icon"><i class="fas fa-notes-medical"></i></div>
                            Historial Médico
                        </a>

                        <!-- Administrar medicamentos -->
                        <a class="nav-link" href="<?= $host ?>/views/inventarioMedicamentos/">
                            <div class="sb-nav-link-icon"><i class="fas fa-pills"></i></div>
                            Administración de Medicamentos
                        </a>

                        <!-- Registro Equinos -->
                        <a class="nav-link" href="<?= $host ?>/views/registroEquinos/">
                            <div class="sb-nav-link-icon"><i class="fas fa-clipboard-list"></i></div>
                            Registro Equino
                        </a>
                        <a class="nav-link" href="<?= $host ?>/views/registroEquinos/listadoequinos.php/">
                            <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                            Listado Equinos
                        </a>

                        <!-- Alimentos -->
                        <a class="nav-link" href="<?= $host ?>/views/inventarioAlimentos/">
                            <div class="sb-nav-link-icon"><i class="fas fa-apple-alt"></i></div>
                            Alimento Equino
                        </a>

                        <!-- Usuarios -->
                        <a class="nav-link" href="<?= $host ?>/views/usuarios">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Gestión de Usuarios
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?= $_SESSION['login']['nombres'] ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">