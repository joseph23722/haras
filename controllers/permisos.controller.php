<?php
session_start();
require_once '../models/Permisos.php';  // Incluye el modelo de permisos

$Permisos = new Permisos();  // Instancia del modelo Permisos

// Habilitar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si hay una operación solicitada
if (isset($_GET['operation']) || isset($_POST['operation'])) {
    $operation = $_GET['operation'] ?? $_POST['operation'];

    switch ($operation) {
        case 'listarRoles':
            try {
                $roles = $Permisos->listarRoles();
                echo json_encode($roles);
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
            break;

        case 'listarPermisos':
            try {
                $permisos = $Permisos->listarPermisos();
                echo json_encode($permisos);
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
            break;

        case 'obtenerPermisosRol':
            if (isset($_GET['idRol'])) {
                $idRol = $_GET['idRol'];
                try {
                    $permisosRol = $Permisos->obtenerPermisosRol($idRol);
                    echo json_encode($permisosRol);
                } catch (Exception $e) {
                    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "ID del rol no proporcionado"]);
            }
            break;

        case 'asignarPermisos':
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);

                // Validar datos recibidos
                if (!isset($data['idRol']) || !isset($data['permisos'])) {
                    echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
                    break;
                }

                $idRol = $data['idRol'];
                $permisosSeleccionados = $data['permisos'];  // Array de permisos seleccionados

                try {
                    $Permisos->asignarPermisosRol($idRol, $permisosSeleccionados);
                    echo json_encode(["status" => "success", "message" => "Permisos asignados correctamente."]);
                } catch (Exception $e) {
                    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Método de solicitud no permitido"]);
            }
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Operación no válida"]);
            break;
    }
} else {
    echo json_encode(["status" => "error", "message" => "No se proporcionó ninguna operación."]);
}
