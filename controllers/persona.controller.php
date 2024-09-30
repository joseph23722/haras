<?php


error_reporting(E_ALL);
ini_set('display_errors', 1);



require_once '../models/Persona.php';
$personal = new Personal();

if (isset($_GET['operation'])) {
    switch ($_GET['operation']) {
        case 'searchByDoc':
            $nrodocumento = $_GET['nrodocumento'];
            $result = $personal->searchByDoc($nrodocumento);
            echo json_encode($result);
            break;

        case 'getAll': // Este es el caso nuevo que añade la lista del personal
            $result = $personal->getAll();
            echo json_encode($result);
            break;
    }
}

if (isset($_POST['operation'])) {
    switch ($_POST['operation']) {
        case 'add':
            $datosRecibidos = [
                "nombres" => $personal->limpiarCadena($_POST['nombres']),
                "apellidos" => $personal->limpiarCadena($_POST['apellidos']),
                "direccion" => $personal->limpiarCadena($_POST['direccion']),
                "tipodoc" => $personal->limpiarCadena($_POST['tipodoc']),
                "nrodocumento" => $personal->limpiarCadena($_POST['nrodocumento']),
                "numeroHijos" => (int) $_POST['numeroHijos'],
                "fechaIngreso" => !empty($_POST['fechaIngreso']) ? $_POST['fechaIngreso'] : null
            ];

            // Insertar el personal y obtener el ID generado
            $idPersonal = $personal->add($datosRecibidos);
            echo json_encode(['idPersonal' => $idPersonal]);
            break;
    }
}
