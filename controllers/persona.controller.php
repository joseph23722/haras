<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../models/Persona.php';
$personal = new Personal();

if (isset($_POST['operation'])) {
    switch ($_POST['operation']) {
        case 'register':
            $datos = [
                "apellidos" => $_POST['apellidos'],
                "nombres" => $_POST['nombres'],
                "nrodocumento" => $_POST['nrodocumento'],
                "direccion" => $_POST['direccion'],
                "tipodoc" => $_POST['tipodoc'],
                "numeroHijos" => $_POST['numeroHijos'],
                "fechaIngreso" => !empty($_POST['fechaIngreso']) ? $_POST['fechaIngreso'] : null,
                "correo" => $_POST['correo'],
                "clave" => password_hash($_POST['clave'], PASSWORD_BCRYPT), // Encriptar la contraseña
                "idRol" => $_POST['idRol']
            ];
            $idobtenido = $personal->registrarPersonaUsuario($datos);
            echo json_encode(["idPersonal" => $idobtenido]);
            break;

        case 'searchByDoc':
            $nrodocumento = $_POST['nrodocumento'];
            $result = $personal->searchByDoc($nrodocumento);
            echo json_encode($result);
            break;
    }
}
